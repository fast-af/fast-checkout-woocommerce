<?php
/**
 * Generate custom post types for Fast Checkout for WooCommerce.
 *
 * @package fast
 */

/**
 * Register custom post types.
 */
function fastwc_register_post_types() {
	// Register the headless checkout link post type.
	$fastwc_headless_checkout_link = new \FastWC\Post_Types\Headless_Checkout_Link();
	$fastwc_headless_checkout_link->maybe_redirect();
}
add_action( 'init', 'fastwc_register_post_types' );
