<?php
/**
 * Plugin Name: Fast Checkout for WooCommerce
 * Plugin URI: https://fast.co
 * Description: Install the Checkout button that increases conversion, boosts sales and delights customers.
 * Version: 1.0.1
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package Fast
 */

define( 'FASTWC_PATH', plugin_dir_path( __FILE__ ) );
define( 'FASTWC_URL', plugin_dir_url( __FILE__ ) );

// WP Admin plugin settings.
require_once FASTWC_PATH . 'includes/admin/settings.php';
// Loads Fast js and css assets.
require_once FASTWC_PATH . 'includes/assets.php';
// Loads fast utilities.
require_once FASTWC_PATH . 'includes/utilities.php';
// Add suppot for multicurrency.
require_once FASTWC_PATH . 'includes/multicurrency.php';
// Adds Fast Checkout button to store.
require_once FASTWC_PATH . 'includes/checkout.php';
// Adds Fast Login button to store.
require_once FASTWC_PATH . 'includes/login.php';
// Registers routes for the plugin API endpoints.
require_once FASTWC_PATH . 'includes/routes.php';
// Add Fast button shortcodes.
require_once FASTWC_PATH . 'includes/shortcodes.php';
