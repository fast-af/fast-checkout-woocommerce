<?php
/**
 * Fast Checkout
 *
 * Adds Fast Checkout button to store.
 *
 * @package Fast
 */

/**
 * Returns cart item data that Fast Checkout button can interpret.
 * This function also populates some global variables about cart state, such as whether it contains products we don't support.
 */
function fast_get_cart_data() {
	$fast_cart_data = array();

	if ( ! empty( WC()->cart ) ) {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			// Populate info about this cart item.
			// Fast backend expects strings for product/variant/quantity so we use strval() here.
			$fast_cart_item_data = array(
				'product_id' => strval( $cart_item['product_id'] ),
				'quantity'   => strval( intval( $cart_item['quantity'] ) ), // quantity is a float in wc, casting to int first to be safer.
			);
			if ( ! empty( $cart_item['variation_id'] ) ) {
				// Only track variation_id if it's set.
				$fast_cart_item_data['variant_id'] = strval( $cart_item['variation_id'] );
			}
			if ( ! empty( $cart_item['variation'] ) ) {
				// Track the attribute options if they are set.
				foreach ( $cart_item['variation'] as $option_id => $option_value ) {
					$fast_cart_item_data['option_values'][] = array(
						'option_id'    => $option_id,
						'option_value' => $option_value,
					);
				}
			}
			$fast_cart_data[ $cart_item_key ] = $fast_cart_item_data;
		}
	}

	return $fast_cart_data;
}

/**
 * Returns true if the fast-checkout-button should be hidden for any of the following reasons:
 * - Test mode is enabled and current user is someone who should NOT see the button when test mode is on.
 * - The product has addons (not yet supported).
 * - The product is a grouped product (not yet supported).
 * - The product is a subscription product (not yet supported).
 * - The product is an external product (never supported).
 * - The FAST_SETTING_APP_ID option is empty.
 */
function fast_should_hide_checkout_button() {
	$return = false;

	// Check for test mode and app id set.
	if ( fast_is_hidden_for_test_mode() || fast_is_app_id_empty() ) {
		$return = true;
	}

	if ( ! $return ) {
		// These variables are set in the following hooks:
		// - wc_product_addon_start
		// - woocommerce_grouped_product_list_before
		// They are ran on the PDP before this function is called.
		global $fast_product_has_addons, $fast_product_is_grouped;

		if (
			$fast_product_has_addons ||
			$fast_product_is_grouped
		) {
			$return = true;
		}
	}

	if ( ! $return ) {
		// Don't show Fast checkout for the following types of products:
		// - External (i.e. not purchased on this store).
		// - Grouped (multiple products on one PDP; this can be supported later).
		global $product;

		if (
			$product &&
			(
				is_a( $product, WC_Product_External::class ) ||
				is_string( $product ) ||
				// Don't show Fast checkout on PDP if the product is out of stock or on backorder.
				$product->get_stock_status() === 'outofstock' ||
				is_a( $product, WC_Product_Subscription::class )
			)
		) {
			$return = true;
		}
	}

	return $return;
}

/**
 * Returns true if the fast-cart-checkout-button should be hidden for any of the following reasons:
 * - Test mode is enabled and current user is someone who should NOT see the button when test mode is on.
 * - The cart has multiple coupons (not yet supported).
 * - A product in the cart has addons (not yet supported).
 * - A product in the cart is a subscription (not yet supported).
 * - The FAST_SETTING_APP_ID option is empty.
 */
function fast_should_hide_cart_checkout_button() {
	// Check for test mode and app id set.
	if (
		fast_is_hidden_for_test_mode() ||
		fast_is_app_id_empty() ||
		fast_should_hide_because_unsupported_products() ||
		fast_should_hide_because_too_many_coupons()
	) {
		return true;
	}

	return false;
}

/**
 * Check the coupon count.
 *
 * @return bool
 */
function fast_should_hide_because_too_many_coupons() {
	$cart = WC()->cart;

	// Check the coupon count.
	$applied_coupons = $cart->get_applied_coupons();
	if ( count( $applied_coupons ) > 1 ) {
		return true;
	}

	return false;
}

/**
 * Check cart for products we don't support.
 *
 * @return bool
 */
