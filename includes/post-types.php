<?php
/**
 * Generate custom post types for Fast Checkout for WooCommerce.
 *
 * @package fast
 */

// Load the base post type class.
require_once FASTWC_PATH . 'includes/post-types/class-post-type.php';
//Load the headless checkout link post type.
require_once FASTWC_PATH . 'includes/post-types/class-headless-checkout-link.php';

/**
 * Register custom post types.
 */
function fastwc_register_post_types() {
	// Register the headless checkout link post type.
	$fastwc_headless_checkout_link = new \FastWC\Post_Types\Headless_Checkout_Link();
	$fastwc_headless_checkout_link->maybe_redirect();
}
add_action( 'init', 'fastwc_register_post_types' );
