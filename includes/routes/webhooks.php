<?php
/**
 * Provides an API that exposes a list of disabled Fast webhooks.
 *
 * @package Fast
 */

/**
 * Route handler to return a list of disabled webhooks.
 */
function fastwc_route_get_disabled_webhooks() {
    $webhook_ids = fastwc_get_disabled_webhooks();
    $webhooks    = array();

    foreach ( $webhook_ids as $webhook_id ) {
        $webhook = wc_get_webhook( $webhook_id );

        if ( ! empty( $webhook ) ) {
            $webhooks[] = $webhook->get_data();
        }
    }

	return new WP_REST_Response( $webhooks, 200 );
}
