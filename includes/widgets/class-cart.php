<?php
/**
 * Fast cart button widget.
 *
 * @package Fast
 */

namespace FastWC\Widgets;

/**
 * Fast cart button widget class.
 */
class Cart extends Widget {

	/**
	 * Widget template name.
	 *
	 * @var string
	 */
	protected $template = 'widgets/fast-cart';

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'fastwc_cart',
			__( 'Fast Cart Button', 'fast' ),
			array(
				'description' => __( 'Display the Fast Checkout cart button.', 'fast' ),
			)
		);
	}

	/**
	 * Function to determine if this widget should be hidden.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	protected function should_hide( $instance ) {
		return \fastwc_should_hide_cart_checkout_button();
	}
}
