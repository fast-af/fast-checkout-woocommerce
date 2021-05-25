<?php
/**
 * Conditions for determining if/when to hide the Fast Checkout buttons.
 *
 * @package Fast
 */

/**
 * Returns true if the fast-checkout-button should be hidden for any of the following reasons:
 * - Test mode is enabled and current user is someone who should NOT see the button when test mode is on.
 * - The product has addons (not yet supported).
 * - The product is a grouped product (not yet supported).
 * - The product is a subscription product (not yet supported).
 * - The product is an external product (never supported).
 * - The FASTWC_SETTING_APP_ID option is empty.
 */
function fastwc_should_hide_checkout_button() {
	$return = false;

	global $product;

	$product_id = ! empty( $product ) ? $product->ID : 0;

	// Check for test mode and app id set.
	if (
		! empty( $product_id ) &&
		(
			fastwc_is_hidden_for_test_mode() ||
			fastwc_is_app_id_empty() ||
			fastwc_should_hide_pdp_button_for_product() ||
			fastwc_should_hide_for_product_addons( $product_id )
		)
	) {
		$return = true;
	}

	if ( ! $return ) {
		// This variable is set in the following hook:
		// - woocommerce_grouped_product_list_before
		// It is run on the PDP before this function is called.
		global $fastwc_product_is_grouped;

		if ( $fastwc_product_is_grouped ) {
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
				// Don't show if the product is a subscription product (not yet supported by Fast).
				is_a( $product, WC_Product_Subscription::class ) ||
				is_a( $product, WC_Product_Variable_Subscription::class )
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
 * - The FASTWC_SETTING_APP_ID option is empty.
 */
function fastwc_should_hide_cart_checkout_button() {
	// Check for test mode and app id set.
	if (
		fastwc_is_hidden_for_test_mode() ||
		fastwc_is_app_id_empty() ||
		fastwc_should_hide_cart_button_for_product() ||
		fastwc_should_hide_because_unsupported_products() ||
		fastwc_should_hide_because_too_many_coupons()
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
function fastwc_should_hide_because_too_many_coupons() {
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
function fastwc_should_hide_because_unsupported_products() {
	$cart   = WC()->cart;
	$return = false;

	// Check for products we don't support.
	foreach ( $cart->get_cart() as $cart_item ) {
		$hide_for_addons = fastwc_should_hide_for_product_addons( $cart_item['product_id'] );
		if ( $hide_for_addons ) {
			$return = true;
			break;
		}

		// Subscriptions are not yet supported.
		$product = wc_get_product( $cart_item['product_id'] );
		if ( is_a( $product, WC_Product_Subscription::class ) || is_a( $product, WC_Product_Variable_Subscription::class ) ) {
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
 * Get the list of products for which the button should be hidden.
 *
 * @return array
 */
function fastwc_get_products_to_hide_buttons() {
	$fastwc_hidden_products = get_option( FASTWC_SETTING_HIDE_BUTTON_PRODUCTS );

	if ( ! empty( $fastwc_hidden_products ) ) {
		$fastwc_count_products = count( $fastwc_hidden_products );

		for ( $i = 0; $i < $fastwc_count_products; $i++ ) {
			$fastwc_hidden_products[ $i ] = (int) $fastwc_hidden_products[ $i ];
		}
	}

	return $fastwc_hidden_products;
}

/**
 * Determine if the Fast PDP button should be hidden for a specific product.
 *
 * @return bool
 */
function fastwc_should_hide_pdp_button_for_product() {
	$fastwc_hidden_products = fastwc_get_products_to_hide_buttons();

	if ( ! empty( $fastwc_hidden_products ) && is_product() ) {
		// Check current product ID.
		global $product;

		$product_id = ! empty( $product ) ? $product->get_id() : 0;

		if ( ! empty( $product_id ) && in_array( $product_id, $fastwc_hidden_products, true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Determine if the Fast cart button should be hidden for a specific product.
 *
 * @return bool
 */
function fastwc_should_hide_cart_button_for_product() {
	$fastwc_hidden_products = fastwc_get_products_to_hide_buttons();

	if ( ! empty( WC()->cart ) ) {
		$cart = WC()->cart->get_cart();

		foreach ( $cart as $cart_item ) {
			$product_id = ! empty( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;

			if ( ! empty( $product_id ) && in_array( $product_id, $fastwc_hidden_products, true ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Detect if the product has any addons (Fast Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function fastwc_should_hide_for_product_addons( $product_id ) {
	$return = false;

	if ( class_exists( WC_Product_Addons_Helper::class ) ) {
		// If the store has the addons plugin installed, then we can use its static function to see if this product has any
		// addons.
		$addons = WC_Product_Addons_Helper::get_product_addons( $product_id );
		if ( ! empty( $addons ) ) {
			// If this product has any addons (not just the one in the cart, but the product as a whole), hide the button.
			$return = true;
		}
	}

	return $return;
}


/**
 * Detect if the product is a grouped product (Fast Checkout does not yet support these products).
 */
function fastwc_woocommerce_grouped_product_list_before() {
	global $fastwc_product_is_grouped;
	$fastwc_product_is_grouped = true;
}
add_action( 'woocommerce_grouped_product_list_before', 'fastwc_woocommerce_grouped_product_list_before' );
