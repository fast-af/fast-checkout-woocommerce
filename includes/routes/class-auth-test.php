<?php
/**
 * Authorization Header API
 *
 * Provides an API to test the Authorization header.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast plugin info route object.
 */
class Auth_Test extends Route {

	/**
	 * Route name.
	 *
	 * @var string
	 */
	protected $route = 'authecho';

	/**
	 * Permission callback.
	 *
	 * @var callable
	 */
	protected $permission_callback = '__return_true';

	/**
	 * Test the Authorization header.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {
		$this->request = $request;

		$auth_header = 'No Authorization Header';

		$headers = $this->request->get_headers();

		if ( ! empty( $headers['authorization'] ) ) {
			$header_count = count( $headers['authorization'] );

			if ( is_array( $headers['authorization'] ) && $header_count > 0 ) {
				$auth_header = $headers['authorization'][0];
			} elseif ( is_string( $headers['authorization'] ) ) {
				$auth_header = $headers['authorization'];
			}
		}

		\fastwc_log_info( 'Authorization header endpoint called: ' . $auth_header );

		return new \WP_REST_Response( $auth_header, 200 );
	}
}
