<?php
/**
 * Base class for Fast block types.
 *
 * @package Fast
 */

namespace FastWC\Blocks;

/**
 * Fast base block type class.
 */
abstract class Block {
	/**
	 * The namespace of the block.
	 *
	 * @var string
	 */
	protected $namespace = 'fastwc';

	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The name of the block template to render.
	 *
	 * @var string
	 */
	protected $template;

	/**
	 * Register the block type.
	 */
	public function register() {
		$block_name = $this->namespace . '/' . $this->name;
		$block_args = $this->get_block_args();

		$default_block_args = array(
			'attributes'      => array(),
			'render_callback' => array( $this, 'render' ),
		);

		$block_args = wp_parse_args( $block_args, $default_block_args );

		// Register the block type.
		\register_block_type(
			$block_name,
			$block_args
		);
	}

	/**
	 * Get the args to use to register the block type.
	 *
	 * @return array
	 */
	protected function get_block_args() {
		return array();
	}

	/**
	 * Render the block.
	 *
	 * @param array  $attributes Attributes passed form the block to the server for server side rendering.
	 * @param string $content    The original content.
	 *
	 * @return string
	 */
	public function render( $attributes, $content ) {
		$block_output = '';

		if ( ! empty( $this->template ) ) {
			ob_start();

			\fastwc_load_template( $this->template, $attributes );

			$block_output = ob_get_clean();
		}

		return $block_output;
	}
}
