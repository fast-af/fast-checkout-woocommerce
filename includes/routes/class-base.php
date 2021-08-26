<?php
/**
 * Base class for Fast route object.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast route base class.
 */
abstract class Base {

	/**
	 * Route namespace.
	 *
	 * @var string
	 */
	protected $namespace = FASTWC_ROUTES_BASE;

	/**
	 * Route name.
	 *
	 * @var string
	 */
	protected $route;

	/**
	 * Route methods.
	 *
	 * @var string
	 */
	protected $methods = 'GET';

	/**
	 * Route callback.
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Permission callback.
	 *
	 * @var callable
	 */
	protected $permission_callback = 'fastwc_api_permission_callback';

	/**
	 * Request object passed to the route endpoint.
	 *
	 * @var \WP_REST_Request
	 */
	protected $request;

	/**
	 * Route constructor, protected to prevent multiple instances.
	 */
	public function __construct( $do_register = true ) {
		$this->init();

		if ( $do_register ) {
			$this->register();
		}
	}

	/**
	 * Initialize the route arguments.
	 */
	protected function init() {
		if ( empty( $this->callback ) ) {
			$this->callback = array( $this, 'callback' );
		}
	}

	/**
	 * Register the route.
	 */
	protected function register() {
		register_rest_route(
			$this->namespace,
			$this->route,
			array(
				'methods'             => $this->methods,
				'callback'            => $this->callback,
				'permission_callback' => $this->permission_callback,
			)
		);

		\fastwc_log_info( 'Registered route: ' . $this->namespace . $this->route );
	}

	/**
	 * Route callback function.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 */
	abstract public function callback( $request );
}
