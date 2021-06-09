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
	protected $route = 'order';

	/**
	 * Route methods.
	 *
	 * @var string
	 */
	protected $methods = 'POST';

	/**
	 * Create an order from the payload passed from Fast.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {
		// 1. Get shipping lines and shipping_options if new order.
		$shipping_route = new Shipping( false ); // Instantiate without registering the route.
		$shipping       = $shipping_route->callback( $request );

		// 2. Create/update order. (/wp-json/wc/v3/orders)
		$wc_rest_orders_controller = new \WC_REST_Orders_Controller();
		$wc_order                  = $wc_rest_orders_controller->create_item( $request );

		// 3. Fetch product details for each product in the order. (/wp-json/wc/v3/products/87368)
		$product_details = array( 'placeholder' );

		// 4. Return the merged response.
		return new \WP_REST_Request(
			array(
				'order'            => $wc_order,
				'shipping_options' => $shipping,
				'product_details'  => $product_details,
			),
			200
		);
	}
}
