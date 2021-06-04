<?php
/**
 * Provides an API for creating a new order.
 *
 * @package Fast
 */

/**
 * Create an order from the payload passed from Fast.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_update_order( WP_REST_Request $request ) {
	// 1. Get shipping lines and shipping_options if new order.
	$shipping = fastwc_calculate_shipping( $request );

	// 2. Create/update order. (/wp-json/wc/v3/orders)
	$wc_rest_orders_controller = new WC_REST_Orders_Controller();
	$wc_order                  = $wc_rest_orders_controller->save_object( $request, true );

	// 3. Fetch product details for each product in the order. (/wp-json/wc/v3/products/87368)

	// 4. Return the merged response.

	return WP_REST_Request(
		array(
			'order'    => $wc_order,
			'shipping' => $shipping,
		),
		200
	);
}

/**
 * Fetch an order.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_fetch_order( WP_REST_Request $request ) {

	$order_id = $request['id'];

	if ( empty( $order_id ) ) {
		$response = new WP_Error( 'no_order_id', __( 'No order ID.', 'fast' ) );
	} else {
		$order = wc_get_order( $order_id );

		if ( empty( $order ) ) {
			$response = new WP_Error( 'invalid_order_id', __( 'Invalid order ID.', 'fast' ), $order_id );
		} else {
			$response = new WP_REST_Response( $order->get_data(), 200 );
		}
	}

	return $response;
}
