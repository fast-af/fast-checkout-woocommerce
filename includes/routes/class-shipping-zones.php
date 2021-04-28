<?php
/**
 * Provides an API that exposes shipping zones.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast shipping zone route object.
 */
class Shipping_Zones extends Base {

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
	protected $route = 'shipping_zones';

	/**
	 * Route methods.
	 *
	 * @var string
	 */
	protected $methods = 'GET';

	/**
	 * Collects all possible shipping locations at once
	 * Used in FE to precheck.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {
		$this->request = $request;

		$zone_ids = array_keys( array( '' ) + \WC_Shipping_Zones::get_zones() );
		$loc_arr  = array();

		// Loop through shipping Zones IDs.
		foreach ( $zone_ids as $zone_id ) {
			// Get the shipping Zone object.
			$shipping_zone = new \WC_Shipping_Zone( $zone_id );

			// Get all shipping location values for the shipping zone.
			$locations = $shipping_zone->get_zone_locations();

			$loc_arr = array_merge(
				$loc_arr,
				$this->parse_locations( $locations, $loc_arr )
			);
		}

		return new \WP_REST_Response( $loc_arr, 200 );
	}

	/**
	 * Parse locations.
	 *
	 * @param array $locations List of locations to parse.
	 * @param array $loc_arr   Location array.
	 *
	 * @return array
	 */
	protected function parse_locations( $locations, $loc_arr ) {
		$new_loc_arr = array();

		foreach ( $locations as $location ) {
			if ( 'country' !== $location->type && 'state' !== $location->type ) {
				continue;
			}

			// Do not insert item with same code.
			if ( ! $this->loc_arr_has_location( $loc_arr, $location ) ) {
				$new_loc_arr[] = array(
					'code' => $location->code,
					'type' => $location->type,
				);
			}
		}

		return $new_loc_arr;
	}

	/**
	 * Check loc_arr if location already exists.
	 *
	 * @param array    $loc_arr  Array of locations.
	 * @param stdClass $location Location to check.
	 *
	 * @return bool
	 */
	protected function loc_arr_has_location( $loc_arr, $location ) {
		foreach ( $loc_arr as $li ) {
			if ( $li->code === $location->code ) {
				return true;
			}
		}

		return false;
	}
}
