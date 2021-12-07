<?php
/**
 * Fast third-party plugin class for WooCommerce Dynamic Pricing & Discounts.
 *
 * @see https://codecanyon.net/item/woocommerce-dynamic-pricing-discounts/7119279
 *
 * @package Fast
 */

namespace FastWC\Third_Party;

/**
 * Fast third-party plugin class for WooCommerce Dynamic Pricing & Discounts.
 */
class WC_Dynamic_Pricing_And_Discounts extends Plugin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $slug = 'wc-dynamic-pricing-and-discounts/wc-dynamic-pricing-and-discounts.php';

	/**
	 * Array of cart item subtotals.
	 *
	 * @var array
	 */
	protected $cart_item_subtotals = array();

	/**
	 * Initialize the plugin.
	 */
	protected function init() {
		\add_filter(
			'woocommerce_rest_pre_insert_shop_order_object',
			array( $this, 'woocommerce_rest_pre_insert_shop_order_object' ),
			10,
			2
		);
	}

	/**
	 * Get the setting title.
	 *
	 * @return string
	 */
	protected function get_setting_title() {
		return \__( 'WooCommerce Dynamic Pricing & Discounts', 'fast' );
	}

	/**
	 * Get the setting description.
	 *
	 * @return string
	 */
	protected function get_setting_description() {
		return \__( 'Activate tools to add support for the WooCommerce Dynamic Pricing & Discounts plugin', 'fast' );
	}

	/**
	 * Handle the order object before it is inserted via the REST API.
	 *
	 * @param WC_Data         $order    Object object.
	 * @param WP_REST_Request $request  Request object.
	 *
	 * @return WC_Data
	 */
	function woocommerce_rest_pre_insert_shop_order_object( $order, $request ) {

		// Only do this if the RightPress_Product_Price_Cart class exists.
		if ( ! class_exists( 'RightPress_Product_Price_Cart' ) ) {
			return;
		}

		\fastwc_log_info( 'Order before cart created in WC_Dynamic_Pricing_And_Discounts: ' . print_r( $order, true ) );

		// First, create a cart from the request objet.
		\fastwc_create_cart_from_request( $request );
		\fastwc_log_info( 'Cart created in WC_Dynamic_Pricing_And_Discounts: ' . print_r( \WC()->cart, true ) );

		// Update cart items with pricing rules.
		\RightPress_Product_Price_Cart::get_instance()->cart_loaded_from_session( \WC()->cart );
		\fastwc_log_info( 'Cart maybe updated by RightPress_Product_Price_Cart::cart_loaded_from_Session: ' . print_r( \WC()->cart, true ) );

		if ( ! WC()->cart->is_empty() ) {
			// Update order items from cart items.
			$this->get_cart_item_subtotals();
			$order = $this->update_order_items( $order );
		}

		return $order;
	}

	/**
	 * Get a cart item key.
	 *
	 * @param int   $product_id   The ID of the product.
	 * @param int   $variation_id The ID of the product variation.
	 * @param array $variation    The variation attributes.
	 *
	 * @return string
	 */
	protected function get_cart_item_key( $product_id, $variation_id, $variation ) {
		return json_encode(
			array(
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
				'variation'    => $variation,
			)
		);
	}

	/**
	 * Get subtotals from cart items.
	 */
	protected function get_cart_item_subtotals() {
		$this->cart_item_subtotals = array();

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product  = $cart_item['data'];
			$quantity = $cart_item['quantity'];
			$price    = $product->get_price();

			$cart_item_key = $this->get_cart_item_key(
				$cart_item['product_id'],
				! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0,
				! empty( $cart_item['variation'] ) ? $cart_item['variation'] : array()
			);

			\fastwc_log_info( 'Cart item key: ' . $cart_item_key );

			$this->cart_item_subtotals[ $cart_item_key ] = $price * $quantity;
		}

		\fastwc_log_info( 'Cart Products: ' . print_r( $this->cart_item_subtotals, true ) );
	}

	/**
	 * Update order items.
	 *
	 * @param WC_Data $order Object object.
	 *
	 * @return WC_Data
	 */
	protected function update_order_items( $order ) {

		if ( ! empty( $order->get_items() ) ) {
			foreach ( $order->get_items() as $order_item ) {
				$meta_data = $order_item->get_meta_data();
				$variation = array();

				if ( ! empty( $meta_data ) ) {
					foreach ( $meta_data as $meta_data_item ) {
						$mdi_data  = $meta_data_item->get_data();
						$mdi_key   = ! empty( $mdi_data['key'] ) ? $mdi_data['key'] : '';
						$mdi_value = ! empty( $mdi_data['value'] ) ? $mdi_data['value'] : '';

						if ( 0 === strpos( $mdi_key, 'attribute_' ) ) {
							$variation[ $mdi_key ] = $mdi_value;
						}
					}
				}

				$cart_item_key = $this->get_cart_item_key(
					$order_item->get_product_id(),
					$order_item->get_variation_id(),
					$variation
				);

				\fastwc_log_info( 'Cart item key from order: ' . $cart_item_key );

				if ( isset( $this->cart_item_subtotals[ $cart_item_key ] ) ) {
					$subtotal = $this->cart_item_subtotals[ $cart_item_key ];

					// Set the price.
					$order_item->set_subtotal( $subtotal );
					$order_item->set_total( $subtotal );

					// Make new tax calculations.
					$order_item->calculate_taxes();

					// Save the line item data.
					$order_item->save();
				}
			}
		}

		return $order;
	}
}
