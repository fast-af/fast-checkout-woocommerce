<?php
/**
 * Dark Mode setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;



/**
 * Dark Mode checkbox setting.
 */
class Dark_Mode extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_USE_DARK_MODE;
		$this->section_name      = FASTWC_SECTION_STYLES;
		$this->title             = __( 'Enable Dark Mode', 'fast' );
		$this->field_label       = \__( 'Enable Dark Mode for the Fast Buttons.', 'fast' );
		$this->field_description = \__( 'When this box is checked, the Fast buttons will be rendered in dark mode.', 'fast' );
		$this->default           = 0;
	}
}
