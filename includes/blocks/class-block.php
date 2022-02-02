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
	 * Check to see if the button should be hidden.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return bool
	 */
	protected function should_hide( $attributes ) {
		return false;
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

		if (
			! $this->should_hide( $attributes ) &&
			! empty( $this->template )
		) {
			ob_start();

			/**
			 * Action to be triggered before a block is rendered.
			 *
			 * @param string $template The name of the block template being rendered.
			 */
			\do_action( 'fastwc_before_render_block', $this->template );

			\fastwc_load_template( $this->template, $attributes );

			/**
			 * Action to be triggered after a block is rendered.
			 *
			 * @param string $template The name of the block template being rendered.
			 */
			\do_action( 'fastwc_after_render_block', $this->teplate );

			$block_output = ob_get_clean();
		}

		return $block_output;
	}
}
