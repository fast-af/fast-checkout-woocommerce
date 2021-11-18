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
	 */
	function woocommerce_rest_pre_insert_shop_order_object( $order, $request ) {

		// First, create a cart from the order objet.
		\fastwc_create_cart_from_order( $order );

		$cart = WC()->cart;
		\fastwc_log_info( 'Cart created in WC_Dynamic_Pricing_And_Discounts: ' . print_r( $cart, true ) );

		// TODO: Update cart items with pricing rules.

		// TODO: Update order items from cart items.
	}
}
