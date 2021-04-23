<?php
/**
 * Add support for Price Based on Country for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
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
function fastwc_update_price_for_multicurrency_woocommerce_product_price_based_on_countries( $price, $product, $order, $request ) {

	// Entry point for updating the order for multicurrency using this plugin.

	return $price;
}
add_filter( 'fastwc_update_price_for_multicurrency_woocommerce_product_price_based_on_countries', 'fastwc_update_price_for_multicurrency_woocommerce_product_price_based_on_countries', 10, 2 );

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

	// Entry point for updating the shipping for multicurrency using this plugin.

	return $rate_info;
}
add_filter( 'fastwc_update_shipping_rate_for_multicurrency_woocommerce_product_price_based_on_countries', 'fastwc_update_shipping_rate_for_multicurrency_woocommerce_product_price_based_on_countries', 10, 3 );
