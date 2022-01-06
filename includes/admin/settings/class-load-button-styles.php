<?php
/**
 * Load button styles setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

use FastWC\Admin\Settings\Type\Checkbox;

/**
 * Load button styles checkbox setting.
 */
class Load_Button_Styles extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_LOAD_BUTTON_STYLES;
		$this->section_name      = FASTWC_SECTION_STYLES;
		$this->title             = __( 'Load Button Styles', 'fast' );
		$this->field_label       = \__( 'Load the button styles as configured in the settings.', 'fast' );
		$this->field_description = \__( 'When this box is checked, the styles configured below will be loaded to provide additional styling to the loading of the Fast buttons.', 'fast' );
		$this->default           = '1';
	}
}
