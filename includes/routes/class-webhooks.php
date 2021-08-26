<?php
/**
 * Provides an API that exposes a list of disabled Fast webhooks.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast shipping route object.
 */
class Webhooks extends Base {

    /**
     * Route name.
     *
     * @var string
     */
    protected $route = 'webhooks';


    /**
     * Route handler to return a list of disabled webhooks.
     *
     * @param WP_REST_Request $request JSON request for shipping endpoint.
     *
     * @return array|WP_Error|WP_REST_Response
     */
    public function callback( $request ) {
        $webhook_ids = \fastwc_get_disabled_webhooks();
        $webhooks    = array();

        foreach ( $webhook_ids as $webhook_id ) {
            $webhook = \wc_get_webhook( $webhook_id );

            if ( ! empty( $webhook ) ) {
                $webhooks[] = $webhook->get_data();
            }
        }

    	return new \WP_REST_Response( $webhooks, 200 );
    }
}
