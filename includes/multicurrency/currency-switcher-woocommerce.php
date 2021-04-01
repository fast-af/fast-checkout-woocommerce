<?php
/**
 * Add support for Currency Switcher for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/currency-switcher-woocommerce/
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
function fastwc_update_price_for_multicurrency_currency_switcher_woocommerce( $price, $product, $order, $request ) {

	// Entry point for updating the order for multicurrency using this plugin.

	return $price;
}
add_filter( 'fastwc_update_price_for_multicurrency_currency_switcher_woocommerce', 'fastwc_update_price_for_multicurrency_currency_switcher_woocommerce', 10, 4 );
