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
		$wc_order_response         = $wc_rest_orders_controller->create_item( $request );

		if ( ! \is_wp_error( $wc_order_response ) ) {
			$wc_order = $wc_order_response->data;
		} else {
			$wc_order = $wc_order_response;
		}

		// 3. Fetch product details for each product in the order. (/wp-json/wc/v3/products/87368)
		$product_details = array();

		if ( ! \is_wp_error( $wc_order_response ) ) {
			$products = $wc_order['line_items'];

			foreach ( $products as $product ) {
				$product_id = $product['product_id'];
				if ( ! empty( $product['variation_id'] ) && $product_id !== $product['variation_id'] ) {
					$product_id = $product['variation_id'];
				}

				$product_request             = array( 'id' => $product_id );
				$wc_rest_products_controller = new \WC_REST_Products_Controller();
				$product_response            = $wc_rest_products_controller->get_item( $product_request );

				$product_details[ "$product_id" ] = $product_response->data;
			}
		}

		// 4. Return the merged response.
		$response = new \WP_REST_Response(
			array(
				'order'            => $wc_order,
				'shipping_options' => $shipping,
				'product_details'  => $product_details,
			),
			200
		);
		$response = \rest_ensure_response( $response );

		return $response;
	}
}
