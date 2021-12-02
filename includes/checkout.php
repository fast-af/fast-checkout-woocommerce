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

		fastwc_log_debug( 'Fetched cart data: ' . print_r( $fastwc_cart_data, true ) ); // phpcs:ignore
	}

	return $fastwc_cart_data;
}

/**
 * Maybe render the Fast Checkout button.
 *
 * @param string $button_type The type of button to maybe render.
 * @param string $template    The template to use.
 */
function fastwc_maybe_render_checkout_button( $button_type, $template ) {
	$button_types = array( 'pdp', 'cart' );

	if (
		! in_array( $button_type, $button_types, true ) ||
		( 'pdp' === $button_type && fastwc_should_hide_pdp_checkout_button() ) ||
		( 'cart' === $button_type && fastwc_should_hide_cart_checkout_button() )
	) {
		fastwc_log_info( 'Not rendering checkout button. Type: ' . $button_type . ', Template: ' . $template );
		return;
	}

	fastwc_load_template( $template );
}

/**
 * Maybe render the PDP checkout button.
 */
function fastwc_maybe_render_pdp_button() {
	$current_hook           = current_action();
	$fastwc_pdp_button_hook = fastwc_get_pdp_button_hook();

	if ( $current_hook === $fastwc_pdp_button_hook ) {
		fastwc_maybe_render_checkout_button( 'pdp', 'fast-pdp' );
	}
}

/**
 * Inject Fast Checkout button at the selected hook.
 */
function fastwc_pdp_button_hook_init() {
	$fastwc_pdp_button_hook = fastwc_get_pdp_button_hook();

	add_action( $fastwc_pdp_button_hook, 'fastwc_maybe_render_pdp_button' );
}
add_action( 'init', 'fastwc_pdp_button_hook_init' );

/**
 * Inject Fast Checkout button after Proceed to Checkout button on cart page.
 */
function fastwc_woocommerce_proceed_to_checkout() {
	fastwc_maybe_render_checkout_button( 'cart', 'fast-cart' );
}
add_action( 'woocommerce_proceed_to_checkout', 'fastwc_woocommerce_proceed_to_checkout', 9 );

/**
 * Inject the Fast Checkout button on the mini-cart widget.
 */
function fastwc_woocommerce_widget_shopping_cart_before_buttons() {
	fastwc_maybe_render_checkout_button( 'cart', 'fast-mini-cart' );
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'fastwc_woocommerce_widget_shopping_cart_before_buttons', 30 );

/**
 * Inject the Fast Checkout button on the checkout page.
 */
function fastwc_woocommerce_before_checkout_form() {
	fastwc_maybe_render_checkout_button( 'cart', 'fast-checkout' );
}
add_action( 'woocommerce_before_checkout_form', 'fastwc_woocommerce_before_checkout_form' );

/**
 * Handle the order object before it is inserted via the REST API.
 *
 * @param WC_Data         $order   Object object.
 * @param WP_REST_Request $request Request object.
 *
 * @return WC_Data
 */
function fastwc_woocommerce_rest_pre_insert_shop_order_object( $order, $request ) {

	$order = fastwc_maybe_update_order_for_multicurrency( $order, $request );

	fastwc_log_debug( 'fastwc_woocommerce_rest_pre_insert_shop_order_object ' . print_r( $order, true ) ); // phpcs:ignore

	// For order updates with a coupon line item, make sure there is a cart object.
	if (
		isset( $request['coupon_lines'] ) &&
		is_array( $request['coupon_lines'] )
	) {
		fastwc_log_info( 'Generating cart object on WC orders endpoint.' );

		fastwc_create_cart_from_order( $order );

		fastwc_log_debug( 'Cart generated on pre-insert order hook: ' . print_r( WC()->cart, true ) ); // phpcs:ignore
	}

	// Return the order object unchanged.
	return $order;
}
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'fastwc_woocommerce_rest_pre_insert_shop_order_object', 10, 2 );

