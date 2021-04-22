<?php
/**
 * Provides an API for polling shipping options.
 *
 * @package Fast
 */

/**
 * Given shipping address and product, attempts to calculate available shipping rates for the address.
 * Doesn't support shipping > 1 address.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 * @return array|WP_Error|WP_REST_Response
 * @throws Exception If failed to add items to cart or no shipping options available for address.
 */
function fastwc_calculate_shipping( WP_REST_Request $request ) {
	$params = $request->get_params();
	$return = false;

	$currency = ! empty( $params['currency'] ) ? $params['currency'] : '';

	// This is needed for session to work.
	WC()->frontend_includes();

	fastwc_shipping_init_wc_session();
	fastwc_shipping_init_wc_customer();
	fastwc_shipping_init_wc_cart();
	$return = fastwc_shipping_add_line_items_to_cart( $params );

	if ( false === $return ) {
		$return = fastwc_shipping_update_customer_information( $params );
	}

	if ( false === $return ) {
		$return = fastwc_shipping_calculate_packages( $currency );
	}

	// Cleanup cart.
	WC()->cart->empty_cart();

	return $return;
}

/**
 * Initialize the WC session.
 */
function fastwc_shipping_init_wc_session() {
	if ( null === WC()->session ) {
		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
		WC()->session  = new $session_class();
		WC()->session->init();
	}
}

/**
 * Initialize the WC customer.
 */
function fastwc_shipping_init_wc_customer() {
	if ( null === WC()->customer ) {
		WC()->customer = new WC_Customer( get_current_user_id(), false );
	}
}

/**
 * Initialize the WC cart.
 */
function fastwc_shipping_init_wc_cart() {
	if ( null === WC()->cart ) {
		WC()->cart = new WC_Cart();
		// We need to force a refresh of the cart contents
		// from session here (cart contents are normally
		// refreshed on wp_loaded, which has already happened
		// by this point).
		WC()->cart->get_cart();

		// This cart may contain items from prev session empty before using
		WC()->cart->empty_cart();
	}
}

/**
 * Add line items to cart.
 *
 * @param array $params The request params.
 *
 * @return mixed
 */
function fastwc_shipping_add_line_items_to_cart( $params ) {
	// Add body line items to cart.
	foreach ( $params['line_items'] as $line_item ) {
		$variation_id = ! empty( $line_item['variation_id'] ) ? $line_item['variation_id'] : 0;

		$variation_attribute_values = array();

		// For now hardcode to grab first object as we shouldnt need more.
		if ( ! empty( $line_item['variation_attribute_values'] ) ) {
			// If there are attributes use it when adding item to cart which are required to get shipping options back.
			$variation_attribute_values = $line_item['variation_attribute_values'];
		}

		try {
			WC()->cart->add_to_cart( $line_item['product_id'], $line_item['quantity'], $variation_id, $variation_attribute_values );
		} catch ( \Exception $e ) {
			return WP_Error( 'add_to_cart_error', $e->getMessage(), array( 'status' => 500 ) );
		}
	}

	// Return false to indicate no error.
	return false;
}

/**
 * Update customer information.
 *
 * @param array $params The request params.
 *
 * @return mixed
 */
function fastwc_shipping_update_customer_information( $params ) {
	// Update customer information.
	WC()->customer->set_props(
		array(
			'shipping_country'   => $params['shipping']['country'],
			'shipping_state'     => $params['shipping']['state'],
			'shipping_postcode'  => $params['shipping']['postcode'],
			'shipping_city'      => $params['shipping']['city'],
			'shipping_address_1' => $params['shipping']['address_1'],
			'shipping_address_2' => $params['shipping']['address_2'],
		)
	);
	// Save what we added.
	WC()->customer->save();

	// Calculate shipping.
	WC()->cart->calculate_shipping();
	WC()->cart->calculate_totals();

	// See if we need to calculate anything.
	if ( ! WC()->cart->needs_shipping() ) {
		return new WP_Error( 'shipping_methods_error', 'no shipping methods available for product and address', array( 'status' => 400 ) );
	}

	// Return false for no error.
	return false;
}

