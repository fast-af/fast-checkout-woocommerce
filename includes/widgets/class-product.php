<?php
/**
 * Fast PDP button widget.
 *
 * @package Fast
 */

namespace FastWC\Widgets;

/**
 * Fast PDP button widget class.
 */
class Product extends \WP_Widget {

	/**
	 * Widget constructor.
	 */
	public function __constrcut() {
		parent::__construct(
			'fastwc-pdp-widget',
			__( 'Fast Product Button', 'fast' )
		);
	}

	/**
	 * Process the widget options and display the HTML on the page.
	 *
	 * @param array $args     Widget args.
	 * @param array $instance Widget options for the current instance.
	 */
	public function widget( $args, $instance ) {
		// TODO.
	}

	/**
	 * Display the form that will be used to set options for the widget.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	public function form( $instance ) {
		// TODO.
	}

	/**
	 * Save the widget options to the database.
	 *
	 * @param array $new_instance The new settings for the widget.
	 * @param array $old_instance The old settings for the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		// TODO.
	}
}
