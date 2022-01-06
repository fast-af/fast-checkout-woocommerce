<?php
/**
 * Disable Multicurrency setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

use FastWC\Admin\Settings\Type\Checkbox;

/**
 * Disable Multicurrency checkbox setting.
 */
class Disable_Multicurrency extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_DISABLE_MULTICURRENCY;
		$this->section_name      = FASTWC_SECTION_TEST_MODE;
		$this->title             = __( 'Disable Multicurrency Support', 'fast' );
		$this->field_label       = \__( 'Disable Multicurrency Support', 'fast' );
		$this->field_description = \__( 'Disable multicurrency support in Fast Checkout.', 'fast' );
		$this->default           = 0;
	}
}
