<?php
/**
 * Initialized custom Gutenberg blocks for Fast Checkout buttons.
 *
 * @package Fast
 */

// Load the base block class.
require_once FASTWC_PATH . 'includes/blocks/class-block.php';
// Load the cart block class.
require_once FASTWC_PATH . 'includes/blocks/class-cart.php';
// Load the login block class.
require_once FASTWC_PATH . 'includes/blocks/class-login.php';
// Load the PDP block class.
require_once FASTWC_PATH . 'includes/blocks/class-pdp.php';

/**
 * Register the block types.
 */
function fastwc_register_block_types() {
	// Skip this if Gutenberg is not active on the site.
	if ( ! fastwc_gutenberg_is_active() ) {
		return;
	}

	$blocks = array(
		new FastWC\Blocks\Cart(),
		new FastWC\Blocks\Pdp(),
		new FastWC\Blocks\Login(),
	);

	foreach ( $blocks as $block_type ) {
		$block_type->register();
	}
}
add_action( 'init', 'fastwc_register_block_types' );
