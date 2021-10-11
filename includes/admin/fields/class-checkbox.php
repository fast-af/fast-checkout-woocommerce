<?php
/**
 * Checkbox field class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Fields;

/**
 * Checkbox field class.
 */
class Checkbox extends Field {

	/**
	 * Get the default args for the field type.
	 *
	 * @return array
	 */
	protected function get_default_args() {
		return array(
			'name'        => '',
			'id'          => '',
			'class'       => 'checkbox-field',
			'value'       => '1',
			'checked'     => 1,
			'current'     => 0,
			'label'       => '',
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
			type="checkbox"
			class="<?php echo \esc_attr( $this->args['class'] ); ?>"
			value="<?php echo \esc_attr( $this->args['value'] ); ?>"
			<?php \checked( $this->args['checked'], $this->args['current'] ); ?>
		/>
		<label for="<?php echo \esc_attr( $this->args['name'] ); ?>"><?php echo \esc_html( $this->args['label'] ); ?></label>
		<?php
	}
}
