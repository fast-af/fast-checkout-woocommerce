<?php
/**
 * Show Login Button setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

use FastWC\Admin\Settings\Type\Checkbox;

/**
 * Show Login Button checkbox setting.
 */
class Show_Login_Button extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER;
		$this->section_name      = FASTWC_SECTION_OPTIONS;
		$this->title             = __( 'Display Login in Footer', 'fast' );
		$this->field_label       = \__( 'Display Fast Login Button in Footer', 'fast' );
		$this->field_description = \__( 'The Fast Login button displays in the footer by default for non-logged in users. Uncheck this option to prevent the Fast Login button from displaying in the footer.', 'fast' );
		$this->default           = '1';
	}
}
