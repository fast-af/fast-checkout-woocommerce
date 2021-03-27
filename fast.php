<?php
/**
 * Plugin Name: Fast Checkout for WooCommerce
 * Plugin URI: https://fast.co
 * Description: Install the Checkout button that increases conversion, boosts sales and delights customers. Fast Checkout enables a hassle-free, secure one-click checkout experience for your customers. Install it in minutes so your customers can check out in seconds. Simply install & configure the Fast Checkout for WooCommerce WordPress plugin to start seeing fewer abandoned carts and more sales for your business. Our top sellers have seen a 20-35% uplift in conversion within one week of installing Fast Checkout. In addition to skyrocketing conversion, Fast delights your customers by letting them skip the headache of creating accounts, remembering passwords and filling out long forms. After making a purchase, they'll get free access to a one-stop dashboard where they can view transactions, track deliveries, and re-order with one-click.
 * Version: 1.0
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package Fast
 */

define( 'FAST_PATH', plugin_dir_path( __FILE__ ) );

// WP Admin plugin settings.
require_once FAST_PATH . 'includes/fast-plugin-settings.php';
// Loads fast.js.
require_once FAST_PATH . 'includes/fast-js.php';
// Loads fast utilities.
require_once FAST_PATH . 'includes/fast-utilities.php';
// Adds Fast Checkout button to store.
require_once FAST_PATH . 'includes/fast-checkout.php';
// Adds Fast Login button to store.
require_once FAST_PATH . 'includes/fast-login.php';
// Registers routes for the plugin API endpoints.
require_once FAST_PATH . 'includes/fast-routes.php';
// Add Fast button shortcodes.
require_once FAST_PATH . 'includes/fast-shortcodes.php';
