<?php
/**
 * Register routes for the Fast Woocommerce plugin API.
 *
 * @package Fast
 */

// Define the API route base path.
define( 'FASTWC_ROUTES_BASE', 'wc/fast/v1' );

// Load route base class.
require_once FASTWC_PATH . 'includes/routes/class-route.php';
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
 * REST API permissions callback for product attributes.
 *
 * @return bool
 */
function fastwc_api_managewc_permission_callback() {
	return fastwc_api_general_permission_callback( 'manage_woocommerce', 'API Manage WooCommerce Permission Callback' );
}

/**
 * Log the filter callbacks.
 *
 * @param string $filter The name of the filter.
 */
function fastwc_log_filter_callbacks( $filter ) {
	global $wp_filter;

	$filter_callbacks = ! empty( $wp_filter[ $filter ] ) ? $wp_filter[ $filter ] : array();

	if ( ! empty( $filter_callbacks ) ) {
		fastwc_log_debug( 'Filter callbacks (' . $filter . '):' . print_r( $filter_callbacks, true ) );
	}
}

/**
 * Filter the REST response after dispatch.
 *
 * @param WP_HTTP_Response $result  The result to send to the client. Usually a WP_REST_Response.
 * @param WP_REST_Server   $server  The REST Server.
 * @param WP_REST_Request  $request The request object.
 *
 * @return WP_HTTP_Response
 */
function fastwc_rest_post_dispatch( $result, $server, $request ) {
	$route = $request->get_route();

	if ( '/wc/fast/v1/shippig' === $route ) {
		fastwc_log_filter_callbacks( 'rest_post_dispatch' );
		fastwc_log_debug( 'Result rest_post_dispatch: ' . print_r( $result, true ) );
		fastwc_log_debug( 'Request rest_post_dispatch: ' . print_r( $request, true ) );
	}

	return $result;
}
add_filter( 'rest_post_dispatch', 'fastwc_rest_post_dispatch', 10, 3 );

/**
 * Filter whether the REST API request has already been served.
 *
 * @param bool             $served  Whether the request has been served (default false).
 * @param WP_HTTP_Response $result  Result to send to the client. Usually a WP_REST_Response.
 * @param WP_REST_Request  $request Request used to generate the response.
 * @param WP_REST_Server   $server  Server instance.
 *
 * @return bool
 */
function fastwc_rest_pre_serve_request( $served, $result, $request, $server ) {
	$route = $request->get_route();

	if ( '/wc/fast/v1/shippig' === $route ) {
		fastwc_log_filter_callbacks( 'rest_pre_serve_request' );
		fastwc_log_debug( 'Result rest_pre_serve_request: ' . print_r( $result, true ) );
		fastwc_log_debug( 'Request rest_pre_serve_request: ' . print_r( $request, true ) );
	}

	return $served;
}
add_filter( 'rest_pre_serve_request', 'fastwc_rest_pre_serve_request', 10, 4 );

/**
 * Filter the REST API response.
 *
 * @param array            $result  Response data to send to the client.
 * @param WP_REST_Server   $server  Server instance.
 * @param WP_REST_Request  $request Request used to generate the response.
 *
 * @return array
 */
function fastwc_rest_pre_echo_response( $result, $server, $request ) {
	$route = $request->get_route();

	if ( '/wc/fast/v1/shippig' === $route ) {
		fastwc_log_filter_callbacks( 'rest_pre_echo_response' );
		fastwc_log_debug( 'Result rest_pre_echo_response: ' . print_r( $result, true ) );
		fastwc_log_debug( 'Request rest_pre_echo_response: ' . print_r( $request, true ) );
	}

	return $result;
}
add_filter( 'rest_pre_echo_response', 'fastwc_rest_pre_echo_response', 10, 3 );
