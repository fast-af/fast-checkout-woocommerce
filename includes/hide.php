<?php
/**
 * Conditions for determining if/when to hide the Fast Checkout buttons.
 *
 * @package Fast
 */

/**
 * Check if we should hide the Fast Checkout PDP button.
 *
 * @param int $product_id Optional. The ID of the product.
 *
 * @return bool
 */
function fastwc_should_hide_pdp_checkout_button( $product_id = 0 ) {
	if ( empty( $product_id ) && is_product() ) {
		global $product;
	} elseif ( ! empty( $product_id ) ) {
		$product = wc_get_product( $product_id );
	}

	// Never show a PDP button if there is no product or if the product is a string.
	if ( empty( $product ) || is_string( $product ) ) {
		return true;
	}

	/**
	 * Filter to set whether or not to hide the Fast Checkout PDP button. Returns false by default.
	 *
	 * @param bool       $should_hide Flag to pass through the filters for whether or not to hide the php checkout button.
	 * @param WC_Product $product     The WooCommerce product object.
	 *
	 * @return bool
	 */
	return apply_filters( 'fastwc_should_hide_pdp_checkout_button', false, $product );
}

/**
 * Check if we should hide the Fast Checkout cart button. Returns false by default.
 *
 * @return bool
 */
function fastwc_should_hide_cart_checkout_button() {
	/**
	 * Filter to set whether or not to hide the Fast Checkout cart button. Returns false by default.
	 *
	 * @param bool $should_hide Flag to pass through the filters for whether or not to hide the cart checkout button.
	 *
	 * @return bool
	 */
	return apply_filters( 'fastwc_should_hide_cart_checkout_button', false );
}

/**
 * Checks if the Fast Checkout button should be hidden for the current user based on the Test Mode field and their email
 * The button should be hidden for all non-Fast users if Test Mode is enabled, and should be visible for everyone if
 * Test Mode is disabled.
 *
 * @param bool $should_hide Flag from filter to hide or not hide the PDP button.
 *
 * @return bool true if we should hide the button, false otherwise
 */
