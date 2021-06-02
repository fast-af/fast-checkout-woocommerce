<?php
/**
 * Common utility functions for the Fast plugin.
 *
 * @package Fast
 */

/**
 * Load a Fast temlate.
 *
 * @param string $template_name The name of the template to load.
 * @param array  $args          Optional. Args to pass to the template. Requires WP 5.5+.
 *
 * @uses load_template
 */
function fastwc_load_template( $template_name, $args = array() ) {
	$locations = array(
		// Child theme directory.
		get_stylesheet_directory() . '/templates/' . $template_name . '.php',

		// Parent theme directory.
		get_template_directory() . '/templates/' . $template_name . '.php',

		// Plugin directory.
		FASTWC_PATH . 'templates/' . $template_name . '.php',
	);

	// Check each file location and load the first one that exists.
	foreach ( $locations as $location ) {
		if ( file_exists( $location ) ) {
			/**
			 * WordPress load_template function to load the located template.
			 *
			 * @param string $location     Location of the template to load.
			 * @param bool   $require_once Flag to use require_once instead of require.
			 * @param array  $args         Array of args to pass to the tepmlate. Requires WP 5.5+.
			 */
			load_template( $location, false, $args );
			return;
		}
	}
}

/**
 * Get the selected hook/location to render the PDP button.
 *
 * @return string
 */
function fastwc_get_pdp_button_hook() {
	$fastwc_pdp_button_hook = get_option( FASTWC_SETTING_PDP_BUTTON_HOOK, FASTWC_DEFAULT_PDP_BUTTON_HOOK );

	if ( 'other' === $fastwc_pdp_button_hook ) {
		$fastwc_pdp_button_hook = get_option( FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER, FASTWC_DEFAULT_PDP_BUTTON_HOOK );
	}

	return ! empty( $fastwc_pdp_button_hook ) ? $fastwc_pdp_button_hook : FASTWC_DEFAULT_PDP_BUTTON_HOOK;
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
 * Determine if a product is supported.
 *
 * @param int $product_id The product ID to check.
 *
 * @return bool
 */
function fastwc_product_is_supported( $product_id ) {
	/**
	 * Filter to determine if a product is supported by Fast Checkout. Returns true by default.
	 *
	 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
	 * @param int  $product_id   The ID of the product to check.
	 */
	return apply_filters( 'fastwc_product_is_supported', true, $product_id );
}

/**
 * Check if a product is supported based on if it has addons.
 *
 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
 * @param int  $product_id   The ID of the product to check.
 *
 * @return bool
 */
function fastwc_product_is_supported_if_no_addons( $is_supported, $product_id ) {
	if ( fastwc_product_has_addons( $product_id ) ) {
		$is_supported = false;
	}

	return $is_supported;
}
add_filter( 'fastwc_product_is_supported', 'fastwc_product_is_supported_if_no_addons', 10, 2 );

/**
 * Check if a product is supported based on if it is not a grouped product.
 *
 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
 * @param int  $product_id   The ID of the product to check.
 *
 * @return bool
 */
function fastwc_product_is_supported_if_not_grouped( $is_supported, $product_id ) {
	if ( fastwc_product_is_grouped( $product_id ) ) {
		$is_supported = false;
	}

	return $is_supported;
}
add_filter( 'fastwc_product_is_supported', 'fastwc_product_is_supported_if_not_grouped', 10, 2 );

/**
 * Check if a product is supported based on if it is not a subscription product.
 *
 * @param bool $is_supported Flag to pass through the filters to set if the product is supported.
 * @param int  $product_id   The ID of the product to check.
 *
 * @return bool
 */
function fastwc_product_is_supported_if_not_subscription( $is_supported, $product_id ) {
	if ( fastwc_product_is_subscription( $product_id ) ) {
		$is_supported = false;
	}

	return $is_supported;
}
add_filter( 'fastwc_product_is_supported', 'fastwc_product_is_supported_if_not_subscription', 10, 2 );

/**
 * Detect if the product has any addons (Fast Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function fastwc_product_has_addons( $product_id ) {
	$has_addons = false;

	if ( class_exists( WC_Product_Addons_Helper::class ) ) {
		// If the store has the addons plugin installed, then we can use its static function to see if this product has any
		// addons.
		$addons = WC_Product_Addons_Helper::get_product_addons( $product_id );
		if ( ! empty( $addons ) ) {
			// If this product has any addons (not just the one in the cart, but the product as a whole), hide the button.
			$has_addons = true;
		}
	}

	return $has_addons;
}

/**
 * Detect if the product is a grouped product (Fast Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function fastwc_product_is_grouped( $product_id ) {
	$is_grouped = false;

	$product = wc_get_product( $product_id );

	if (
		method_exists( $product, 'get_type' ) &&
		'grouped' === $product->get_type()
	) {
		$is_grouped = true;
	}

	return $is_grouped;
}

/**
 * Detect if the product is a subscription product (Fast Checkout does not yet support these products).
 *
 * @param int $product_id The ID of the product.
 *
 * @return bool
 */
function fastwc_product_is_subscription( $product_id ) {
	$product = wc_get_product( $product_id );

	if (
		is_a( $product, WC_Product_Subscription::class ) ||
		is_a( $product, WC_Product_Variable_Subscription::class )
	) {
		return true;
	}

	return false;
}
