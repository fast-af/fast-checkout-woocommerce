<?php
/**
 * Fast base third-party plugin class.
 *
 * @package Fast
 */

namespace FastWC\Third_Party;

use FastWC\Config;

/**
 * Fast base third-party plugin class.
 */
abstract class Plugin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Plugin status.
	 *
	 * @var string
	 */
	protected $status = '';

	/**
	 * Settings section name.
	 *
	 * @var string
	 */
	protected $section_name = 'fast_third_party';

	/**
	 * Construct the plugin integration.
	 */
	public function __construct() {

		if ( $this->is_active() ) {
			\add_action( 'admin_init', array( $this, 'maybe_add_setting' ) );

			$setting_name = $this->get_setting_name();

			$do_init = boolval(
				\get_option(
					$setting_name,
					true
				)
			);

			$do_init = boolval(
				/**
				 * Flag to activate or deactivate the plugin initialization.
				 *
				 * @param bool $do_init The initial value of the flag.
				 *
				 * @return bool
				 */
				\apply_filters(
					$setting_name,
					$do_init
				)
			);

			if ( $do_init ) {
				$this->init();
			}
		}
	}

	/**
	 * Initialize the plugin.
	 */
	abstract protected function init();

	/**
	 * Get the setting name.
	 *
	 * @return string
	 */
	protected function get_setting_name() {
		return sprintf(
			Config::KEY_PLUGIN_DO_INIT_FORMAT,
			str_replace( array( '/', '.' ), '_', $this->slug )
		);
	}

	/**
	 * Maybe add the settings section.
	 */
	protected function maybe_add_settings_section() {
		global $wp_settings_sections;

		if ( empty( $wp_settings_sections[ $this->section_name ][ $this->section_name ] ) ) {
			\add_settings_section( $this->section_name, '', false, $this->section_name );

			\add_filter(
				'fastwc_settings_tabs',
				/**
				 * Update the list of settings tabs.
				 *
				 * @param array $settings_tabs The list of settings tabs.
				 *
				 * @return array
				 */
				function( $settings_tabs ) {
					$settings_tabs[ $this->section_name ] = \__( 'Third Party Plugins', 'fast' );

					return $settings_tabs;
				}
			);
		}
	}

	/**
	 * Get the setting title.
	 *
	 * @return string
	 */
	abstract protected function get_setting_title();

	/**
	 * Get the setting description.
	 *
	 * @return string
	 */
	abstract protected function get_setting_description();

	/**
	 * Callback for rendering the setting.
	 */
	public function setting_callback() {
		$setting_name  = $this->get_setting_name();
		$not_set_value = 'not set';
		$setting_value = \get_option( $setting_name, $not_set_value );

		if ( $not_set_value === $setting_value ) {
			$setting_value = '1';
			update_option( $setting_name, $setting_value );
		}

		\fastwc_settings_field_checkbox(
			array(
				'name'        => $setting_name,
				'current'     => $setting_value,
				'label'       => $this->get_setting_title(),
				'description' => $this->get_setting_description(),
			)
		);
	}


	/**
	 * If the plugin is active, add a setting to activate/deactivate the plugin support object.
	 *
	 * @return bool
	 */
	public function maybe_add_setting() {
		if ( $this->is_active() ) {
			$setting_name = $this->get_setting_name();

			$this->maybe_add_settings_section();
			\register_setting( $this->section_name, $setting_name );
			// add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() )
			\add_settings_field( $setting_name, $this->get_setting_title(), array( $this, 'setting_callback' ), $this->section_name, $this->section_name );

			return true;
		}

		return false;
	}

	/**
	 * Check to see if the plugin is active.
	 *
	 * @return bool
	 */
	protected function is_active() {
		$is_active = false;

		if ( empty( $this->status ) ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$is_active = \is_plugin_active( $this->slug );

			// Store the status to avoid having to call `is_plugin_active` again.
			$this->status = $is_active ? 'active' : 'inactive';
		} else {
			$is_active = ( 'active' === $this->status );
		}

		return $is_active;
	}

}
