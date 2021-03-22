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
function fast_add_shortcodes() {
	// Add PDP checkout button shortcode.
	add_shortcode( 'fast_product', 'fast_shortcode_product_button' );

	// Add cart checktout button shortcode.
	add_shortcode( 'fast_cart', 'fast_shortcode_cart_button' );
}
add_action( 'init', 'fast_add_shortcodes' );

/**
 * Display the Fast PDP button from the `fast_product` shortcode.
 * Note: This is currently exprimental and should not be used in a production site.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function fast_shortcode_product_button( $atts ) {
	return fast_shortcode_button_template(
		'buttons/fast-checkout-button',
		$atts
	);
}

/**
 * Display the Fast Cart button fromt the `fast_cart` shortcode.
 *
 * @param array $atts Shortcode attriubtes.
 *
 * @return string
 */
function fast_shortcode_cart_button( $atts ) {
	return fast_shortcode_button_template(
		'buttons/fast-checkout-cart-button',
		$atts
	);
}

/**
 * Helper to display a shortcode button template.
 *
 * @param string $template The name of the template.
 * @param array  $atts     Shortcode attributes.
 *
 * @return string
 */
function fast_shortcode_button_template( $template, $atts ) {

	// Start the output buffer.
	ob_start();

	// Load the button template, passing in `$atts` as the template's `$args`.
	fast_load_template( $template, $atts );

	return ob_get_clean();
}
