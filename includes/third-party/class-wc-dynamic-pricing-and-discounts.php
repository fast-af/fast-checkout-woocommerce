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

		// First, create a cart from the order objet.
		\fastwc_create_cart_from_order( $order );
		\fastwc_log_info( 'Cart created in WC_Dynamic_Pricing_And_Discounts: ' . print_r( \WC()->cart, true ) );

		// Update cart items with pricing rules.
		\RightPress_Product_Price_Cart::get_instance()->cart_loaded_from_session( \WC()->cart );
		\fastwc_log_info( 'Cart maybe updated by RightPress_Product_Price_Cart::cart_loaded_from_Session: ' . print_r( \WC()->cart, true ) );

		if ( ! WC()->cart->is_empty() ) {
			// Update order items from cart items.
			$cart_item_subtotals = array();
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$product  = $cart_item['data'];
				$quantity = $cart_item['quantity'];
				$price    = $product->get_price();

				$cart_item_subtotals[ $cart_item['product_id'] ] = $price * $quantity;
			}

			\fastwc_log_info( 'Cart Products: ' . print_r( $cart_item_subtotals, true ) );

			if ( ! empty( $order->get_items() ) ) {
				foreach ( $order->get_items() as $order_item ) {
					$product_id = $order_item->get_product_id();

					if ( isset( $cart_item_subtotals[ $product_id ] ) ) {
						$subtotal = $cart_item_subtotals[ $product_id ];

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
		}

		return $order;
	}
}
