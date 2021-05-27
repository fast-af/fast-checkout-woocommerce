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
	public function __construct() {
		parent::__construct(
			'fastwc_pdp_widget',
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
		$widget_data = array(
			'args'     => $args,
			'instance' => $instance,
		);

		\fastwc_load_template( 'widgets/fast-product', $widget_data );
	}

	/**
	 * Display the form that will be used to set options for the widget.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	public function form( $instance ) {
		$title      = isset( $instance['title'] ) ? $instance['title'] : '';
		$product_id = isset( $instance['product_id'] ) && is_numeric( $instance['product_id'] ) ? $instance['product_id'] : 0;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_fields_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'fast' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_fields_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_fields_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_fields_name( 'product_id' ) ); ?>"><?php esc_html_e( 'Product ID (Optional):', 'fast' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_fields_id( 'product_id' ) ); ?>"
				name="<?php echo esc_attr( $this->get_fields_name( 'product_id' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $product_id ); ?>"
			/>
		</p>
		<?php
	}

	/**
	 * Save the widget options to the database.
	 *
	 * @param array $new_instance The new settings for the widget.
	 */
	public function update( $new_instance ) {
		$instance = array();

		$instance['title']      = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['product_id'] = isset( $new_instance['product_id'] ) && is_numeric( $new_instance['product_id'] ) ? $new_instance['product_id'] : 0;

		return $instance;
	}
}
