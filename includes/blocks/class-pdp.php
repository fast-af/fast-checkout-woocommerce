<?php
/**
 * Fast Checkout PDP button block.
 *
 * @package Fast
 */

namespace FastWC\Blocks;

/**
 * Fast Login button block class.
 */
class Pdp extends Block {

	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	protected $name = 'fast-pdp-button';

	/**
	 * The name of the block template to render.
	 *
	 * @var string
	 */
	protected $template = 'buttons/fast-checkout-button';

	/**
	 * Get the args to use to register the block type.
	 *
	 * @return array
	 */
	protected function get_block_args() {
		return array(
			'attributes' => array(
				'product_id' => array(
					'type'    => 'integer',
					'default' => 0,
				),
				'variant_id' => array(
					'type'    => 'integer',
					'default' => 0,
				),
				'quantity' => array(
					'type'    => 'integer',
					'default' => 1,
				),
				'product_options' => array(
					'type'    => 'object',
					'default' => null,
				),
			),
		);
	}

	/**
	 * Check to see if the button should be hidden.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return bool
	 */
	protected function should_hide( $attributes ) {
		$product_id = isset( $attributes['product_id'] ) ? $attributes['product_id'] : 0;
		return fastwc_should_hide_pdp_checkout_button( $product_id );
	}
}
