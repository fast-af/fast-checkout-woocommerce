<?php
/**
 * Plugin Name: Fast
 * Plugin URI: https://fast.co
 * Description: One-click login and checkout.
 * Version: 1.0
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
