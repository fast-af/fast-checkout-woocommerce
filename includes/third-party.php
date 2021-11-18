<?php
/**
 * Tools to mitigate conflicts with third-party plugins that prevent the Fast Checkout experience from fully working properly.
 *
 * @package Fast
 */

// Load the base third-party plugin class.
require_once FASTWC_PATH . 'includes/third-party/class-plugin.php';
// Load the WooCommerce Tiered Pricing Table class.
require_once FASTWC_PATH . 'includes/third-party/class-woocommerce-tiered-pricing-table.php';
// Load the WooCommerce Dynamic Pricing and Discounts class.
require_once FASTWC_PATH . 'includes/third-party/class-wc-dynamic-pricing-and-discounts.php';

/**
 * Initialize the third-party plugin tools.
 */
function fastwc_initialize_third_party_plugin_support() {
	/**
	 * Filter the list of third-party plugins.
	 *
	 * @param array $fastwc_third_party_plugins The list of third-party plugins.
	 *
	 * @return array
	 */
	$fastwc_third_party_plugins = apply_filters(
		'fastwc_third_party_plugin_classes',
		array(
			'FastWC\Third_Party\WooCommerce_Tiered_Pricing_Table',
			'FastWC\Third_Party\WC_Dynamic_Pricing_And_Discounts',
		)
	);

	foreach ( $fastwc_third_party_plugins as $fastwc_third_party_plugin ) {
		if (
			class_exists( $fastwc_third_party_plugin ) &&
			method_exists( $fastwc_third_party_plugin, 'get_instance' )
		) {
			$fastwc_third_party_plugin::get_instance();
		}
	}
}
fastwc_initialize_third_party_plugin_support();
