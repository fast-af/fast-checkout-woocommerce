<?php
/**
 * Widgets for displaying the Fast Checkout buttons.
 *
 * @package Fast
 */

// Define the PDP button widget.
require_once FASTWC_PATH . 'includes/widgets/class-product.php';
// Define the cart button widget.
require_once FASTWC_PATH . 'includes/widgets/class-cart.php';

/**
 * Register the Fast Checkout button widgets.
 */
function fastwc_register_widgets() {

	$fastwc_widgets = array(
		'\FastWC\Widgets\Product',
		'\FastWC\Widgets\Cart',
	);

	foreach ( $fastwc_widgets as $fastwc_widget ) {
		register_widget( $fastwc_widget );
	}

}
add_action( 'widgets_init', 'fastwc_register_widgets' );
