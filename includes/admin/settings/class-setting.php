<?php
/**
 * Base setting class.
 *
 * @package Fast
 */

namespace FastWC\Admin\Settings;

/**
 * Base setting class.
 */
abstract class Setting {

	/**
	 * The setting ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The settings group name. Should correspond to an allowed option key name.
	 * 
	 * @var string
	 */
	protected $section_name;

	/**
	 * Formatted section title.
	 *
	 * @var string
	 */
	protected $section_title = '';

	/**
	 * Function that echos out any content at the
	 * top of the section (Between heading and fields).
	 *
	 * @var mixed|callable|bool
	 */
	protected $section_callback = false;

	/**
	 * The settings page on which the setting is displayed.
	 *
	 * @var string
	 */
	protected $page;

	/**
	 * Setting title.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Data used to describe the setting when registered.
	 *
	 * @var array
	 */
	protected $setting_args = array();

	/**
	 * Extra arguments used when outputting the field.
	 *
	 * @var array
	 */
	protected $field_args = array();

	/**
	 * Field label.
	 *
	 * @var string
	 */
	protected $field_label;

	/**
	 * Field description.
	 *
	 * @var string
	 */
	protected $field_description = '';

	/**
	 * Default value.
	 *
	 * @var mixed
	 */
	protected $default = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wp_settings_sections;

		// Initialize the setting.
		$this->init();

		$this->page = ! empty( $this->page ) ? $this->page : $this->section_name;

		if (
			! empty( $this->page)
			&& ! empty( $this->section )
			&& empty( $wp_settings_sections[ $this->page ][ $this->section ] )
		) {
			/**
			 * Add the settings section if it does not already exist.
			 *
			 * @param string   $id       Slug-name to identify the section.
			 * @param string   $title    Formatted title fo the section.
			 * @param callable $callback Function that echos out any content at the
			 *                           top of the section (Between heading and fields).
			 * @param string   $page     The slug-name of the settings page on which to
			 *                           show the section.
			 *
			 * @see https://developer.wordpress.org/reference/functions/add_settings_section/
			 */
			\add_settings_section(
				$this->section_name,
				$this->section_title,
				$this->section_callback,
				$this->page
			);
		}

		/**
		 * Register the setting and its data.
		 *
		 * @param string $option_group A settings group name. Should correspond
		 *                             to an allowed key name.
		 * @param string $option_name  The name of an option to sanitize and save.
		 * @param array  $args         Data used to describe the setting when registered.
		 *
		 * @see https://developer.wordpress.org/reference/functions/register_setting/
		 */
		\register_setting(
			$this->section_name,
			$this->id,
			$this->setting_args
		);


		/**
		 * Add a new field to a section of a settings page.
		 *
		 * @param string   $id       Slug-name to identify the field.
		 * @param string   $title    Formatted title of the field.
		 * @param callable $callback Function that fills the field with the desired form inputs.
		 * @param string   $page     The slug-name of the settings page on which to show the section.
		 * @param string   $section  The slug-name of the section of the settings page in which to show the box.
		 * @param array    $args     Extra arguments when outputting the field.
		 *
		 * @see https://developer.wordpress.org/reference/functions/add_settings_field/
		 */
		\add_settings_field(
			$this->id,
			$this->title,
			array( $this, 'callback' ),
			$this->page,
			$this->section_name,
			$this->field_args
		);
	}

	/**
	 * Get the value of the setting.
	 *
	 * @return mixed
	 */
	protected function get_value() {
		return \get_option( $this->id, $this->default );
	}

	/**
	 * Initialize the object.
	 */
	protected function init();

	/**
	 * Settings field callback.
	 */
	public function callback();
}
