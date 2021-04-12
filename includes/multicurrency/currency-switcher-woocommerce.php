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

	$wc_currency    = get_woocommerce_currency();
	$order_currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : $wc_currency;
	$new_price      = alg_get_product_price_by_currency( $price, $order_currency, $product, true );

	if ( ! empty( $new_price ) ) {
		return $new_price;
	}

	return $price;
}
add_filter( 'fastwc_update_price_for_multicurrency_currency_switcher_woocommerce', 'fastwc_update_price_for_multicurrency_currency_switcher_woocommerce', 10, 4 );

/**
 * Update the shipping rate for multicurrency.
 *
 * @param array  $rate_info The rate response information.
 * @param string $currency  The customer currency.
 *
 * @return array
 */
function fastwc_update_shipping_rate_for_multicurrency_currency_switcher_woocommerce( $rate_info, $currency ) {

	// Entry point for updating the shipping for multicurrency using this plugin.

	return $rate_info;
}
add_filter( 'fastwc_update_shipping_rate_for_multicurrency_currency_switcher_woocommerce', 'fastwc_update_shipping_rate_for_multicurrency_currency_switcher_woocommerce', 10, 4 );
