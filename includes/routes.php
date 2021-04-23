<?php
/**
 * Register routes for the Fast Woocommerce plugin API.
 *
 * @package Fast
 */

// Define the API route base path.
define( 'FASTWC_ROUTES_BASE', 'wc/fast/v1' );

// Provides an API for polling shipping options.
require_once FASTWC_PATH . 'includes/routes/class-shipping.php';
// Provides an API that exposes shipping zones.
require_once FASTWC_PATH . 'includes/routes/class-shipping-zones.php';
// Provides an API that exposes plugin info.
require_once FASTWC_PATH . 'includes/routes/class-plugin-info.php';

/**
 * Register Fast Woocommerce routes for the REST API.
 */
function fastwc_rest_api_init() {
	// Register a utility route to get information on installed plugins.
	\FastWC\Routes\Plugin_Info::get_instance();

	// Register a route to collect all possible shipping locations.
	\FastWC\Routes\Shipping_Zones::get_instance();

	// Register a route to calculate available shipping rates.
	// FE -> OMS -> Blender -> (pID, variantID, Shipping info, CustomerID)Plugin.
	\FastWC\Routes\Shipping::get_instance();

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

	return current_user_can( 'manage_options' );
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

	return new WP_REST_Response( $auth_header, 200 );
}
