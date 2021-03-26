<?php
/**
 * Add support for Price Based on Country for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
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
function fast_supported_multicurrency_plugins_woocommerce_product_price_based_on_countries( $supported_plugins ) {
	$supported_plugins['woocommerce-product-price-based-on-countries/woocommerce-product-price-based-on-countries.php'] = 'woocommerce_product_price_based_on_countries';

	return $supported_plugins;
}
add_filter( 'fast_supported_multicurrency_plugins', 'fast_supported_multicurrency_plugins_woocommerce_product_price_based_on_countries' );

/**
 * Update the order for multicurrency.
 *
 * @param WC_Data         $order   The order to check.
 * @param WP_REST_Request $request Request object.
 *
 * @return WC_Data
 */
function fast_update_order_for_multicurrency_woocommerce_product_price_based_on_countries( $order, $request ) {

	// Entry point for updating the order for multicurrency using this plugin.

	return $order;
}
add_filter( 'fast_update_order_for_multicurrency_woocommerce_product_price_based_on_countries', 'fast_update_order_for_multicurrency_woocommerce_product_price_based_on_countries', 10, 2 );
