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

	fastwc_log_debug( 'Request object: ' . print_r( $request, true ) );

	fastwc_log_debug( 'fastwc_woocommerce_rest_pre_insert_shop_order_object ' . print_r( $order, true ) ); // phpcs:ignore

	// Remove coupon lines from request if the coupon has already been applied to the order.
	fastwc_check_request_coupon_lines( $request, $order );

	fastwc_log_debug( 'Request object after coupon lines check: ' . print_r( $request, true ) ); // phpcs:ignore

	// For order updates with a coupon line item, make sure there is a cart object.
	if (
		isset( $request['coupon_lines'] ) &&
		is_array( $request['coupon_lines'] )
	) {
		fastwc_log_info( 'Generating cart object on WC orders endpoint.' );

		fastwc_create_cart_from_request( $request );

		fastwc_log_debug( 'Cart generated on pre-insert order hook: ' . print_r( WC()->cart, true ) ); // phpcs:ignore
	}

	// Return the order object unchanged.
	return $order;
}
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'fastwc_woocommerce_rest_pre_insert_shop_order_object', 10, 2 );

/**
 * Generate a cart from the order object.
 *
 * @param WP_REST_Request $request Request object.
 */
function fastwc_create_cart_from_request( $request ) {

	if ( empty( WC()->cart ) ) {
		wc_load_cart();

		$request_line_items = isset( $request['line_items'] ) ? $request['line_items'] : array();
		fastwc_log_debug( 'Request line items: ' . print_r( $request_line_items, true ) ); // phpcs:ignore

		// Empty the cart to make sure no lingering products get added previously.
		WC()->cart->empty_cart();

		foreach ( $request_line_items as $item ) {
			fastwc_log_debug( 'Request line item: ' . print_r( $item, true ) ); // phpcs:ignore

			// Skip items with 0 quantity.
			if ( empty( $item['quantity'] ) || empty( $item['product_id'] ) ) {
				continue;
			}

			$product_id   = ! empty( $item['product_id'] ) ? $item['product_id'] : 0;
			$variation_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : 0;
			$quantity     = $item['quantity'];
			$variation    = array();

			if ( ! empty( $item['meta_data'] ) ) {
				foreach ( $item['meta_data'] as $item_attribute ) {
					if ( ! empty( $item_attribute['key'] ) && ! empty( $item_attribute['value'] ) ) {
						$variation[ $item_attribute['key'] ] = $item_attribute['value'];
					}
				}
			}

			// Add the product to the cart.
			WC()->cart->add_to_cart(
				$product_id,
				$quantity,
				$variation_id,
				$variation
			);

			fastwc_log_debug(
				sprintf(
					'Product added to cart from order. Product ID: %1$s, Quantity: %2$s, Variation ID: %3$s, Variation Attributes: %4$s',
					$product_id,
					$quantity,
					$variation_id,
					print_r( $variation, true ) // phpcs:ignore
				)
			);
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

/**
 * Check coupon codes from the request object to avoid applying the same coupon code more than once.
 *
 * @param WP_REST_Request $request Request object.
 * @param WC_Data         $order   Object object.
 */
function fastwc_check_request_coupon_lines( $request, $order ) {
	// Do nothing if there are no coupons in the request.
	if ( empty( $request['coupon_lines'] ) || count( $request['coupon_lines'] ) > 1 ) {
		return;
	}

	$discounts = new WC_Discounts( $order );

	$current_order_coupons = array_values( $order->get_coupons() );
	if ( empty( $current_order_coupons ) ) {
		return;
	}

	$current_order_coupon_codes = array_map(
		function( $coupon ) {
			return $coupon->get_code();
		},
		$current_order_coupons
	);

	$new_coupon_lines = array();
	foreach ( $request['coupon_lines'] as $coupon_line ) {
		if ( empty( $coupon_line['code'] ) ) {
			continue;
		}

		$coupon_code = wc_format_coupon_code( $coupon_line['code'] );

		if ( ! in_array( $coupon_code, $current_order_coupon_codes, true ) ) {
			$new_coupon_lines[] = $coupon_line;
		}
	}

	if ( empty( $new_coupon_lines ) ) {
		unset( $request['coupon_lines'] );
		$order->recalculate_coupons();
	} else {
		$request['coupon_lines'] = $new_coupon_lines;
	}
}
