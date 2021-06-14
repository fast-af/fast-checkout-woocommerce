<?php
/**
 * Add support for Price Based on Country for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
 *
 * @package fast
 */

/**
 * Check to see if the Price Based on Country plugin is active.
 *
 * @return bool
 */
function fastwc_product_price_based_on_countries_active() {
	return class_exists( 'WC_Product_Price_Based_Country' );
}

/**
 * Update the product price for multicurrency.
 *
 * @param string     $price   Value of the price.
 * @param WC_Product $product The product object.
 * @param WC_Data    $order   The order to check.
 * @param WC_Request $request Request object.
 *
 * @return string
 */
function fastwc_update_price_for_multicurrency_woocommerce_product_price_based_on_countries( $price, $product, $order, $request ) {

	if ( ! fastwc_product_price_based_on_countries_active() ) {
		return $price;
	}

	fastwc_log_debug( 'Price Based on Country multicurrency plugin - Update price' );
	fastwc_log_debug( 'Price Before Conversion: ' . $price );

	$zone = fastwc_woocommerce_product_price_based_on_countries_get_zone( $request );
	fastwc_log_debug( 'Zone: ' . print_r( $zone, true ) ); // phpcs:ignore

	if ( ! empty( $zone ) ) {
		fastwc_log_debug( 'Setting price for zone' );
		$price = $zone->get_post_price( $product->get_id(), '_price' );
	}

	fastwc_log_debug( 'Price After Conversion: ' . $price );

	return $price;
}
add_filter( 'fastwc_update_price_for_multicurrency', 'fastwc_update_price_for_multicurrency_woocommerce_product_price_based_on_countries', 10, 4 );

/**
 * Update the shipping rate for multicurrency.
 *
 * @param array           $rate_info The rate response information.
 * @param string          $currency  The customer currency.
 * @param WP_REST_Request $request   The request object.
 *
 * @return array
 */
function fastwc_update_shipping_rate_for_multicurrency_woocommerce_product_price_based_on_countries( $rate_info, $currency, $request ) {

	if ( ! fastwc_product_price_based_on_countries_active() ) {
		return $rate_info;
	}

	$country = fastwc_woocommerce_product_price_based_on_countries_get_country( $request );

	fastwc_log_debug( 'Price Based on Country multicurrency plugin - Update shipping' );
	fastwc_log_debug( 'Shipping Before Conversion: ' . print_r( $rate_info, true ) ); // phpcs:ignore

	$zone = fastwc_woocommerce_product_price_based_on_countries_get_zone( $request );
	fastwc_log_debug( 'Zone: ' . print_r( $zone, true ) ); // phpcs:ignore

	if ( ! empty( $zone ) ) {
		$rate_info['price'] = $zone->get_exchange_rate_price( $rate_info['price'] );

		if ( ! empty( $rate_info['taxes'] ) ) {
			$rate_taxes = $rate_info['taxes'];

			foreach ( $rate_taxes as $rate_tax_id => $rate_tax ) {
				$rate_info['taxes'][ $rate_tax_id ] = $zone->get_exchange_rate_price( $rate_tax );
			}
		}
	}

	fastwc_log_debug( 'Shipping After Conversion: ' . print_r( $rate_info, true ) ); // phpcs:ignore

	return $rate_info;
}
add_filter( 'fastwc_update_shipping_rate_for_multicurrency', 'fastwc_update_shipping_rate_for_multicurrency_woocommerce_product_price_based_on_countries', 10, 3 );

/**
 * Get the billing address country from the request.
 *
 * @param mixed $request The request object.
 *
 * @return string
 */
function fastwc_woocommerce_product_price_based_on_countries_get_country( $request ) {
	$country = '';

	$valid_based_on = array( 'billing', 'shipping' );
	$based_on       = get_option( 'wc_price_based_country_based_on', 'billing' );

	// Make sure based on is billing or shipping.
	if ( ! in_array( $based_on, $valid_based_on, true ) ) {
		$based_on = 'billing';
	}

	if ( is_array( $request ) ) {
		if ( ! empty( $request[ $based_on ]['country'] ) ) {
			$country = $request[ $based_on ]['country'];
		}
	} elseif ( is_a( $request, 'WP_REST_Request' ) ) {
		$params = $request->get_params();

		if ( ! empty( $params[ $based_on ]['country'] ) ) {
			$country = $params[ $based_on ]['country'];
		}
	}

	return $country;
}

/**
 * Get the pricing zone from the request.
 *
 * @param mixed $request The request object.
 *
 * @return WCPBC_Pricing_Zone|WCPBC_Pricing_Zone_Pro
 */
function fastwc_woocommerce_product_price_based_on_countries_get_zone( $request ) {
	$country = fastwc_woocommerce_product_price_based_on_countries_get_country( $request );

	fastwc_log_debug( 'Country: ' . $country );

	if ( ! empty( $country ) ) {
		$zone = wcpbc_get_zone_by_country( $country );
		fastwc_log_debug( 'Zone by country: ' . print_r( $zone, true ) ); // phpcs:ignore

		return $zone;
	} else {
		$zones          = WCPBC_Pricing_Zones::get_zone_by_country();
		$order_currency = fastwc_get_order_currency( $order );

		// Loop through the zones and get a zone by the currency.
		foreach ( $zones as $_zone ) {
			if ( $order_currency === $_zone->get_currency() ) {
				$zone = $_zone;
				fastwc_log_debug( 'Zone by currency: ' . print_r( $zone, true ) ); // phpcs:ignore

				return $zone;
			}
		}
	}

	return false;
}
