<?php
/**
 * Add support for multicurrency functionality.
 *
 * @package fast
 */

// Add support for Currency Switcher for WooCommerce plugin.
require_once FASTWC_PATH . 'includes/multicurrency/currency-switcher-woocommerce.php';
// Add support for WooCommerce Currency Switcher plugin.
require_once FASTWC_PATH . 'includes/multicurrency/woocommerce-currency-switcher.php';
// Add support for Price Based on Country for WooCommerce plugin.
require_once FASTWC_PATH . 'includes/multicurrency/woocommerce-product-price-based-on-countries.php';

/**
 * Get a list of multicurrency plugins supported by Fast.
 *
 * @return array
 */
function fastwc_get_supported_multicurrency_plugins() {

	// List of built-in supported multicurrency plugins.
	$supported_plugins = array(
		'currency_switcher_woocommerce'                => 'currency-switcher-woocommerce/currency-switcher-woocommerce.php',
		'woocommerce_currency_switcher'                => 'woocommerce-currency-switcher/index.php',
		'woocommerce_product_price_based_on_countries' => 'woocommerce-product-price-based-on-countries/woocommerce-product-price-based-on-countries.php',
	);

	/**
	 * Filter the list of supported multicurrency plugins to add
	 * support for more third-party plugins.
	 *
	 * @param array $supported_plugins The list of supported plugins.
	 */
	return apply_filters( 'fastwc_supported_multicurrency_plugins', $supported_plugins );
}

/**
 * Get the active multicurrency plugin or return false if
 * no supported plugins are active.
 *
 * @return mixed
 */
function fastwc_get_active_multicurrency_plugin() {
	$multicurrency_plugins = fastwc_get_supported_multicurrency_plugins();

	if ( ! empty( $multicurrency_plugins ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Loop through the list of supported plugins and return the first active one.
		// This operates under the assumption that there would only be 1 active
		// multicurrency plugin.
		foreach ( $multicurrency_plugins as $multicurrency_plugin_slug => $multicurrency_plugin ) {
			if ( is_plugin_active( $multicurrency_plugin ) ) {
				return $multicurrency_plugin_slug;
			}
		}
	}

	return false;
}

/**
 * Maybe update order for multicurrency.
 *
 * @param WC_Data         $order    The order to check.
 * @param WP_REST_Request $request  Request object.
 *
 * @return WC_Data
 */
function fastwc_maybe_update_order_for_multicurrency( $order, $request ) {
	$multicurrency_plugin = fastwc_get_active_multicurrency_plugin();

	$wc_currency    = get_woocommerce_currency();
	$order_currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : $wc_currency;

	if (
		false !== $multicurrency_plugin // Make sure a supported multicurrency plugin is activated.
		&& ! empty( $order_currency ) // Make sure the order currency is set.
		&& $wc_currency !== $order_currency // Make sure the order currency is not the default currency.
	) {
		$order = fastwc_update_order_for_multicurrency( $order, $request, $multicurrency_plugin );
	}

	return $order;
}

/**
 * Update the order for multicurrency.
 *
 * @param WC_Data         $order                The order to check.
 * @param WP_REST_Request $request              Request object.
 * @param string          $multicurrency_plugin The name of the multicurrency plugin.
 *
 * @return WC_Data
 */
function fastwc_update_order_for_multicurrency( $order, $request, $multicurrency_plugin ) {

	foreach ( $order->get_items() as $item_id => $item ) {
		$product  = method_exists( $item, 'get_product' ) ? $item->get_product() : null;
		$quantity = method_exists( $item, 'get_quantity' ) ? (int) $item->get_quantity() : 0;

		if ( ! empty( $product ) ) {
			// Get the price from the multicurrency plugin.
			$price = apply_filters(
				"fastwc_update_price_for_multicurrency_{$multicurrency_plugin}",
				$product->get_price(),
				$product,
				$order,
				$request
			);

			// Calculate the price by multiplying by quantity.
			$new_price = $price * $quantity;

			// Set the new price.
			$item->set_subtotal( $new_price );
			$item->set_total( $new_price );

			// Make new tax calculations.
			$item->calculate_taxes();

			// Save the item data.
			$item->save();
		}
	}

	$order->calculate_totals();

	return $order;
}
