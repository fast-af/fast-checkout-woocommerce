<?php
/**
 * Hide Regular Checkout Buttons setting.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

use FastWC\Admin\Settings\Type\Checkbox;

/**
 * Hide Regular Checkout Buttons checkbox setting.
 */
class Hide_Regular_Checkout extends Checkbox {

	/**
	 * Initialize the object.
	 */
	protected function init() {
		$this->id                = FASTWC_SETTING_HIDE_REGULAR_CHECKOUT_BUTTONS;
		$this->section_name      = FASTWC_SECTION_OPTIONS;
		$this->title             = __( 'Hide WooCommerce Checkout Buttons on Cart', 'fast' );
		$this->field_label       = \__( 'Hide WooCommerce Checkout Buttons on Cart', 'fast' );
		$this->field_description = \__( 'Hide the standard WooCommerce "Proceed to Checkout" buttons on the cart page and the mini cart widget.', 'fast' );
		$this->default           = 0;
	}
}
