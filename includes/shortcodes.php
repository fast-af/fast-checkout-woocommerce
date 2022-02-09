<?php
/**
 * Add shortcodes to display Fast Checkout buttons.
 *
 * @package Fast
 */

/**
 * Add shortcodes on init.
 * Note that the shortcode names use underscores (_) instead of hyphens (-).
 * This is intentional. See https://codex.wordpress.org/Shortcode_API#Hyphens
 *
 * @uses add_shortcode
 * @see https://codex.wordpress.org/Shortcode_API
 */
function fastwc_add_shortcodes() {
	// Add PDP checkout button shortcode.
	add_shortcode( 'fast_product', 'fastwc_shortcode_product_button' );

	// Add cart checktout button shortcode.
	add_shortcode( 'fast_cart', 'fastwc_shortcode_cart_button' );

	fastwc_log_info( 'Initialized Fast shortcodes: fast_product and fast_cart' );
}
add_action( 'init', 'fastwc_add_shortcodes' );

/**
 * Display the Fast PDP button from the `fastwc_product` shortcode.
 * Note: This is currently exprimental and should not be used in a production site.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function fastwc_shortcode_product_button( $atts ) {
	$atts = shortcode_atts(
		array(
			'product_id'      => 0,
			'variation_id'    => 0,
			'variant_id'      => 0,
			'quantity'        => 1,
			'product_options' => false,
		),
		$atts,
		'fast_product'
	);

	if ( empty( $atts['variant_id'] ) && ! empty( $atts['variation_id'] ) ) {
		$atts['variant_id'] = $atts['variation_id'];
	}

	if ( fastwc_should_hide_pdp_checkout_button( $atts['product_id'] ) ) {
		$shortcode_output = '';
	} else {
		$shortcode_output = fastwc_shortcode_button_template(
			'buttons/fast-checkout-button',
			$atts
		);
	}

	return $shortcode_output;
}

/**
 * Display the Fast Cart button fromt the `fast_cart` shortcode.
 *
 * @param array $atts Shortcode attriubtes.
 *
 * @return string
 */
function fastwc_shortcode_cart_button( $atts ) {
	if ( fastwc_should_hide_cart_checkout_button() ) {
		$shortcode_output = '';
	} else {
		$shortcode_output = fastwc_shortcode_button_template(
			'buttons/fast-checkout-cart-button',
			$atts
		);
	}

	return $shortcode_output;
}

/**
 * Helper to display a shortcode button template.
 *
 * @param string $template The name of the template.
 * @param array  $atts     Shortcode attributes.
 *
 * @return string
 */
function fastwc_shortcode_button_template( $template, $atts ) {

	fastwc_log_info( 'Rendering shortcode template: ' . $template );

	// Start the output buffer.
	ob_start();

	/**
	 * Action that triggers before a shortcode template is rendered.
	 *
	 * @param string $template The name of the shortcode template.
	 */
	do_action( 'fastwc_before_render_shortcode', $template );

	// Load the button template, passing in `$atts` as the template's `$args`.
	fastwc_load_template( $template, $atts );

	/**
	 * Action that triggers after a shortcode template is rendered.
	 *
	 * @param string $template The name of the shortcode template.
	 */
	do_action( 'fastwc_after_render_shortcode', $template );

	return ob_get_clean();
}
