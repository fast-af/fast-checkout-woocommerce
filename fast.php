<?php
/**
 * Plugin Name: Fast Checkout for WooCommerce
 * Plugin URI: https://fast.co
 * Author: Fast
 * Author URI: https://fast.co
 * Description: Install the Checkout button that increases conversion, boosts sales and delights customers.
 * Version: 1.1.15
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package Fast
 */

define( 'FASTWC_PATH', plugin_dir_path( __FILE__ ) );
define( 'FASTWC_URL', plugin_dir_url( __FILE__ ) );
define( 'FASTWC_VERSION', '1.1.15' );

// WooCommerce version utilities.
require_once FASTWC_PATH . 'includes/version.php';

// Check whether the woocommerce plugin is active.
if ( fastwc_woocommerce_is_active() ) {
	// Fast upgrade functions.
	require_once FASTWC_PATH . 'includes/upgrade.php';
	// Fast debug functions.
	require_once FASTWC_PATH . 'includes/debug.php';
	// WP Admin plugin settings.
	require_once FASTWC_PATH . 'includes/admin/settings.php';
	// Loads Fast js and css assets.
	require_once FASTWC_PATH . 'includes/assets.php';
	// Loads fast utilities.
	require_once FASTWC_PATH . 'includes/utilities.php';
	// Adds Fast Checkout button to store.
	require_once FASTWC_PATH . 'includes/checkout.php';
	// Adds Fast Login button to store.
	require_once FASTWC_PATH . 'includes/login.php';
	// Registers routes for the plugin API endpoints.
	require_once FASTWC_PATH . 'includes/routes.php';
	// Add Fast button shortcodes.
	require_once FASTWC_PATH . 'includes/shortcodes.php';
	// Add Fast button widgets.
	require_once FASTWC_PATH . 'includes/widgets.php';
	// Add Fast button blocks.
	require_once FASTWC_PATH . 'includes/blocks.php';
	// Add Fast failed/disabled webhook handler.
	require_once FASTWC_PATH . 'includes/webhooks.php';
	// Add tools to support third-party plugins.
	require_once FASTWC_PATH . 'includes/third-party.php';
	// Add Freemius integration.
	require_once FASTWC_PATH . 'includes/class-freemius.php';
}

define( 'FASTWC_PLUGIN_ACTIVATED', 'fastwc_plugin_activated' );
/**
 * Add a flag indicating that the plugin was just activated.
 */
function fastwc_plugin_activated() {
	// First make sure that WooCommerce is installed and active.
	if ( fastwc_woocommerce_is_active() ) {
		// Add a flag to show that the plugin was activated.
		add_option( FASTWC_PLUGIN_ACTIVATED, true );
	}
}
register_activation_hook( __FILE__, 'fastwc_plugin_activated' );