/**
 * Calculate packages
 *
 * @param string $currency The customer's currency.
 *
 * @return mixed
 */
function fastwc_shipping_calculate_packages( $currency = '' ) {
	// Get packages for the cart.
	$packages = WC()->cart->get_shipping_packages();

	// Currently we only support 1 shipping address per package.
	if ( count( $packages ) > 1 ) {
		// Perform address check to make sure all are the same.
		$count_packages = count( $packages );
		for ( $x = 1; $x < $count_packages; $x++ ) {
			if ( $packages[0]->destination !== $packages[ $x ]->destination ) {
				return new WP_Error( 'shipping_packages', 'Shipping package to > 1 address is not supported', array( 'status' => 400 ) );
			}
		}
	}

	// Add package ID to array.
	foreach ( $packages as $key => $package ) {
		if ( ! isset( $packages[ $key ]['package_id'] ) ) {
			$packages[ $key ]['package_id'] = $key;
		}
	}
	$calculated_packages = WC()->shipping()->calculate_shipping( $packages );

	$resp = fastwc_get_item_response( $calculated_packages, $currency );

	return new WP_REST_Response( $resp, 200 );
}

/**
 * Build JSON response for line item.
 *
 * @param array  $package  WooCommerce shipping packages.
 * @param string $currency The customer's currency.
 *
 * @return array
 */
function fastwc_get_item_response( $package, $currency ) {
	// Add product names and quantities.
	$items = array();
	foreach ( $package[0]['contents'] as $item_id => $values ) {
		$items[] = array(
			'key'               => $item_id,
			'name'              => $values['data']->get_name(),
			'quantity'          => $values['quantity'],
			'product_id'        => $values['product_id'],
			'variation_id'      => $values['variation_id'],
			'line_subtotal'     => $values['line_subtotal'],
			'line_subtotal_tax' => $values['line_subtotal_tax'],
			'line_total'        => $values['line_total'],
			'line_tax'          => $values['line_tax'],
		);
	}

	return array(
		'package_id'     => $package[0]['package_id'],
		'destination'    =>
			array(
				'address_1' => $package[0]['destination']['address_1'],
				'address_2' => $package[0]['destination']['address_2'],
				'city'      => $package[0]['destination']['city'],
				'state'     => $package[0]['destination']['state'],
				'postcode'  => $package[0]['destination']['postcode'],
				'country'   => $package[0]['destination']['country'],
			),
		'items'          => $items,
		'shipping_rates' => fastwc_prepare_rates_response( $package, $currency ),
	);
}

/**
 * Prepare an array of rates from a package for the response.
 *
 * @param array  $package Shipping package complete with rates from WooCommerce.
 * @param string $currency The cutsomer's currency.
 *
 * @return array
 */
function fastwc_prepare_rates_response( $package, $currency ) {
	$rates = $package [0]['rates'];

	$response = array();

	foreach ( $rates as $rate ) {
		$response[] = fastwc_get_rate_response( $rate, $currency );
	}

	return $response;
}


/**
 * Response for a single rate.
 *
 * @param WC_Shipping_Rate $rate Rate object.
 * @param string $currency The cutsomer's currency.
 *
 * @return array
 */
