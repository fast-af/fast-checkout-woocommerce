<?php
/**
 * Textarea field class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Fields;

/**
 * Textarea field class.
 */
class Textarea extends Field {

	/**
	 * Get the default args for the field type.
	 *
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'name'        => '',
			'id'          => '',
			'rows'        => '10',
			'cols'        => '50',
			'class'       => 'textarea-field',
			'value'       => '',
			'description' => '',
		);
	}

	/**
	 * Render the field.
	 */
	protected function render() {
		?>
		<textarea
			name="<?php echo \esc_attr( $this->args['name'] ); ?>"
			id="<?php echo \esc_attr( $this->args['id'] ); ?>"
			rows="<?php echo \esc_attr( $this->args['rows'] ); ?>"
			cols="<?php echo \esc_attr( $this->args['cols'] ); ?>"><?php echo \esc_textarea( $this->args['value'] ); ?></textarea>
		<?php
	}
}
