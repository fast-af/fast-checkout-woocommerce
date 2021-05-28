<?php
/**
 * Widgets for displaying the Fast buttons.
 *
 * @package Fast
 */

// Define the base button widget.
require_once FASTWC_PATH . 'includes/widgets/class-widget.php';
// Define the PDP button widget.
require_once FASTWC_PATH . 'includes/widgets/class-product.php';
// Define the cart button widget.
require_once FASTWC_PATH . 'includes/widgets/class-cart.php';
// Define the login button widget.
require_once FASTWC_PATH . 'includes/widgets/class-login.php';

/**
 * Register the Fast button widgets.
 */
function fastwc_register_widgets() {

	$fastwc_widgets = array(
		'\FastWC\Widgets\Product',
		'\FastWC\Widgets\Cart',
		'\FastWC\Widgets\Login',
	);

	foreach ( $fastwc_widgets as $fastwc_widget ) {
		register_widget( $fastwc_widget );
	}

}
add_action( 'widgets_init', 'fastwc_register_widgets' );
