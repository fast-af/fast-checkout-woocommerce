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
			 * Action hook to trigger before loading the template.
			 *
			 * @param array $args Array of args that get passed to the template.
			 */
			do_action( "fastwc_before_load_template_{$template_name}", $args );

			/**
			 * WordPress load_template function to load the located template.
			 *
			 * @param string $location     Location of the template to load.
			 * @param bool   $require_once Flag to use require_once instead of require.
			 * @param array  $args         Array of args to pass to the tepmlate. Requires WP 5.5+.
			 */
			load_template( $location, false, $args );

			/**
			 * Action hook to trigger after loading the template.
			 *
			 * @param array $args Array of args that get passed to the template.
			 */
			do_action( "fastwc_after_load_template_{$template_name}", $args );

			fastwc_log_info( 'Loaded template: ' . $location );
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

	$fastwc_pdp_button_hook = ! empty( $fastwc_pdp_button_hook ) ? $fastwc_pdp_button_hook : FASTWC_DEFAULT_PDP_BUTTON_HOOK;

	/**
	 * Filter to overrie the Fast PDP button hook.
	 *
	 * @param string $fastwc_pdp_button_hook The selected PDP button hook.
	 *
	 * @return string
	 */
	return apply_filters( 'fastwc_pdp_button_hook', $fastwc_pdp_button_hook );
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

		fastwc_log_info( 'Products fetched to hide buttons: ' . print_r( $fastwc_hidden_products, true ) ); // phpcs:ignore
	}

	/**
	 * Filter to override the list of products for which the button should be hidden.
	 *
	 * @param array $fastwc_hidden_products The list of products for which the button should be hidden.
	 *
	 * @return array
	 */
	return apply_filters( 'fastwc_hidden_products', $fastwc_hidden_products );
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
	$is_supported = apply_filters( 'fastwc_product_is_supported', true, $product_id );

	fastwc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported: ' . $product_id );

	return $is_supported;
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

	fastwc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported after addon check: ' . $product_id );

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

	fastwc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported after grouped check: ' . $product_id );

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

	fastwc_log_info( 'Product is' . ( $is_supported ? '' : ' not' ) . ' supported after subscription check: ' . $product_id );

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

	fastwc_log_info( 'Product does' . ( $has_addons ? '' : ' not' ) . ' have addons: ' . $product_id );

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

	fastwc_log_info( 'Product is' . ( $is_grouped ? '' : ' not' ) . ' grouped: ' . $product_id );

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

	$is_subscription = false;

	if (
		is_a( $product, WC_Product_Subscription::class ) ||
		is_a( $product, WC_Product_Variable_Subscription::class )
	) {
		$is_subscription = true;
	}

	fastwc_log_info( 'Product is' . ( $is_subscription ? '' : ' not' ) . ' a subscription: ' . $product_id );

	return false;
}

/**
 * Get Fast button styles.
 *
 * @param mixed|string|array $button_type Type of styles to get. Default to empty string for all.
 *
 * @return string
 */
function fastwc_get_button_styles( $button_type = '' ) {
	$types = array(
		'pdp'       => FASTWC_SETTING_PDP_BUTTON_STYLES,
		'mini_cart' => FASTWC_SETTING_MINI_CART_BUTTON_STYLES,
		'login'     => FASTWC_SETTING_LOGIN_BUTTON_STYLES,
		'checkout'  => FASTWC_SETTING_CHECKOUT_BUTTON_STYLES,
		'cart'      => FASTWC_SETTING_CART_BUTTON_STYLES,
	);

	// If $button_type is empty, use all button types.
	$button_type = '' === $button_type ? array_keys( $types ) : $button_type;
	$button_type = is_array( $button_type ) ? $button_type : array( $button_type );

	$button_styles = array();
	foreach ( $button_type as $type ) {
		if ( in_array( $type, array_keys( $types ), true ) ) {
			$button_styles[] = get_option( $types[ $type ], '' );
		}
	}

	return implode( "\n", $button_styles );
}

/**
 * Check if a string is valid JSON.
 *
 * @param string $string The string to check.
 *
 * @return bool
 */
function fastwc_is_json( $string ) {
	if ( ! defined( 'JSON_ERROR_NONE' ) ) {
		define( 'JSON_ERROR_NONE', 0 );
	}

	json_decode( $string );
	return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Get the Fast product options string.
 *
 * @param mixed|string|array $product_options The product options value.
 *
 * @return string
 */
function fastwc_get_normalized_product_options( $product_options ) {
	if ( is_array( $product_options ) ) {
		$product_options = json_encode( $product_options );
	}

	return fastwc_is_json( $product_options ) ? $product_options : '';
}

/**
 * Check whether or not to use dark mode.
 *
 * @param int $product_id Optional. The ID of the product.
 *
 * @return bool
 */
function fastwc_use_dark_mode( $product_id = 0 ) {
	$use_dark_mode = get_option( FASTWC_SETTING_USE_DARK_MODE, false );

	/**
	 * Filter the boolean for using dark mode. The product ID allows for setting
	 * or disabling dark mode for specific products.
	 *
	 * @param bool $use_dark_mode The global dark mode setting.
	 * @param int  $product_id    The ID of the product.
	 *
	 * @return bool
	 */
	$use_dark_mode = apply_filters( 'fast_use_dark_mode', $use_dark_mode, $product_id );

	return $use_dark_mode;
}

/**
 * Get the Fast order ID from the WooCommerce order ID.
 *
 * @param int $wc_order_id The WooCommerce order ID.
 *
 * @return string
 */
function fastwc_get_fast_order_id_from_woocommerce_order_id( $wc_order_id ) {
	return ! empty( $wc_order_id ) ? get_post_meta( $wc_order_id, 'fast_order_id', true ) : '';
}
