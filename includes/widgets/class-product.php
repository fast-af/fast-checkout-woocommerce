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
class Product extends Widget {

	/**
	 * Widget template name.
	 *
	 * @var string
	 */
	protected $template = 'widgets/fast-product';

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'fastwc_product',
			__( 'Fast Product Button', 'fast' ),
			array(
				'description' => __( 'Display the Fast Checkout product button.', 'fast' ),
			)
		);
	}

	/**
	 * Function to determine if this widget should be hidden.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	protected function should_hide( $instance ) {
		$product_id = isset( $instance['product_id'] ) ? $instance['product_id'] : 0;
		return \fastwc_should_hide_pdp_checkout_button( $product_id );
	}

	/**
	 * Display the form that will be used to set options for the widget.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	public function form( $instance ) {
		parent::form( $instance );

		$product_id      = isset( $instance['product_id'] ) && is_numeric( $instance['product_id'] ) ? $instance['product_id'] : 0;
		$variant_id      = isset( $instance['variant_id'] ) && is_numeric( $instance['variant_id'] ) ? $instance['variant_id'] : 0;
		$quantity        = isset( $instance['quantity'] ) && is_numeric( $instance['quantity'] ) ? $instance['quantity'] : 1;
		$product_options = isset( $instance['product_options'] ) ? $instance['product_options'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'product_id' ) ); ?>"><?php esc_html_e( 'Product ID:', 'fast' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'product_id' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'product_id' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $product_id ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'variant_id' ) ); ?>"><?php esc_html_e( 'Variation ID:', 'fast' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'variant_id' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'variant_id' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $variant_id ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'quantity' ) ); ?>"><?php esc_html_e( 'Quantity:', 'fast' ); ?></label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'quantity' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'quantity' ) ); ?>"
				type="number"
				min="1"
				value="<?php echo esc_attr( $quantity ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'product_options' ) ); ?>"><?php esc_html_e( 'Product Options:', 'fast' ); ?></label>
			<textarea
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'product_options' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'product_options' ) ); ?>"
				><?php echo esc_textarea( $product_options ); ?></textarea>
			<span class="description"><?php esc_html_e( 'The product options must be formatted as valid JSON.', 'fast' ); ?></span>
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
		$instance = parent::update( $new_instance, $old_instance );

		$instance['product_id']      = isset( $new_instance['product_id'] ) && is_numeric( $new_instance['product_id'] ) ? $new_instance['product_id'] : 0;
		$instance['variant_id']      = isset( $new_instance['variant_id'] ) && is_numeric( $new_instance['variant_id'] ) ? $new_instance['variant_id'] : 0;
		$instance['quantity']        = isset( $new_instance['quantity'] ) && is_numeric( $new_instance['quantity'] ) ? $new_instance['quantity'] : 1;
		$instance['product_options'] = isset( $new_instance['product_options'] ) ? fastwc_get_normalized_product_options( $new_instance['product_options'] ) : '';

		return $instance;
	}
}
