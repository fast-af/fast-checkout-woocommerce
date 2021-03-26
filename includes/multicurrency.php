<?php
/**
 * Add support for multicurrency functionality.
 *
 * @package fast
 */

// Add support for Currency Switcher for WooCommerce plugin.
require_once FAST_PATH . 'includes/multicurrency/currency-switcher-woocommerce.php';
// Add support for WooCommerce Currency Switcher plugin.
require_once FAST_PATH . 'includes/multicurrency/woocommerce-currency-switcher.php';
// Add support for Price Based on Country for WooCommerce plugin.
require_once FAST_PATH . 'includes/multicurrency/woocommerce-product-price-based-on-countries.php';

/**
 * Get a list of multicurrency plugins supported by Fast.
 *
 * @return array
 */
function fast_get_supported_multicurrency_plugins() {
	return apply_filters( 'fast_supposted_multicurrency_plugins', array() );
}

/**
 * Get the active multicurrency plugin or return false if
 * no supported plugins are active.
 *
 * @return mixed
 */
function fast_get_active_multicurrency_plugin() {
	$multicurrency_plugins = fast_get_supported_multicurrency_plugins();

	if ( ! empty( $multicurrency_plugins ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Loop through the list of supported plugins and return the first active one.
		// This operates under the assumption that there would only be 1 active
		// multicurrency plugin.
		foreach ( $multicurrency_plugins as $multicurrency_plugin => $multicurrency_plugin_slug ) {
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
 * @param WC_Data         $order   The order to check.
 * @param WP_REST_Request $request Request object.
 *
 * @return WC_Data
 */
function fast_maybe_update_order_for_multicurrency( $order, $request ) {
	$multicurrency_plugin = fast_get_active_multicurrency_plugin();

	if ( false !== $multicurrency_plugin ) {
		$order = apply_filters(
			"fast_update_order_for_multicurrency_{$multicurrency_plugin}",
			$order,
			$request
		);
	}

	return $order;
}
