<?php
/**
 * Plugin API
 *
 * Provides an API that exposes plugin info.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast plugin info route object.
 */
class Plugin_Info extends Base {

	/**
	 * Route namespace.
	 *
	 * @var string
	 */
	protected $namespace = FASTWC_ROUTES_BASE . '/store';

	/**
	 * Route name.
	 *
	 * @var string
	 */
	protected $route = 'plugins';

	/**
	 * Utility to get information on installed plugins.
	 *
	 * Returns an array of all installed plugins and indicates which are
	 * plugin are active and which are not. Array is keyed by the plugin's
	 * folder/slug.php (which is how WP looks at them) and includes the
	 * name, version, and true/false whether it is active or not.
	 *
	 * @see: https://codex.wordpress.org/Function_Reference/get_plugins
	 * @see: https://developer.wordpress.org/reference/functions/get_plugins/
	 * @see: https://developer.wordpress.org/reference/functions/get_option/
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return $plugins array {
	 *     An array of all installed plugins.
	 *
	 *     @type array $plugin {
	 *         The plugin information (note: array key is folder/slug.php)
	 *
	 *         @type string  $name
	 *         @type string  $version
	 *         @type boolean $active
	 *     }
	 * }
	 */
	public function callback( $request ) {
		// Get all plugins.
		include_once 'wp-admin/includes/plugin.php';
		$all_plugins = \get_plugins();

		// Get active plugins.
		$active_plugins = \get_option( 'active_plugins' );

		$plugins = array();

		// Assemble array of name, version, and whether plugin is active (boolean).
		foreach ( $all_plugins as $key => $value ) {
			$is_active = ( in_array( $key, $active_plugins, true ) ) ? true : false;
			$plugins[] = array(
				'name'    => $value['Name'],
				'version' => $value['Version'],
				'active'  => $is_active,
			);
		}

		return new \WP_REST_Response( $plugins, 200 );
	}
}
