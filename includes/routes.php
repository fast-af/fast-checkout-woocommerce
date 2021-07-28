<?php
/**
 * Register routes for the Fast Woocommerce plugin API.
 *
 * @package Fast
 */

// Define the API route base path.
define( 'FASTWC_ROUTES_BASE', 'wc/fast/v1' );

// Provides an API for polling shipping options.
require_once FASTWC_PATH . 'includes/routes/shipping.php';
// Provides an API that exposes shipping zones.
require_once FASTWC_PATH . 'includes/routes/shipping-zones.php';
// Provides an API that exposes plugin info.
require_once FASTWC_PATH . 'includes/routes/plugin-info.php';
// Provides an API that exposes product attributes.
require_once FASTWC_PATH . 'includes/routes/product-attributes.php';
// Provides an API that exposes orders with refunds.
require_once FASTWC_PATH . 'includes/routes/refunds.php';

/**
 * Register Fast Woocommerce routes for the REST API.
 */
function fastwc_rest_api_init() {
	// Register a utility route to get information on installed plugins.
	register_rest_route(
		FASTWC_ROUTES_BASE . '/store',
		'plugins',
		array(
			'methods'             => 'GET',
			'callback'            => 'fastwc_get_plugin_info',
			'permission_callback' => 'fastwc_api_permission_callback',
		)
	);

	fastwc_log_info( 'Registered route: ' . FASTWC_ROUTES_BASE . '/store/plugins' );

	// Register a route to collect all possible shipping locations.
	register_rest_route(
		FASTWC_ROUTES_BASE,
		'shipping_zones',
		array(
			'methods'             => 'GET',
			'callback'            => 'fastwc_get_zones',
			'permission_callback' => 'fastwc_api_permission_callback',
		)
	);

	fastwc_log_info( 'Registered route: ' . FASTWC_ROUTES_BASE . '/shipping_zones' );

	// Register a route to calculate available shipping rates.
	// FE -> OMS -> Blender -> (pID, variantID, Shipping info, CustomerID)Plugin.
	register_rest_route(
		FASTWC_ROUTES_BASE,
		'shipping',
		array(
			'methods'             => 'POST',
			'callback'            => 'fastwc_calculate_shipping',
			'permission_callback' => 'fastwc_api_permission_callback',
		)
	);

	fastwc_log_info( 'Registered route: ' . FASTWC_ROUTES_BASE . '/shipping' );

	// Register a route to load product attributes.
	register_rest_route(
		FASTWC_ROUTES_BASE,
		'product/attributes',
		array(
			'methods'             => 'GET',
			'callback'            => 'fastwc_get_product_attributes',
			'permission_callback' => 'fastwc_api_managewc_permission_callback',
		)
	);

	fastwc_log_info( 'Registered route: ' . FASTWC_ROUTES_BASE . '/shipping' );

	// Register a route to get all orders with refunds.
	register_rest_route(
		FASTWC_ROUTES_BASE,
		'refunds',
		array(
			'methods'             => 'GET',
			'callback'            => 'fastwc_get_orders_with_refunds',
			'permission_callback' => 'fastwc_api_permission_callback',
		)
	);

	fastwc_log_info( 'Registered route: ' . FASTWC_ROUTES_BASE . '/refunds' );

	// Register a route to test the Authorization header.
	register_rest_route(
		FASTWC_ROUTES_BASE,
		'authecho',
		array(
			'methods'             => 'GET',
			'callback'            => 'fastwc_test_authorization_header',
			'permission_callback' => '__return_true',
		)
	);

	fastwc_log_info( 'Registered route: ' . FASTWC_ROUTES_BASE . '/authecho' );
}
add_action( 'rest_api_init', 'fastwc_rest_api_init' );

/**
 * REST API permissions callback.
 *
 * @return bool
 */
function fastwc_api_permission_callback() {
	// Make sure an instance of WooCommerce is loaded.
	// This will load the `WC_REST_Authentication` class, which
	// handles the API consumer key and secret.
	WC();

	$has_permission = current_user_can( 'manage_options' );

	fastwc_log_info( 'API Permission Callback: ' . ( $has_permission ? 'granted' : 'denied' ) );

	return $has_permission;
}

/**
 * REST API permissions callback for product attributes
 *
 * @return bool
 */
function fastwc_api_managewc_permission_callback() {
	// Make sure an instance of WooCommerce is loaded.
	// This will load the `WC_REST_Authentication` class, which
	// handles the API consumer key and secret.
	WC();

	$has_permission = current_user_can( 'manage_woocommerce' );

	fastwc_log_info( 'API Product Attributes Permission Callback: ' . ( $has_permission ? 'granted' : 'denied' ) );

	return $has_permission;
}

/**
 * Test the Authorization header.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_test_authorization_header( $request ) {
	$auth_header = 'No Authorization Header';

	$headers = $request->get_headers();

	if ( ! empty( $headers['authorization'] ) ) {
		$header_count = count( $headers['authorization'] );

		if ( is_array( $headers['authorization'] ) && $header_count > 0 ) {
			$auth_header = $headers['authorization'][0];
		} elseif ( is_string( $headers['authorization'] ) ) {
			$auth_header = $headers['authorization'];
		}
	}

	fastwc_log_info( 'Authorization header endpoint called: ' . $auth_header );

	return new WP_REST_Response( $auth_header, 200 );
}
