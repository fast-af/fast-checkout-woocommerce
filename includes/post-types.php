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
    if ( fastwc_headless_is_enabled() && fastwc_gutenberg_is_active() ) {
    	// Register the headless checkout link post type.
    	new \FastWC\Post_Types\Headless_Checkout_Link();
    }
}
add_action( 'init', 'fastwc_register_post_types' );
