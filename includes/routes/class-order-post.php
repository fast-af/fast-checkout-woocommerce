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
		$shipping = $this->get_shipping( $request );

		// 2. Create/update order. (/wp-json/wc/v3/orders)
		$wc_order = $this->create_order( $request );

		if ( \is_wp_error( $wc_order ) ) {
			$response = \rest_ensure_response( $wc_order );
		}

		// 3. Fetch product details for each product in the order. (/wp-json/wc/v3/products/87368)
		if ( empty( $response ) ) {
			$product_details = $this->get_product_details( $request, $wc_order );

			if ( \is_wp_error( $product_details ) ) {
				$response = \rest_ensure_response( $product_details );
			}
		}

		// 4. Return the merged response.
		if ( empty( $response ) ) {
			$response = new \WP_REST_Response(
				array(
					'order'            => $wc_order,
					'shipping_options' => $shipping,
					'product_details'  => $product_details,
				),
				200
			);
			$response = \rest_ensure_response( $response );
		}

		return $response;
	}

	/**
	 * Get shipping options.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	protected function get_shipping( $request ) {
		$shipping_route   = new Shipping( false ); // Instantiate without registering the route.
		$shipping_options = $shipping_route->callback( $request );

		return \is_wp_error( $shipping_options ) ? array() : $shipping_options;
	}

	/**
	 * Create the order.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	protected function create_order( $request ) {
		$wc_rest_orders_controller = new \WC_REST_Orders_Controller();
		$wc_order_response         = $wc_rest_orders_controller->create_item( $request );

		return \is_wp_error( $wc_order_response ) ? $wc_order_response : $wc_order_response->data;
	}

	/**
	 * Fetch product details.
	 *
	 * @param WP_REST_Request           $request JSON request for shipping endpoint.
	 * @param WP_REST_Response|WP_Error $wc_order The order object.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	protected function get_product_details( $request, $wc_order ) {
		$product_details = array();

		$products = $wc_order['line_items'];

		foreach ( $products as $product ) {
			$product_id = $product['product_id'];
			if ( ! empty( $product['variation_id'] ) && $product_id !== $product['variation_id'] ) {
				$product_id = $product['variation_id'];
			}

			$single_product_details = $this->get_single_product_details( $product_id );

			if ( \is_wp_error( $single_product_details ) ) {
				return $single_product_details;
			}

			$product_details[ "$product_id" ] = $single_product_details;
		}

		return $product_details;
	}

	/**
	 * Get details for a single product.
	 *
	 * @param int $product_id The ID of the product to fetch.
	 *
	 * @return array|WP_Error|WP_Rest_Response
	 */
	protected function get_single_product_details( $product_id ) {
		$product_request             = array( 'id' => $product_id );
		$wc_rest_products_controller = new \WC_REST_Products_Controller();
		$product_response            = $wc_rest_products_controller->get_item( $product_request );

		return \is_wp_error( $product_response ) ? $product_response : $product_response->data;
	}
}
