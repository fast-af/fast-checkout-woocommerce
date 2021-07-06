<?php
/**
 * Fast base widget class.
 *
 * @package Fast
 */

namespace FastWC\Widgets;

/**
 * Fast base widget class.
 */
class Widget extends \WP_Widget {

	/**
	 * Widget template name.
	 *
	 * @var string
	 */
	protected $template = '';

	/**
	 * Function to determine if this widget should be hidden.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	protected function should_hide( $instance ) {
		return false;
	}

	/**
	 * Process the widget options and display the HTML on the page.
	 *
	 * @param array $args     Widget args.
	 * @param array $instance Widget options for the current instance.
	 */
	public function widget( $args, $instance ) {
		if ( $this->should_hide( $instance ) ) {
			return;
		}

		$widget_data = array(
			'template' => $this->template,
			'args'     => $args,
			'instance' => $instance,
		);

		\fastwc_load_template( 'widgets/fast-widget', $widget_data );
	}

	/**
	 * Display the form that will be used to set options for the widget.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	public function form( $instance ) {
		$title       = isset( $instance['title'] ) ? $instance['title'] : '';
		$description = isset( $instance['description'] ) ? $instance['description'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'fast' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php esc_html_e( 'Description:', 'fast' ); ?></label>
			<textarea
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"
				><?php echo esc_html( $description ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Save the widget options to the database.
	 *
	 * @param array $new_instance The new settings for the widget.
	 * @param array $old_instance The old settings for the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']       = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['description'] = isset( $new_instance['description'] ) ? $new_instance['description'] : '';

		return $instance;
	}
}
