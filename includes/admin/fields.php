<?php
/**
 * Fast Plugin Settings Fields
 *
 * @package Fast
 */

/**
 * Get the field args.
 *
 * @param array $default_args The default args.
 * @param array $args         The field-specific args.
 *
 * @return array
 */
function fastwc_get_field_args( $default_args, $args ) {
	$defaults = array(
		'name' => '',
		'id'   => '',
	);

	$default_args = wp_parse_args( $default_args, $defaults );

	$args = wp_parse_args( $args, $default_args );

	// If the id is empty, set it equal to the name field.
	if ( empty( $args['id'] ) ) {
		$args['id'] = $args['name'];
	}

	return $args;
}

/**
 * Render a field description if it exists.
 *
 * @param string $description The field description.
 */
function fastwc_maybe_render_field_description( $description ) {
	if ( ! empty( $description ) ) :
		?>
	<p class="description"><?php echo wp_kses_post( $description ); ?></p>
		<?php
	endif;
}

/**
 * Standard text input field.
 *
 * @param array $args Attribute args for the field.
 */
function fastwc_settings_field_input( $args ) {
	$args = fastwc_get_field_args(
		array(
			'name'        => '',
			'id'          => '',
			'type'        => 'text',
			'class'       => 'input-field',
			'value'       => '',
			'description' => '',
		),
		$args
	);

	// An input field with no name is invalid. Do nothing if the name is empty.
	if ( empty( $args['name'] ) ) {
		return;
	}
	?>
	<input
		name="<?php echo esc_attr( $args['name'] ); ?>"
		id="<?php echo esc_attr( $args['id'] ); ?>"
		type="<?php echo esc_attr( $args['type'] ); ?>"
		class="<?php echo esc_attr( $args['class'] ); ?>"
		value="<?php echo esc_attr( $args['value'] ); ?>"
	/>
	<?php
	fastwc_maybe_render_field_description( $args['description'] );
}

/**
 * Standard textarea field.
 *
 * @param array $args Attribute args for the field.
 */
function fastwc_settings_field_textarea( $args ) {
	$args = fastwc_get_field_args(
		array(
			'name'        => '',
			'id'          => '',
			'rows'        => '10',
			'cols'        => '50',
			'class'       => 'textarea-field',
			'value'       => '',
			'description' => '',
		),
		$args
	);

	// A textarea field with no name is invalid. Do nothing if the name is empty.
	if ( empty( $args['name'] ) ) {
		return;
	}
	?>
	<textarea name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" rows="<?php echo esc_attr( $args['rows'] ); ?>" cols="<?php echo esc_attr( $args['cols'] ); ?>"><?php echo esc_textarea( $args['value'] ); ?></textarea>
	<?php
	fastwc_maybe_render_field_description( $args['description'] );
}

/**
 * Standard checkbox input field.
 *
 * @param array $args Attribute args for the field.
 */
function fastwc_settings_field_checkbox( $args ) {
	$args = fastwc_get_field_args(
		array(
			'name'        => '',
			'id'          => '',
			'class'       => 'checkbox-field',
			'value'       => '1',
			'checked'     => 1,
			'current'     => 0,
			'label'       => '',
			'description' => '',
		),
		$args
	);

	// A textarea field with no name is invalid. Do nothing if the name is empty.
	if ( empty( $args['name'] ) ) {
		return;
	}
	?>
	<input
		name="<?php echo esc_attr( $args['name'] ); ?>"
		id="<?php echo esc_attr( $args['id'] ); ?>"
		type="checkbox"
		class="<?php echo esc_attr( $args['class'] ); ?>"
		value="<?php echo esc_attr( $args['value'] ); ?>"
		<?php checked( $args['checked'], $args['current'] ); ?>
	/>
	<label for="<?php echo esc_attr( $args['name'] ); ?>"><?php echo esc_html( $args['label'] ); ?></label>
	<?php
	fastwc_maybe_render_field_description( $args['description'] );
}

/**
 * Regular select settings field.
 *
 * @param array $args Attribute args for the field.
 */
