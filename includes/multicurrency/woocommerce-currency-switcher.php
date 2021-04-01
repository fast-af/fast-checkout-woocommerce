<?php
/**
 * Add support for WooCommerce Currency Switcher plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-currency-switcher/
 *
 * @package fast
 */

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
	global $WOOCS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

	return $WOOCS->raw_woocommerce_price( $price, $product ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
}
add_filter( 'fastwc_update_price_for_multicurrency_woocommerce_currency_switcher', 'fastwc_update_price_for_multicurrency_woocommerce_currency_switcher', 10, 4 );
