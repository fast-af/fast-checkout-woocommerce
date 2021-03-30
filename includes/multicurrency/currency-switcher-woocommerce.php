<?php
/**
 * Add support for Currency Switcher for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/currency-switcher-woocommerce/
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
function fast_update_order_for_multicurrency_currency_switcher_woocommerce( $order, $request ) {

	// Entry point for updating the order for multicurrency using this plugin.

	return $order;
}
add_filter( 'fast_update_order_for_multicurrency_currency_switcher_woocommerce', 'fast_update_order_for_multicurrency_currency_switcher_woocommerce', 10, 2 );
