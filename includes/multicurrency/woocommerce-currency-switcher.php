<?php
/**
 * Add support for WooCommerce Currency Switcher plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-currency-switcher/
 *
 * @package fast
 */

/**
 * Update the order for multicurrency.
 *
 * @param WC_Data         $order   The order to check.
 * @param WP_REST_Request $request Request object.
 *
 * @return WC_Data
 */
function fast_update_order_for_multicurrency_woocommerce_currency_switcher( $order, $request ) {

	// Entry point for updating the order for multicurrency using this plugin.

	return $order;
}
add_filter( 'fast_update_order_for_multicurrency_woocommerce_currency_switcher', 'fast_update_order_for_multicurrency_woocommerce_currency_switcher', 10, 2 );