function fastwc_is_hidden_for_test_mode( $should_hide ) {

	if ( ! $should_hide ) {
		// If test mode option is not yet set (e.g. plugin was just installed), treat it as enabled.
		// There is code in the settings page that actually sets this to enabled the first time the user views the form.
		$fastwc_test_mode = get_option( FASTWC_SETTING_TEST_MODE, '1' );
		if ( $fastwc_test_mode ) {
			// In test mode, we only want to show the button if the user is an admin or their email ends with @fast.co.
			$current_user = wp_get_current_user();
			if ( ! preg_match( '/@fast.co$/i', $current_user->user_email ) && ! $current_user->has_cap( 'administrator' ) ) {
				// User is not an admin or a Fast employee. Return early so button never sees the light of day.
				$should_hide = true;
			}
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_pdp_checkout_button', 'fastwc_is_hidden_for_test_mode', 1 );
add_filter( 'fastwc_should_hide_cart_checkout_button', 'fastwc_is_hidden_for_test_mode', 1 );

/**
 * Checks if the store's app ID is empty.
 *
 * @param bool $should_hide Flag from filter to hide or not hide the PDP button.
 *
 * @return bool true if the app ID is empty, false otherwise
 */
function fastwc_is_app_id_empty( $should_hide ) {

	if ( ! $should_hide ) {
		$fastwc_app_id = fastwc_get_app_id();

		if ( empty( $fastwc_app_id ) ) {
			$should_hide = true;
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_pdp_checkout_button', 'fastwc_is_app_id_empty', 1 );
add_filter( 'fastwc_should_hide_cart_checkout_button', 'fastwc_is_app_id_empty', 1 );

/**
 * Determine if the Fast PDP button should be hidden for a specific product.
 *
 * @param bool       $should_hide Flag from filter to hide or not hide the PDP button.
 * @param WC_Product $product     The product to check.
 *
 * @return bool
 */
function fastwc_should_hide_pdp_button_for_product( $should_hide, $product ) {
	if ( ! $should_hide ) {
		$fastwc_hidden_products = fastwc_get_products_to_hide_buttons();

		if ( ! empty( $fastwc_hidden_products ) ) {
			$product_id = ! empty( $product ) ? $product->get_id() : 0;

			if ( ! empty( $product_id ) && in_array( $product_id, $fastwc_hidden_products, true ) ) {
				$should_hide = true;
			}
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_pdp_checkout_button', 'fastwc_should_hide_pdp_button_for_product', 2, 2 );

/**
 * Determine if the Fast PDP button should be hidden for an unsupported product.
 *
 * @param bool       $should_hide Flag from filter to hide or not hide the PDP button.
 * @param WC_Product $product     The product to check.
 *
 * @return bool
 */
function fastwc_should_hide_pdp_button_for_unsupported_product( $should_hide, $product ) {

	if ( ! $should_hide ) {
		$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : 0;

		// If the product is not supported, we should hide the PDP checkout button.
		if ( ! fastwc_product_is_supported( $product_id ) ) {
			$should_hide = true;
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_pdp_checkout_button', 'fastwc_should_hide_pdp_button_for_unsupported_product', 3, 2 );

/**
 * Determine if the Fast PDP button should be hidden for an out-of-stock product.
 * Don't show Fast checkout on PDP if the product is out of stock or on backorder.
 *
 * @param bool       $should_hide Flag from filter to hide or not hide the PDP button.
 * @param WC_Product $product     The product to check.
 *
 * @return bool
 */
function fastwc_should_hide_pdp_button_for_out_of_stock_product( $should_hide, $product ) {

	if ( ! $should_hide ) {
		$stock_status = method_exists( $product, 'get_stock_status' ) ? $product->get_stock_status : '';

		if ( 'outofstock' === $stock_status ) {
			$should_hide = true;
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_pdp_checkout_button', 'fastwc_should_hide_pdp_button_for_out_of_stock_product', 4, 2 );

/**
 * Determine if the Fast PDP button shoudl be hidden for an external product (i.e. not purchased on this store).
 *
 * @param bool       $should_hide Flag from filter to hide or not hide the PDP button.
 * @param WC_Product $product     The product to check.
 *
 * @return bool
 */
function fastwc_should_hide_pdp_button_for_external_product( $should_hide, $product ) {

	if ( ! $should_hide ) {
		if ( is_a( $product, WC_Product_External::class ) ) {
			$should_hide = true;
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_pdp_checkout_button', 'fastwc_should_hide_pdp_button_for_out_of_stock_product', 5, 2 );

/**
 * Determine if the Fast cart button should be hidden for a specific product.
 *
 * @param bool $should_hide Flag from filter to hide or not hide the cart button.
 *
 * @return bool
 */
function fastwc_should_hide_cart_button_for_product( $should_hide ) {

	if ( ! $should_hide ) {
		$fastwc_hidden_products = fastwc_get_products_to_hide_buttons();

		if ( ! empty( WC()->cart ) ) {
			$cart = WC()->cart->get_cart();

			foreach ( $cart as $cart_item ) {
				$product_id = ! empty( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;

				if ( ! empty( $product_id ) && in_array( $product_id, $fastwc_hidden_products, true ) ) {
					$should_hide = true;
					break;
				}
			}
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_cart_checkout_button', 'fastwc_should_hide_cart_button_for_product', 2 );

/**
 * Check cart for products we don't support.
 *
 * @param bool $should_hide Flag from filter to hide or not hide the cart button.
 *
 * @return bool
 */
function fastwc_should_hide_cart_button_because_unsupported_products( $should_hide ) {

	if ( ! $should_hide ) {
		$cart = WC()->cart;

		$cart_items = method_exists( $cart, 'get_cart' ) ? $cart->get_cart() : array();

		if ( empty( $cart_items ) ) {
			$should_hide = true;
		} else {
			// Check for products we don't support.
			foreach ( $cart_items as $cart_item ) {
				if ( ! fastwc_product_is_supported( $cart_item['product_id'] ) ) {
					$should_hide = true;
					break;
				}

				if (
					! empty( $cart_item['wcsatt_data'] ) &&
					! empty( $cart_item['wcsatt_data']['active_subscription_scheme'] )
				) {
					// If the store is using "WooCommerce All Products For Subscriptions" plugin, then this field might be set.
					// If it is anything other than false, then this is a product that has been converted to a subcription; hide the
					// button.
					$should_hide = true;
					break;
				}
			}
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_cart_checkout_button', 'fastwc_should_hide_cart_button_because_unsupported_products', 2 );

/**
 * Check the coupon count.
 *
 * @param bool $should_hide Flag from filter to hide or not hide the cart button.
 *
 * @return bool
 */
function fastwc_should_hide_cart_checkout_button_because_too_many_coupons( $should_hide ) {

	if ( ! $should_hide ) {
		$cart = WC()->cart;

		// Check the coupon count.
		$applied_coupons = $cart->get_applied_coupons();
		if ( count( $applied_coupons ) > 1 ) {
			$should_hide = true;
		}
	}

	return $should_hide;
}
add_filter( 'fastwc_should_hide_cart_checkout_button', 'fastwc_should_hide_cart_checkout_button_because_too_many_coupons', 2 );
