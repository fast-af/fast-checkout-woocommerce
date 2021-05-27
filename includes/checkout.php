<?php
/**
 * Fast Checkout
 *
 * Adds Fast Checkout button to store.
 *
 * @package Fast
 */

// Load helpers to check if/when to hide the Fast Checkout buttons.
require_once FASTWC_PATH . 'includes/hide.php';

/**
 * Returns cart item data that Fast Checkout button can interpret.
 * This function also populates some global variables about cart state, such as whether it contains products we don't support.
 */
function fastwc_get_cart_data() {
	$fastwc_cart_data = array();

	if ( ! empty( WC()->cart ) ) {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			// Populate info about this cart item.
			// Fast backend expects strings for product/variant/quantity so we use strval() here.
			$fastwc_cart_item_data = array(
				'product_id' => strval( $cart_item['product_id'] ),
				'quantity'   => strval( intval( $cart_item['quantity'] ) ), // quantity is a float in wc, casting to int first to be safer.
			);
			if ( ! empty( $cart_item['variation_id'] ) ) {
				// Only track variation_id if it's set.
				$fastwc_cart_item_data['variant_id'] = strval( $cart_item['variation_id'] );
			}
			if ( ! empty( $cart_item['variation'] ) ) {
				// Track the attribute options if they are set.
				foreach ( $cart_item['variation'] as $option_id => $option_value ) {
					$fastwc_cart_item_data['option_values'][] = array(
						'option_id'    => $option_id,
						'option_value' => $option_value,
					);
				}
			}
			$fastwc_cart_data[ $cart_item_key ] = $fastwc_cart_item_data;
		}
	}

	return $fastwc_cart_data;
}

/**
 * Maybe render the cart button.
 *
 * @param string $template The name of the template to render.
 */
function fastwc_maybe_render_cart_button( $template ) {
	if ( fastwc_should_hide_cart_checkout_button() ) {
		return;
	}

	fastwc_load_template( $template );
}

/**
 * Inject Fast Checkout button after Add to Cart button.
 */
function fastwc_woocommerce_after_add_to_cart_quantity() {
	if ( fastwc_should_hide_pdp_checkout_button() ) {
		return;
	}

	fastwc_load_template( 'fast-pdp' );
}
add_action( 'woocommerce_after_add_to_cart_quantity', 'fastwc_woocommerce_after_add_to_cart_quantity' );

/**
 * Inject Fast Checkout button after Proceed to Checkout button on cart page.
 */
function fastwc_woocommerce_proceed_to_checkout() {
	fastwc_maybe_render_cart_button( 'fast-cart' );
}
add_action( 'woocommerce_proceed_to_checkout', 'fastwc_woocommerce_proceed_to_checkout', 9 );

/**
 * Inject the Fast Checkout button on the mini-cart widget.
 */
function fastwc_woocommerce_widget_shopping_cart_before_buttons() {
	fastwc_maybe_render_cart_button( 'fast-mini-cart' );
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'fastwc_woocommerce_widget_shopping_cart_before_buttons', 30 );

/**
 * Inject the Fast Checkout button on the checkout page.
 */
function fastwc_woocommerce_before_checkout_form() {
	fastwc_maybe_render_cart_button( 'fast-checkout' );
}
add_action( 'woocommerce_before_checkout_form', 'fastwc_woocommerce_before_checkout_form' );

/**
 * Handle the order object before it is inserted via the REST API.
 *
 * @param WC_Data         $order    Object object.
 * @param WP_REST_Request $request  Request object.
 */
function fastwc_woocommerce_rest_pre_insert_shop_order_object( $order, $request ) {

	// For order updates with a coupon line item, make sure there is a cart object.
	if (
		empty( WC()->cart ) &&
		isset( $request['coupon_lines'] ) &&
		is_array( $request['coupon_lines'] )
	) {
		wc_load_cart();

		$items = $order->get_items();

		foreach ( $items as $item ) {
			$product  = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : null;
			$quantity = is_callable( array( $item, 'get_quantity' ) ) ? $item->get_quantity() : 0;

			if ( is_callable( array( $product, 'get_id' ) ) ) {
				WC()->cart->add_to_cart( $product->get_id(), $quantity );
			}
		}
	}

	// Return the order object unchanged.
	return $order;
}
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'fastwc_woocommerce_rest_pre_insert_shop_order_object', 10, 3 );

/**
 * Fast transition trash to on-hold.
 *
 * @param int      $order_id The order ID.
 * @param WC_Order $order    The order object.
 */
function fastwc_order_status_trash_to_on_hold( $order_id, $order ) {

	if ( ! empty( $order ) ) {
		$meta_data = $order->get_meta_data();

		foreach ( $meta_data as $data_item ) {
			$data = $data_item->get_data();
			$key  = ! empty( $data['key'] ) ? $data['key'] : '';

			if ( 'fast_order_id' === $key ) {
				$order->add_order_note( __( 'Fast: Order status changed from pending to on-hold.', 'fast' ) );

				// Init WC_Emails so emails exist.
				$wc_emails = WC_Emails::instance();

				// Trigger the new order email.
				if ( ! empty( $wc_emails->emails['WC_Email_New_Order'] ) ) {
					$wc_emails->emails['WC_Email_New_Order']->trigger( $order_id, $order );
				}

				break;
			}
		}
	}
}
add_action( 'woocommerce_order_status_trash_to_on-hold', 'fastwc_order_status_trash_to_on_hold', 10, 2 );

/**
 * Clear the cart of `fast_order_created=1` is added to the URL.
 */
function fastwc_maybe_clear_cart_and_redirect() {
	$fast_order_created = isset( $_GET['fast_order_created'] ) ? absint( $_GET['fast_order_created'] ) : false; // phpcs:ignore

	if (
		1 === $fast_order_created &&
		! empty( WC()->cart ) &&
		is_callable( array( WC()->cart, 'empty_cart' ) )
	) {
		WC()->cart->empty_cart();
		$cart_url = wc_get_cart_url();
		wp_safe_redirect( $cart_url );
	}
}
add_action( 'init', 'fastwc_maybe_clear_cart_and_redirect' );
