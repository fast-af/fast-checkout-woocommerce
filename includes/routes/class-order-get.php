<?php
/**
 * Provides an API for creating a new order.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Order post route object.
 */
class Order_Post extends Base {

	/**
	 * Route name.
	 *
	 * @var string
	 */
	protected $route = 'order/(?P<id>[\d]+)';

	/**
	 * Fetch an order.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {

		$order_id = $request['id'];

		if ( empty( $order_id ) ) {
			$response = new \WP_Error( 'no_order_id', __( 'No order ID.', 'fast' ) );
		} else {
			$order = \wc_get_order( $order_id );

			if ( empty( $order ) ) {
				$response = new \WP_Error( 'invalid_order_id', __( 'Invalid order ID.', 'fast' ), $order_id );
			} else {
				$response = new \WP_REST_Response( $order->get_data(), 200 );
			}
		}

		return $response;
	}
}
