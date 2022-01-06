<?php
/**
 * Test Mode setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

use FastWC\Admin\Settings\Type\Checkbox;

/**
 * Test Mode checkbox setting.
 */
class Test_Mode extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_TEST_MODE;
		$this->section_name      = FASTWC_SECTION_TEST_MODE;
		$this->title             = __( 'Test Mode', 'fast' );
		$this->field_label       = \__( 'Enable test mode', 'fast' );
		$this->field_description = \__( 'When test mode is enabled, only logged-in admin users will see the Fast Checkout button.', 'fast' );
		$this->default           = '1';
	}
}