function fastwc_get_rate_response( $rate, $currency ) {
	$rate_info = array(
		'rate_id'       => fastwc_get_rate_prop( $rate, 'id' ),
		'name'          => fastwc_prepare_html_response( fastwc_get_rate_prop( $rate, 'label' ) ),
		'description'   => fastwc_prepare_html_response( fastwc_get_rate_prop( $rate, 'description' ) ),
		'delivery_time' => fastwc_prepare_html_response( fastwc_get_rate_prop( $rate, 'delivery_time' ) ),
		'price'         => fastwc_get_rate_prop( $rate, 'cost' ),
		'taxes'         => fastwc_get_rate_prop( $rate, 'taxes' ),
		'instance_id'   => fastwc_get_rate_prop( $rate, 'instance_id' ),
		'method_id'     => fastwc_get_rate_prop( $rate, 'method_id' ),
		'meta_data'     => fastwc_get_rate_meta_data( $rate ),
	);

	error_log( print_r( $rate_info, true ) );
	error_log( 'currency: ' . $currency );

	if ( ! empty( $currency ) ) {
		global $fast_currency;

		$wc_currency   = get_woocommerce_currency();
		$fast_currency = $currency;

		add_filter(
			'woocommerce_currency',
			function() {
				global $fast_currency;

				return $fast_currency;
			},
			PHP_INT_MAX
		);

		$rate_info = fastwc_maybe_update_shipping_rate_for_multicurrency( $rate_info, $wc_currency, $currency );
	}

	return array_merge(
		$rate_info,
		fastwc_get_store_currency_response()
	);
}

/**
 * Prepares a list of store currency data to return in responses.
 *
 * @return array
 */
function fastwc_get_store_currency_response() {
	$position = get_option( 'woocommerce_currency_pos' );
	$symbol   = html_entity_decode( get_woocommerce_currency_symbol() );
	$prefix   = '';
	$suffix   = '';

	switch ( $position ) {
		case 'left_space':
			$prefix = $symbol . ' ';
			break;
		case 'left':
			$prefix = $symbol;
			break;
		case 'right_space':
			$suffix = ' ' . $symbol;
			break;
		case 'right':
			$suffix = $symbol;
			break;
		default:
			break;
	}

	return array(
		'currency_code'               => get_woocommerce_currency(),
		'currency_symbol'             => $symbol,
		'currency_minor_unit'         => wc_get_price_decimals(),
		'currency_decimal_separator'  => wc_get_price_decimal_separator(),
		'currency_thousand_separator' => wc_get_price_thousand_separator(),
		'currency_prefix'             => $prefix,
		'currency_suffix'             => $suffix,
	);
}


/**
 * Gets a prop of the rate object, if callable.
 *
 * @param WC_Shipping_Rate $rate Rate object.
 * @param string           $prop Prop name.
 * @return string
 */
function fastwc_get_rate_prop( $rate, $prop ) {
	$getter = 'get_' . $prop;
	return \is_callable( array( $rate, $getter ) ) ? $rate->$getter() : '';
}

/**
 * Converts rate meta data into a suitable response object.
 *
 * @param WC_Shipping_Rate $rate Rate object.
 * @return array
 */
function fastwc_get_rate_meta_data( $rate ) {
	$meta_data = $rate->get_meta_data();

	return array_reduce(
		array_keys( $meta_data ),
		function( $return, $key ) use ( $meta_data ) {
			$return[] = array(
				'key'   => $key,
				'value' => $meta_data[ $key ],
			);
			return $return;
		},
		array()
	);
}

/**
 * Prepares HTML based content, such as post titles and content, for the API response.
 *
 * The wptexturize, convert_chars, and trim functions are also used in the `the_title` filter.
 * The function wp_kses_post removes disallowed HTML tags.
 *
 * @param string|array $response Data to format.
 * @return string|array Formatted data.
 */
function fastwc_prepare_html_response( $response ) {
	if ( is_array( $response ) ) {
		return array_map( 'fastwc_prepare_html_response', $response );
	}
	return is_scalar( $response ) ? wp_kses_post( trim( convert_chars( wptexturize( $response ) ) ) ) : $response;
}

/**
 * Convert monetary values from WooCommerce to string based integers, using
 * the smallest unit of a currency.
 *
 * @param string|float $amount Monetary amount with decimals.
 * @param int          $decimals Number of decimals the amount is formatted with.
 * @param int          $rounding_mode Defaults to the PHP_ROUND_HALF_UP constant.
 * @return string      The new amount.
 */
function prepare_money_response( $amount, $decimals = 2, $rounding_mode = PHP_ROUND_HALF_UP ) {
	return (string) intval(
		round(
			( (float) wc_format_decimal( $amount ) ) * ( 10 ** $decimals ),
			0,
			absint( $rounding_mode )
		)
	);
}