function fast_should_hide_because_unsupported_products() {
	$cart   = WC()->cart;
	$return = false;

	// Check for products we don't support.
	foreach ( $cart->get_cart() as $cart_item ) {
		// Addons are not yet supported.
		if ( class_exists( WC_Product_Addons_Helper::class ) ) {
			// If the store has the addons plugin installed, then we can use its static function to see if this product has any
			// addons.
			$addons = WC_Product_Addons_Helper::get_product_addons( $cart_item['product_id'] );
			if ( ! empty( $addons ) ) {
				// If this product has any addons (not just the one in the cart, but the product as a whole), hide the button.
				$return = true;
				break;
			}
		}

		// Subscriptions are not yet supported.
		if ( is_a( wc_get_product( $cart_item['product_id'] ), WC_Product_Subscription::class ) ) {
			$return = true;
			break;
		}
		if ( ! empty( $cart_item['wcsatt_data'] ) && ! empty( $cart_item['wcsatt_data']['active_subscription_scheme'] ) ) {
			// If the store is using "WooCommerce All Products For Subscriptions" plugin, then this field might be set.
			// If it is anything other than false, then this is a product that has been converted to a subcription; hide the
			// button.
			$return = true;
			break;
		}
	}

	return $return;
}

/**
 * Product detail page
 */

/**
 * Detect if the product has any addons (Fast Checkout does not yet support these products).
 */
function fast_wc_product_addon_start() {
	// If the store has the addons plugin installed, then this hook will run on any PDP pages for products with addons.
	// In this situation, we want to note this so that our hook that displays the button can instead hide the button.
	global $fast_product_has_addons;
	$fast_product_has_addons = true;
}
add_action( 'wc_product_addon_start', 'fast_wc_product_addon_start' );

/**
 * Detect if the product is a grouped product (Fast Checkout does not yet support these products).
 */
function fast_woocommerce_grouped_product_list_before() {
	global $fast_product_is_grouped;
	$fast_product_is_grouped = true;
}
add_action( 'woocommerce_grouped_product_list_before', 'fast_woocommerce_grouped_product_list_before' );

/**
 * Inject Fast Checkout button after Add to Cart button.
 */
function fast_woocommerce_after_add_to_cart_quantity() {
	if ( fast_should_hide_checkout_button() ) {
		return;
	}

	fast_load_template( 'fast-pdp' );
}
add_action( 'woocommerce_after_add_to_cart_quantity', 'fast_woocommerce_after_add_to_cart_quantity' );

/**
 * Cart page
 */

/**
 * Inject Fast Checkout button after Proceed to Checkout button on cart page.
 */
function fast_woocommerce_proceed_to_checkout() {
	if ( fast_should_hide_cart_checkout_button() ) {
		return;
	}

	fast_load_template( 'fast-cart' );
}
add_action( 'woocommerce_proceed_to_checkout', 'fast_woocommerce_proceed_to_checkout', 9 );

/**
 * Mini-cart widget
 */
function fast_woocommerce_widget_shopping_cart_before_buttons() {
	if ( fast_should_hide_cart_checkout_button() ) {
		return;
	}

	fast_load_template( 'fast-mini-cart' );
}
add_action( 'woocommerce_widget_shopping_cart_before_buttons', 'fast_woocommerce_widget_shopping_cart_before_buttons', 30 );

/**
 * Checkout page
 */
function fast_woocommerce_before_checkout_form() {
	if ( fast_should_hide_cart_checkout_button() ) {
		return;
	}

	fast_load_template( 'fast-checkout' );
}
add_action( 'woocommerce_before_checkout_form', 'fast_woocommerce_before_checkout_form' );

/**
 * Handle the order object before it is inserted via the REST API.
 *
 * @param WC_Data         $order   Object object.
 * @param WP_REST_Request $request Request object.
 *
 * @return WC_Data
 */
function fast_woocommerce_rest_pre_insert_shop_order_object( $order, $request ) {

	$order = fast_maybe_update_order_for_multicurrency( $order, $request );

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
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'fast_woocommerce_rest_pre_insert_shop_order_object', 10, 3 );

/**
 * Fast transition trash to on-hold.
 *
 * @param int      $order_id The order ID.
 * @param WC_Order $order    The order object.
 */
function fast_order_status_trash_to_on_hold( $order_id, $order ) {

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
add_action( 'woocommerce_order_status_trash_to_on-hold', 'fast_order_status_trash_to_on_hold', 10, 2 );

/**
 * Clear the cart of `fast_order_created=1` is added to the URL.
 */
function fast_maybe_clear_cart_and_redirect() {
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
add_action( 'init', 'fast_maybe_clear_cart_and_redirect' );
