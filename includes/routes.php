<?php
/**
 * Register routes for the Fast Woocommerce plugin API.
 *
 * @package Fast
 */

// Define the API route base path.
define( 'FASTWC_ROUTES_BASE', 'wc/fast/v1' );

// Load route base class.
require_once FASTWC_PATH . 'includes/routes/class-base.php';
// Provides an API for polling shipping options.
require_once FASTWC_PATH . 'includes/routes/class-shipping.php';
// Provides an API that exposes shipping zones.
require_once FASTWC_PATH . 'includes/routes/class-shipping-zones.php';
// Provides an API that exposes plugin info.
require_once FASTWC_PATH . 'includes/routes/class-plugin-info.php';
// Provides an API to add, edit, and fetch orders.
require_once FASTWC_PATH . 'includes/routes/class-order-post.php';
require_once FASTWC_PATH . 'includes/routes/class-order-get.php';
// Provides an API that exposes product attributes.
require_once FASTWC_PATH . 'includes/routes/class-product-attributes.php';
// Provides an API that exposes orders with refunds.
require_once FASTWC_PATH . 'includes/routes/class-refunds.php';
// Provides an API that exposes a list of disabled Fast webhooks.
require_once FASTWC_PATH . 'includes/routes/class-webhooks.php';
// Provides an API that exposes a test authorization header.
require_once FASTWC_PATH . 'includes/routes/class-auth-test.php';

/**
 * Register Fast Woocommerce routes for the REST API.
 */
function fastwc_rest_api_init() {
	// Register a utility route to get information on installed plugins.
	new \FastWC\Routes\Plugin_Info();

	// Register a route to collect all possible shipping locations.
	new \FastWC\Routes\Shipping_Zones();

	// Register a route to calculate available shipping rates.
	// FE -> OMS -> Blender -> (pID, variantID, Shipping info, CustomerID)Plugin.
	new \FastWC\Routes\Shipping();

	// Register a route to add/edit an order.
	new \FastWC\Routes\Order_Post();

	// Register a route to fetch an order.
	new \FastWC\Routes\Order_Get();

	// Register a route to load product attributes.
	new \FastWC\Routes\Product_Attributes();

	// Register a route to get all orders with refunds.
	new \FastWC\Routes\Refunds();

	// Register a route to get all disabled Fast webhooks.
	new \FastWC\Routes\Webhooks();

	// Register a route to test the Authorization header.
	new \FastWC\Routes\Auth_Test();
}
add_action( 'rest_api_init', 'fastwc_rest_api_init' );

/**
 * Abstract REST API permissions callback.
 *
 * @param string $capability Capability name to check.
 * @param string $log_string Initial string for the permission check log.
 *
 * @return bool
 */
function fastwc_api_general_permission_callback( $capability, $log_string ) {
	// Make sure an instance of WooCommerce is loaded.
	// This will load the `WC_REST_Authentication` class, which
	// handles the API consumer key and secret.
	WC();

	$has_permission = current_user_can( $capability );

	fastwc_log_info( $log_string . ': ' . ( $has_permission ? 'granted' : 'denied' ) );

	return $has_permission;
}

/**
 * REST API permissions callback.
 *
 * @return bool
 */
function fastwc_api_permission_callback() {
	return fastwc_api_general_permission_callback( 'manage_options', 'API Manage Options Permission Callback' );
}

/**
 * REST API permissions callback for product attributes
 *
 * @return bool
 */
function fastwc_api_managewc_permission_callback() {
	return fastwc_api_general_permission_callback( 'manage_woocommerce', 'API Manage WooCommerce Permission Callback' );
}
