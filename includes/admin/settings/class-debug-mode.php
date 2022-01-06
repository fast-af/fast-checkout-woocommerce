<?php
/**
 * Debug Mode setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

use FastWC\Admin\Settings\Type\Checkbox;

/**
 * Debug Mode checkbox setting.
 */
class Debug_Mode extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_DEBUG_MODE;
		$this->section_name      = FASTWC_SECTION_TEST_MODE;
		$this->title             = __( 'Debug Mode', 'fast' );
		$this->field_label       = \__( 'Enable debug mode', 'fast' );
		$this->field_description = \__( 'When debug mode is enabled, the Fast plugin will maintain an error log.', 'fast' );
		$this->default           = 0;
	}
}
