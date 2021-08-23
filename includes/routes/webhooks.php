<?php
/**
 * Provides an API that exposes a list of disabled Fast webhooks.
 *
 * @package Fast
 */

/**
 * 
 */
function fastwc_route_get_webhooks_status() {
    $webhook_ids = fastwc_get_disabled_webhooks();
    $webhooks    = array();

    foreach ( $webhook_ids as $webhook_id ) {
        $webhook = wc_get_webhook( $webhook_id );

        if ( ! empty( $webhook ) ) {
            $webhooks[] = $webhook;
        }
    }

	return new WP_REST_Response( $webhooks, 200 );
}
