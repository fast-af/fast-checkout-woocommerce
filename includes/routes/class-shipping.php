<?php
/**
 * Provides an API for polling shipping options.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast shipping route object.
 */
class Shipping extends Route {

	/**
	 * Route name.
	 *
	 * @var string
	 */
	protected $route = 'shipping';

	/**
	 * Route methods.
	 *
	 * @var string
	 */
	protected $methods = 'POST';

	/**
	 * Currency code.
	 *
	 * @var string
	 */
	protected $currency = '';

	/**
	 * WooCommerce base currency code.
	 *
	 * @var string
	 */
	protected $wc_currency = '';

	/**
	 * Given shipping address and product, attempts to calculate available shipping rates for the address.
	 * Doesn't support shipping > 1 address.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 *
	 * @throws Exception If failed to add items to cart or no shipping options available for address.
	 */
	public function callback( $request ) {
		$this->request = $request;

		$params = $this->request->get_params();
		$return = false;

		$this->get_currency();

		// This is needed for session to work.
		\WC()->frontend_includes();

		$this->init_wc_session();
		$this->init_wc_customer();
		$this->init_wc_cart();
		$return = $this->add_line_items_to_cart( $params );

		if ( false === $return ) {
			$return = $this->update_customer_information( $params );
		}

		if ( false === $return ) {
			$return = $this->calculate_packages();
		}

		// Cleanup cart.
		\WC()->cart->empty_cart();

		return $return;
	}

	/**
	 * Get the currency from the request object.
	 */
	protected function get_currency() {
		if ( empty( $this->request ) ) {
			return;
		}

		$params = $this->request->get_params();

		// Maybe set the wc_currency parameter.
		if ( empty( $this->wc_currency ) ) {
			$this->wc_currency = \get_woocommerce_currency();
		}

		// Get the order ID from the request params.
		$order_id = ! empty( $params['order_id'] ) ? $params['order_id'] : 0;

		if ( empty( $order_id ) ) {
			$this->currency = ! empty( $params['currency'] ) ? $params['currency'] : $this->wc_currency;
		} else {
			$order          = new \WC_Order( $order_id );
			$this->currency = \fastwc_get_order_currency( $order );
		}

		\add_filter( 'woocommerce_currency', array( $this, 'update_woocommerce_currency' ), PHP_INT_MAX );
	}

	/**
	 * Initialize the WC session.
	 */
	protected function init_wc_session() {
		if ( null === \WC()->session ) {
			$session_class = \apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
			\WC()->session = new $session_class();
			\WC()->session->init();
		}
	}

	/**
	 * Initialize the WC customer.
	 */
	protected function init_wc_customer() {
		if ( null === \WC()->customer ) {
			\WC()->customer = new \WC_Customer( get_current_user_id(), false );
		}
	}

	/**
	 * Initialize the WC cart.
	 */
	protected function init_wc_cart() {
		if ( null === \WC()->cart ) {
			\WC()->cart = new \WC_Cart();
			// We need to force a refresh of the cart contents
			// from session here (cart contents are normally
			// refreshed on wp_loaded, which has already happened
			// by this point).
			\WC()->cart->get_cart();

			// This cart may contain items from prev session empty before using.
			\WC()->cart->empty_cart();
		}
	}

	/**
	 * Add line items to cart.
	 *
	 * @param array $params The request params.
	 *
	 * @return mixed
	 */
	protected function add_line_items_to_cart( $params ) {
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
				\WC()->cart->add_to_cart( $line_item['product_id'], $line_item['quantity'], $variation_id, $variation_attribute_values );
			} catch ( \Exception $e ) {
				return \WP_Error( 'add_to_cart_error', $e->getMessage(), array( 'status' => 500 ) );
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
	protected function update_customer_information( $params ) {
		$shipping_param = ! empty( $params['shipping'] ) ? $params['shipping'] : array();
		$shipping_param = wp_parse_args(
			$shipping_param,
			array(
				'country'   => '',
				'state'     => '',
				'postcode'  => '',
				'city'      => '',
				'address_1' => '',
				'address_2' => '',
			)
		);

		$customer_props = array(
			'shipping_country'   => $shipping_param['country'],
			'shipping_state'     => $shipping_param['state'],
			'shipping_postcode'  => $shipping_param['postcode'],
			'shipping_city'      => $shipping_param['city'],
			'shipping_address_1' => $shipping_param['address_1'],
			'shipping_address_2' => $shipping_param['address_2'],
		);

		// Add billing address info if it is part of the request.
		if ( ! empty( $params['billing'] ) && is_array( $params['billing'] ) ) {
			$customer_props = wp_parse_args(
				$params['billing'],
				$customer_props
			);
		}

		// Update customer information.
		\WC()->customer->set_props( $customer_props );

		// Save what we added.
		\WC()->customer->save();

		// Calculate shipping.
		\WC()->cart->calculate_shipping();
		\WC()->cart->calculate_totals();

		// See if we need to calculate anything.
		if ( ! \WC()->cart->needs_shipping() ) {
			return new \WP_Error( 'shipping_methods_error', 'no shipping methods available for product and address', array( 'status' => 400 ) );
		}

		// Return false for no error.
		return false;
	}

	/**
	 * Calculate packages
	 *
	 * @return mixed
	 */
	protected function calculate_packages() {
		// Get packages for the cart.
		$packages = \WC()->cart->get_shipping_packages();

		// Currently we only support 1 shipping address per package.
		if ( count( $packages ) > 1 ) {
			// Perform address check to make sure all are the same.
			$count_packages = count( $packages );
			for ( $x = 1; $x < $count_packages; $x++ ) {
				if ( $packages[0]->destination !== $packages[ $x ]->destination ) {
					return new \WP_Error( 'shipping_packages', 'Shipping package to > 1 address is not supported', array( 'status' => 400 ) );
				}
			}
		}

		// Add package ID to array.
		foreach ( $packages as $key => $package ) {
			if ( ! isset( $packages[ $key ]['package_id'] ) ) {
				$packages[ $key ]['package_id'] = $key;
			}
		}
		$calculated_packages = \WC()->shipping()->calculate_shipping( $packages );

		$resp = $this->get_item_response( $calculated_packages );

		return new \WP_REST_Response( $resp, 200 );
	}

	/**
	 * Build JSON response for line item.
	 *
	 * @param array $package  WooCommerce shipping packages.
	 *
	 * @return array
	 */
	protected function get_item_response( $package ) {
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
			'shipping_rates' => $this->prepare_rates_response( $package ),
		);
	}

