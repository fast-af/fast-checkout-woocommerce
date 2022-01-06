<?php
/**
 * Checkbox setting class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings\Type;

/**
 * Checkbox setting class.
 */
abstract class Checkbox extends FastWC\Admin\Settings\Setting {

	/**
	 * Get the value of the setting.
	 *
	 * @return mixed
	 */
	protected function get_value() {
		$not_set_value = 'value not set';
		$value         = get_option( $this->id, $not_set_value );

		if ( $not_set_value === $value ) {
			$value = $this->default;
			update_option( $this->id, $this->default );
		}

		return $value;
	}

	/**
	 * Settings field callback.
	 */
	public function callback() {
		\fastwc_settings_field_checkbox(
			array(
				'name'        => $this->id,
				'current'     => $this->get_value(),
				'label'       => $this->field_label,
				'description' => $this->field_description,
			)
		);
	}

}
