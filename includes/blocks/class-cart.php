<?php
/**
 * Fast Checkout cart button block.
 *
 * @package Fast
 */

namespace FastWC\Blocks;

/**
 * Fast Checkout cart button block class.
 */
class Cart extends Block {

	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	protected $name = 'fast-cart-button';

	/**
	 * The name of the block template to render.
	 *
	 * @var string
	 */
	protected $template = 'buttons/fast-checkout-cart-button';

	/**
	 * Check to see if the button should be hidden.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return bool
	 */
	protected function should_hide( $attributes ) {
		return fastwc_should_hide_cart_checkout_button();
	}
}
