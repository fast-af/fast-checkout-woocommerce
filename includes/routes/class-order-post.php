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
	 * Shipping options.
	 *
	 * @var array
	 */
	protected $shipping_options = array();

	/**
	 * Initialize the route arguments.
	 */
	protected function init() {
		parent::init();

		add_filter(
			'woocommerce_rest_pre_insert_shop_order_object', // Filter name.
			array( $this, 'set_order_shipping' ), // Callback function.
			9, // Priority.
			2 // Number of variables.
		);
	}

	/**
	 * Create an order from the payload passed from Fast.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {
		// 1. Get shipping lines and shipping_options if new order.
		$this->shipping_options = $this->get_shipping( $request );

		// 2. Create/update order. (/wp-json/wc/v3/orders)
		$order = $this->create_order( $request );

		if ( \is_wp_error( $order ) ) {
			$response = \rest_ensure_response( $order );
		}

		// 3. Fetch product details for each product in the order. (/wp-json/wc/v3/products/87368)
		if ( empty( $response ) ) {
			$product_details = $this->get_product_details( $request, $order );

			if ( \is_wp_error( $product_details ) ) {
				$response = \rest_ensure_response( $product_details );
			}
		}

		// 4. Return the merged response.
		if ( empty( $response ) ) {
			$response = new \WP_REST_Response(
				array(
					'order'            => $order,
					'shipping_options' => $this->shipping_options,
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

		return \is_wp_error( $shipping_options ) ? array() : $shipping_options->data;
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
		$order_response            = $wc_rest_orders_controller->create_item( $request );

		return \is_wp_error( $order_response ) ? $order_response : $order_response->data;
	}

	/**
	 * Set the shipping line item on the order.
	 *
	 * @param WC_Data         $order   Object object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WC_Data
	 */
	public function set_order_shipping( $order, $request ) {
		// Do nothing if there are no shipping options or shipping rates.
		if ( empty( $this->shipping_options ) || empty( $this->shipping_options['shipping_rates'] ) ) {
			return $order;
		}

		$shipping_rates = $this->shipping_options['shipping_rates'];
		$shipping_rate  = $this->get_best_shipping_rate( $shipping_rates );

		if ( ! empty( $shipping_rate ) ) {

			// Clear the existing shipping line items.
			$items = $order->get_items( 'shipping' );

			if ( count( $items ) > 0 ) {
				// Loop through shipping items.
				foreach ( $items as $item_id => $item ) {
					$order->remove_item( $item_id );
				}
				$order->calculate_totals();
			}

			// Set the array for tax calculations.
			$calculate_tax_for = array(
				'country'  => $order->get_shipping_country(),
				'state'    => $order->get_shipping_state(),
				'postcode' => $order->get_shipping_postcode(),
				'city'     => $order->get_shipping_city(),
			);

			// Set a total shipping amount.
			$new_ship_price = floatval( $shipping_rate['price'] );

			// Get a new instance of the WC_Order_Item_Shipping Object.
			$item = new \WC_Order_Item_Shipping();

			$item->set_method_title( $shipping_rate['name'] );
			$item->set_method_id( $shipping_rate['rate_id'] );
			$item->set_total( $new_ship_price );
			$item->calculate_taxes( $calculate_tax_for );

			// Add the new shipping item to the order.
			$order->add_item( $item );

			// Recalculate the totals.
			$order->calculate_totals();
		}

		return $order;
	}

	/**
	 * Get the best rate from the list of shipping rates.
	 *
	 * @param array $shipping_rates The list of shipping rates.
	 *
	 * @return array
	 */
	protected function get_best_shipping_rate( $shipping_rates ) {
		if ( empty( $shipping_rates ) ) {
			return array();
		}

		$best_rate            = PHP_INT_MAX;
		$best_rate_index      = false;
		$shipping_rates_count = count( $shipping_rates );

		if ( 1 === $shipping_rates_count ) {
			$best_rate_index = 0;
		}

		if ( false === $best_rate_index ) {
			foreach ( $shipping_rates as $index => $shipping_rate ) {
				// Do not choose the local pickup rate.
				if ( 'local_pickup' === $shipping_rate['method_id'] ) {
					continue;
				}

				$shipping_rate_price = floatval( $shipping_rate['price'] );

				if ( $shipping_rate_price < $best_rate ) {
					$best_rate       = $shipping_rate_price;
					$best_rate_index = $index;
				}
			}
		}

		return $shipping_rates[ $best_rate_index ];
	}

	/**
	 * Fetch product details.
	 *
	 * @param WP_REST_Request                 $request JSON request for shipping endpoint.
	 * @param array|WP_REST_Response|WP_Error $order   The order object/array.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	protected function get_product_details( $request, $order ) {
		$product_details = array();

		$products = $order['line_items'];

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
