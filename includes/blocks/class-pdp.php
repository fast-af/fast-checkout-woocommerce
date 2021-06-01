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
			),
		);
	}
}
