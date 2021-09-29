<?php
/**
 * Fast Plugin Settings Fields
 *
 * @package Fast
 */

// Load base field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-field.php';
// Load input field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-input.php';
// Load textarea field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-textarea.php';
// Load checkbox field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-checkbox.php';
// Load select field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-select.php';
// Load image select field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-image-select.php';
// Load ajax select field class.
require_once FASTWC_PATH . 'includes/admin/fields/class-ajax-select.php';

/**
 * Standard text input field.
 *
 * @param array $args Attribute args for the field.
 *
 * @return FastWC\Admin\Fields\Input
 */
function fastwc_settings_field_input( $args ) {
	return new FastWC\Admin\Fields\Input( $args );
}

/**
 * Standard textarea field.
 *
 * @param array $args Attribute args for the field.
 *
 * @return FastWC\Admin\Fields\Textarea
 */
function fastwc_settings_field_textarea( $args ) {
	return new FastWC\Admin\Fields\Textarea( $args );
}

/**
 * Standard checkbox input field.
 *
 * @param array $args Attribute args for the field.
 *
 * @return FastWC\Admin\Fields\Checkbox
 */
function fastwc_settings_field_checkbox( $args ) {
	return new FastWC\Admin\Fields\Checkbox( $args );
}

/**
 * Regular select settings field.
 *
 * @param array $args Attribute args for the field.
 *
 * @return FastWC\Admin\Fields\Select
 */
function fastwc_settings_field_select( $args ) {
	return new FastWC\Admin\Fields\Select( $args );
}

/**
 * Image select settings field.
 *
 * @param array $args Attribute args for the field.
 *
 * @return FastWC\Admin\Fields\Image_Select
 */
function fastwc_settings_field_image_select( $args ) {
	return new FastWC\Admin\Fields\Image_Select( $args );
}


/**
 * Ajax select settings field.
 *
 * @param array $args Attribute args for the field.
 *
 * @return FastWC\Admin\Fields\Ajax_Select
 */
function fastwc_settings_field_ajax_select( $args ) {
	return new FastWC\Admin\Fields\Ajax_Select( $args );
}
