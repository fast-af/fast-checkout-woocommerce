<?php
/**
 * Select field class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Fields;

/**
 * Select field class.
 */
class Select extends Field {

	/**
	 * Validate the args before rendering.
	 *
	 * @return bool
	 */
	protected function should_do_render() {
		if ( empty( $this->args['options'] ) ) {
			return false;
		}

		return parent::should_do_render();
	}

	/**
	 * Get the default args for the field type.
	 *
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'name'        => '',
			'id'          => '',
			'class'       => 'fast-select',
			'description' => '',
			'options'     => array(),
			'empty_label' => '',
			'value'       => '',
		);
	}

	/**
	 * Render the field.
	 */
	protected function render() {
		?>
		<select
			class="<?php echo \esc_attr( $this->args['class'] ); ?>"
			name="<?php echo \esc_attr( $this->args['name'] ); ?>"
			id="<?php echo \esc_attr( $this->rgs['id'] ); ?>"
		>
		<?php if ( ! empty( $this->args['empty_label'] ) ) : ?>
		<option value="" <?php \selected( $this->args['value'], '' ); ?>><?php echo \esc_html( $this->args['empty_label'] ); ?></option>
		<?php endif; ?>
		<?php
		foreach ( $this->args['options'] as $value => $label ) :
			?>
		<option value="<?php echo \esc_attr( $value ); ?>" <?php \selected( $this->args['value'], $value ); ?>><?php echo \esc_html( $label ); ?></option>
			<?php
		endforeach;
		?>
		</select>
		<?php
	}
}