	/**
	 * Prepare an array of rates from a package for the response.
	 *
	 * @param array $package Shipping package complete with rates from WooCommerce.
	 *
	 * @return array
	 */
	protected function prepare_rates_response( $package ) {
		$rates = $package [0]['rates'];

		$response = array();

		foreach ( $rates as $rate ) {
			$response[] = $this->get_rate_response( $rate );
		}

		return $response;
	}


	/**
	 * Response for a single rate.
	 *
	 * @param WC_Shipping_Rate $rate Rate object.
	 *
	 * @return array
	 */
	protected function get_rate_response( $rate ) {
		$rate_info = array(
			'rate_id'       => $this->get_rate_prop( $rate, 'id' ),
			'name'          => $this->prepare_html_response( $this->get_rate_prop( $rate, 'label' ) ),
			'description'   => $this->prepare_html_response( $this->get_rate_prop( $rate, 'description' ) ),
			'delivery_time' => $this->prepare_html_response( $this->get_rate_prop( $rate, 'delivery_time' ) ),
			'price'         => $this->get_rate_prop( $rate, 'cost' ),
			'taxes'         => $this->get_rate_prop( $rate, 'taxes' ),
			'instance_id'   => $this->get_rate_prop( $rate, 'instance_id' ),
			'method_id'     => $this->get_rate_prop( $rate, 'method_id' ),
			'meta_data'     => $this->get_rate_meta_data( $rate ),
		);

		$rate_info = \fastwc_maybe_update_shipping_rate_for_multicurrency( $rate_info, $this->wc_currency, $this->currency, $this->request );

		$rate_info['price'] = \wc_format_decimal( $rate_info['price'], 2 );
		if ( ! empty( $rate_info['taxes'] ) ) {
			$rate_taxes = $rate_info['taxes'];

			foreach ( $rate_taxes as $rate_tax_id => $rate_tax ) {
				$rate_info['taxes'][ $rate_tax_id ] = \wc_format_decimal( $rate_tax, 2 );
			}
		}

		return array_merge(
			$rate_info,
			$this->get_store_currency_response()
		);
	}

	/**
	 * Update WC currency.
	 *
	 * @param string $currency The base currency.
	 *
	 * @return string
	 */
	public function update_woocommerce_currency( $currency ) {
		if ( ! empty( $this->currency ) ) {
			$currnecy = $this->currency;
		}

		return $currency;
	}

	/**
	 * Prepares a list of store currency data to return in responses.
	 *
	 * @return array
	 */
	protected function get_store_currency_response() {
		$position = \get_option( 'woocommerce_currency_pos' );
		$currency = ! empty( $this->currency ) ? $this->currency : \get_woocommerce_currency();
		$symbol   = html_entity_decode( \get_woocommerce_currency_symbol( $currency ) );
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
			'currency_code'               => $currency,
			'currency_symbol'             => $symbol,
			'currency_minor_unit'         => \wc_get_price_decimals(),
			'currency_decimal_separator'  => \wc_get_price_decimal_separator(),
			'currency_thousand_separator' => \wc_get_price_thousand_separator(),
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
	protected function get_rate_prop( $rate, $prop ) {
		$getter = 'get_' . $prop;
		return \is_callable( array( $rate, $getter ) ) ? $rate->$getter() : '';
	}

	/**
	 * Converts rate meta data into a suitable response object.
	 *
	 * @param WC_Shipping_Rate $rate Rate object.
	 * @return array
	 */
	protected function get_rate_meta_data( $rate ) {
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
	 *
	 * @return string|array Formatted data.
	 */
	protected function prepare_html_response( $response ) {
		if ( is_array( $response ) ) {
			return array_map( 'fastwc_prepare_html_response', $response );
		}
		return is_scalar( $response ) ? \wp_kses_post( trim( \convert_chars( \wptexturize( $response ) ) ) ) : $response;
	}
}
