<?php
/**
 * Input field class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Fields;

/**
 * Input field class.
 */
class Input extends Field {

	/**
	 * Get the default args for the field type.
	 *
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'name'        => '',
			'id'          => '',
			'type'        => 'text',
			'class'       => 'input-field',
			'value'       => '',
			'description' => '',
		);
	}

	/**
	 * Render the field.
	 */
	protected function render() {
		?>
		<input
			name="<?php echo \esc_attr( $this->args['name'] ); ?>"
			id="<?php echo \esc_attr( $this->args['id'] ); ?>"
			type="<?php echo \esc_attr( $this->args['type'] ); ?>"
			class="<?php echo \esc_attr( $this->args['class'] ); ?>"
			value="<?php echo \esc_attr( $this->args['value'] ); ?>"
		/>
		<?php
	}
}
