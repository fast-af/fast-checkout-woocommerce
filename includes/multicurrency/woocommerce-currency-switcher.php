<?php
/**
 * Add support for WooCommerce Currency Switcher plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-currency-switcher/
 *
 * @package fast
 */

/**
 * Add to list of supported multicurrency plugins.
 *
 * @param array $supported_plugins The list of supported plugins.
 *
 * @return array
 */
function fast_supported_multicurrency_plugins_woocommerce_currency_switcher( $supported_plugins ) {
	$supported_plugins['woocommerce-currency-switcher/index.php'] = 'woocommerce_currency_switcher';

	return $supported_plugins;
}
add_filter( 'fast_supported_multicurrency_plugins', 'fast_supported_multicurrency_plugins_woocommerce_currency_switcher' );

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
