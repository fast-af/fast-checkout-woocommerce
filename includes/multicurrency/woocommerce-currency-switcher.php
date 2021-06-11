<?php
/**
 * Add support for WooCommerce Currency Switcher plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-currency-switcher/
 *
 * @package fast
 */

/**
 * Check to see if the WooCommerce Currency Switcher plugin is active.
 *
 * @return bool
 */
function fastwc_woocommerce_currency_switcher_active() {
	return class_exists( 'WOOCS_STARTER' );
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
function fastwc_update_price_for_multicurrency_woocommerce_currency_switcher( $price, $product, $order, $request ) {

	if ( ! fastwc_woocommerce_currency_switcher_active() ) {
		return $price;
	}

	global $WOOCS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

	$order_currency = fastwc_get_order_currency( $order );

	$_REQUEST['woocs_raw_woocommerce_price_currency'] = $order_currency;

	return $WOOCS->raw_woocommerce_price( $price, $product ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
}
add_filter( 'fastwc_update_price_for_multicurrency', 'fastwc_update_price_for_multicurrency_woocommerce_currency_switcher', 10, 4 );

/**
 * Update the shipping rate for multicurrency.
 *
 * @param array           $rate_info The rate response information.
 * @param string          $currency  The customer currency.
 * @param WP_REST_Request $request   The request object.
 *
 * @return array
 */
function fastwc_update_shipping_rate_for_multicurrency_woocommerce_currency_switcher( $rate_info, $currency, $request ) {

	if ( ! fastwc_woocommerce_currency_switcher_active() ) {
		return $rate_info;
	}

	global $WOOCS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

	$_REQUEST['woocs_raw_woocommerce_price_currency'] = $currency;

	$rate_info['price'] = $WOOCS->raw_woocommerce_price( $rate_info['price'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	if ( ! empty( $rate_info['taxes'] ) ) {
		$rate_taxes = $rate_info['taxes'];

		foreach ( $rate_taxes as $rate_tax_id => $rate_tax ) {
			$rate_info['taxes'][ $rate_tax_id ] = $WOOCS->raw_woocommerce_price( $rate_tax ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		}
	}

	return $rate_info;
}
add_filter( 'fastwc_update_shipping_rate_for_multicurrency', 'fastwc_update_shipping_rate_for_multicurrency_woocommerce_currency_switcher', 10, 3 );