function fastwc_settings_field_select( $args ) {
	$args = fastwc_get_field_args(
		array(
			'name'        => '',
			'id'          => '',
			'class'       => 'fast-select',
			'description' => '',
			'options'     => array(),
			'value'       => '',
		),
		$args
	);

	// A select field with no name or no options is invalid.
	if ( empty( $args['name'] ) || empty( $args['options'] ) ) {
		return;
	}
	?>
	<select
		class="<?php echo esc_attr( $args['class'] ); ?>"
		name="<?php echo esc_attr( $args['name'] ); ?>"
		id="<?php echo esc_attr( $args['id'] ); ?>"
	>
	<?php
	foreach ( $args['options'] as $value => $label ) :
		?>
	<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $args['value'], $value ); ?>><?php echo esc_html( $label ); ?></option>
		<?php
	endforeach;
	?>
	</select>
	<?php
	fastwc_maybe_render_field_description( $args['description'] );
}

/**
 * Image select settings field.
 *
 * @param array $args Attribute args for the field.
 */
function fastwc_settings_field_image_select( $args ) {
	$args = fastwc_get_field_args(
		array(
			'name'        => '',
			'id'          => '',
			'class'       => 'fast-select',
			'description' => '',
			'options'     => array(),
			'value'       => '',
		),
		$args
	);

	// A select field with no name or no options is invalid.
	if ( empty( $args['name'] ) || empty( $args['options'] ) ) {
		return;
	}

	fastwc_maybe_render_field_description( $args['description'] );
	?>
	<div class="fast-image-select" id="fast-image-select-<?php echo esc_attr( $args['name'] ); ?>">
	<?php
	$index = 0;
	foreach ( $args['options'] as $value => $option ) :
		$label = ! empty( $option['label'] ) ? $option['label'] : '';
		$image = ! empty( $option['image'] ) ? $option['image'] : '';
		$id    = $args['name'] . '-' . $index;
		$index++;
		?>
		<div class="fast-image-select--item">
			<input
				type="radio"
				name="<?php echo esc_attr( $args['name'] ); ?>"
				id="<?php echo esc_attr( $id ); ?>"
				class="fast-image-select--input screen-reader-text"
				value="<?php echo esc_attr( $value ); ?>"
				<?php checked( $args['value'], $value ); ?>
			/>
			<label
				for="<?php echo esc_attr( $id ); ?>"
				class="fast-image-select--label"
				aria-label="<?php echo esc_attr( $label ); ?>"
			>
				<span class="fast-image-select--label-text"><?php echo esc_html( $label ); ?></span>
				<?php if ( ! empty( $image ) ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" class="fast-image-select--image" alt="<?php echo esc_attr( $label ); ?>" />
				<?php endif; ?>
			</label>
		</div>
			<?php
	endforeach;
	?>
	</div>
	<?php
}


/**
 * Ajax select settings field.
 *
 * @param array $args Attribute args for the field.
 */
function fastwc_settings_field_ajax_select( $args ) {
	$args = fastwc_get_field_args(
		array(
			'name'        => '',
			'id'          => '',
			'class'       => 'fast-select',
			'description' => '',
			'nonce'       => '',
			'selected'    => array(),
			'style'       => 'width: 400px;',
			'multiple'    => true,
		),
		$args
	);

	// A textarea field with no name is invalid. Do nothing if the name is empty.
	// For security purposes, a nonce must be added as well.
	if ( empty( $args['name'] ) || empty( $args['nonce'] ) ) {
		return;
	}
	$multiple = '';
	$el_name  = $args['name'];
	if ( true === $args['multiple'] ) {
		$multiple = 'multiple';
		$el_name .= '[]';
	}
	?>
	<select
		data-security="<?php echo esc_attr( wp_create_nonce( $args['nonce'] ) ); ?>"
		<?php echo esc_attr( $multiple ); ?>
		class="<?php echo esc_attr( $args['class'] ); ?>"
		name="<?php echo esc_attr( $el_name ); ?>"
		id="<?php echo esc_attr( $args['id'] ); ?>"
		style="<?php echo esc_attr( $args['style'] ); ?>"
	>
	<?php
	if ( ! empty( $args['selected'] ) ) :
		foreach ( $args['selected'] as $value => $label ) :
			?>
		<option value="<?php echo esc_attr( $value ); ?>" selected="selected"><?php echo esc_html( $label ); ?></option>
			<?php
		endforeach;
	endif;
	?>
	</select>
	<?php
	fastwc_maybe_render_field_description( $args['description'] );
}
