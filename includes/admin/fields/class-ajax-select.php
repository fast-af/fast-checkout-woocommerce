<?php
/**
 * AJAX Select field class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Fields;

/**
 * AJAX Select field class.
 */
class Ajax_Select extends Field {

	/**
	 * Validate the args before rendering.
	 *
	 * @return bool
	 */
	protected function should_do_render() {
		if ( empty( $this->args['nonce'] ) ) {
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
			'nonce'       => '',
			'selected'    => array(),
			'style'       => 'width: 400px;',
			'multiple'    => true,
		);
	}

	/**
	 * Render the field.
	 */
	protected function render() {
		$multiple = '';
		$el_name  = $this->args['name'];
		if ( true === $this->args['multiple'] ) {
			$multiple = 'multiple';
			$el_name .= '[]';
		}
		?>
		<select
			data-security="<?php echo \esc_attr( wp_create_nonce( $this->args['nonce'] ) ); ?>"
			<?php echo \esc_attr( $multiple ); ?>
			class="<?php echo \esc_attr( $this->args['class'] ); ?>"
			name="<?php echo \esc_attr( $el_name ); ?>"
			id="<?php echo \esc_attr( $this->args['id'] ); ?>"
			style="<?php echo \esc_attr( $this->args['style'] ); ?>"
		>
		<?php
		if ( ! empty( $this->args['selected'] ) ) :
			foreach ( $this->args['selected'] as $value => $label ) :
				?>
			<option value="<?php echo \esc_attr( $value ); ?>" selected="selected"><?php echo \esc_html( $label ); ?></option>
				<?php
			endforeach;
		endif;
		?>
		</select>
		<?php
	}
}