/**
 * Generate a cart from the order object.
 *
 * @param WC_Data $order Object object.
 */
function fastwc_create_cart_from_order( $order ) {

	if ( empty( WC()->cart ) ) {
		wc_load_cart();

		// Empty the cart to make sure no lingering products get added previously.
		WC()->cart->empty_cart();

		$items = $order->get_items();

		foreach ( $items as $item ) {
			$product  = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : null;
			$quantity = is_callable( array( $item, 'get_quantity' ) ) ? $item->get_quantity() : 0;

			if ( is_callable( array( $product, 'get_id' ) ) ) {
				$product_id   = $product->get_id();
				$product_type = $product->get_type();
				$variation_id = ( 'variation' === $product_type ) ? $product->get_variation_id() : 0;
				$meta_data    = $product->get_meta_data();

				\fastwc_log_info( 'Product Meta Data: ' . print_r( $meta_data, true ) );

				/**
				 * Add item to cart.
				 * @param int   $product_id contains the id of the product to add to the cart.
				 * @param int   $quantity contains the quantity of the item to add.
				 * @param int   $variation_id ID of the variation being added to the cart.
				 * @param array $variation attribute values.
				 * @param array $cart_item_data extra cart item data we want to pass into the item.
				 */
				WC()->cart->add_to_cart( $product->get_id(), $quantity, $variation_id );

				fastwc_log_debug( 'Product added to cart from order. Product ID: ' . $product->get_id() . ', Quantity: ' . $quantity );
			}
		}
	}
}

/**
 * Fast transition trash to on-hold.
 *
 * @param int      $order_id The order ID.
 * @param WC_Order $order    The order object.
 */
function fastwc_order_status_trash_to_on_hold( $order_id, $order ) {

	fastwc_log_info( 'fastwc_order_status_trash_to_on_hold: ' . $order_id );

	if ( ! empty( $order ) ) {
		$meta_data = $order->get_meta_data();

		foreach ( $meta_data as $data_item ) {
			$data = $data_item->get_data();
			$key  = ! empty( $data['key'] ) ? $data['key'] : '';

			if ( 'fast_order_id' === $key ) {
				$status_change_note = __( 'Fast: Order status changed from pending to on-hold.', 'fast' );
				$order->add_order_note( $status_change_note );

				fastwc_log_info( 'Added status change note: ' . $status_change_note );

				// Init WC_Emails so emails exist.
				$wc_emails = WC_Emails::instance();

				// Trigger the new order email.
				if ( ! empty( $wc_emails->emails['WC_Email_New_Order'] ) ) {
					$wc_emails->emails['WC_Email_New_Order']->trigger( $order_id, $order );

					fastwc_log_info( 'Triggered new order email: ' . $order_id );
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
		fastwc_log_info( 'Clearing cart and redirecting after order created.' );
		WC()->cart->empty_cart();

		$redirect_page = absint( get_option( FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE, 0 ) );
		$redirect_url  = wc_get_cart_url();

		if ( ! empty( $redirect_page ) ) {
			$redirect_page_url = get_permalink( $redirect_page );

			// Only change the redirect URL if the redirect page URL is valid.
			$redirect_url = ! empty( $redirect_page_url ) ? $redirect_page_url : $redirect_url;
		}

		wp_safe_redirect( $redirect_url );
	}
}
add_action( 'init', 'fastwc_maybe_clear_cart_and_redirect' );

/**
 * Maybe hide the regular "Proceed to Checkout" buttons.
 */
function fastwc_maybe_hide_proceed_to_checkout_buttons() {

	// Do nothing in the admin.
	if ( is_admin() ) {
		return;
	}

	$hide_regular_checkout_buttons = get_option( FASTWC_SETTING_HIDE_REGULAR_CHECKOUT_BUTTONS, false );

	if ( $hide_regular_checkout_buttons ) {
		remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
	}
}
add_action( 'init', 'fastwc_maybe_hide_proceed_to_checkout_buttons' );
