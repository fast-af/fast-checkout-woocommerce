<?php
/**
 * Tools to manage Fast WooCommerce webhooks.
 *
 * @package Fast
 */

define( 'FASTWC_OPTION_DISABLED_WEBHOOKS', 'fastwc_disabled_webhooks' );

/**
 * Get a list of webhook topics used by Fast.
 *
 * @return array
 */
function fastwc_get_fast_webhook_topics() {
	return array(
		'order.deleted',
		'order.updated',
		'order.created',
		'product.deleted',
		'product.updated',
		'product.created',
	);
}

/**
 * Handle action when a webhook is disabled due to delivery failures.
 *
 * @param int $webhook_id The ID of the webhook that was disabled.
 */
function fastwc_woocommerce_webhook_disabled_due_delivery_failures( $webhook_id ) {
	$webhook = wc_get_webhook( $webhook_id );

	if ( fastwc_is_fast_webhook( $webhook ) ) {
		fastwc_log_disabled_webhook( $webhook );
	}
}
add_action( 'woocommerce_webhook_disabled_due_delivery_failures', 'fastwc_woocommerce_webhook_disabled_due_delivery_failures' );

/**
 * Handle the action when a webhook is saved to see if the webhook was disabled.
 *
 * @param int $webhook_id The ID of the webhook that was saved.
 */
function fastwc_woocommerce_webhook_options_save( $webhook_id ) {
	$webhook = wc_get_webhook( $webhook_id );

	// If the webhook is a Fast webhook and disabled, log it.
	if ( fastwc_is_fast_webhook( $webhook ) && 'disabled' === $webhook->get_status() ) {
		fastwc_log_disabled_webhook( $webhook );
	}

	// Get the list of disabled webhooks, and remove active webhooks from that list.
	fastwc_get_disabled_webhooks();
}
add_action( 'woocommerce_webhook_options_save', 'fastwc_woocommerce_webhook_options_save' );

/**
 * Check if the webhook is a fast webhook.
 *
 * @param WC_Webhook $webhook The webhook to check.
 */
function fastwc_is_fast_webhook( $webhook ) {
	$fast_app_id   = fastwc_get_app_id();

	// If there is no Fast App ID, then the webhook is not a Fast webhook.
	if ( emtpy( $fast_app_id ) ) {
		return false;
	}

	$fast_webhooks = fastwc_get_fast_webhook_topics();

	// First, make sure that the webhook is a Fast webhook.
	if (
		! empty( $webhook )
		&& in_array( $webhook->get_topic(), $fast_webhooks, true )
		&& strpos( $webhook->get_delivery_url(), $fast_app_id )
	) {
		return true;
	}

	return false;
}

/**
 * Fetch the list of disabled webhooks.
 *
 * @return array
 */
function fastwc_get_disabled_webhooks() {
	$disabled_webhooks = get_option( FASTWC_OPTION_DISABLED_WEBHOOKS, array() );

	if ( ! empty( $disabled_webhooks ) ) {
		// Make sure that each webhook is still disabled.
		foreach ( $disabled_webhooks as $webhook_topic => $webhook_id ) {
			$webhook = wc_get_webhook( $webhook_id );

			if ( ! empty( $webhook ) && 'disabled' !== $webhook->get_status() ) {
				unset( $disabled_webhooks[ $webhook_topic ] );
			}
		}
	}

	return $disabled_webhooks;
}

/**
 * Check to see if all Fast webhooks are installed and active.
 *
 * @return bool
 */
function fastwc_woocommerce_has_fast_webhooks() {
	// First, make sure the WC_Webhook_Data_Store class exists.
	if ( ! class_exists( 'WC_Webhook_Data_Store' ) ) {
		return false;
	}

	$fast_app_id = fastwc_get_app_id();

	// If there is no Fast App ID, there is no way to check for valid webhooks.
	if ( empty( $fast_app_id ) ) {
		return false;
	}

	gloal $wpdb;

	$webhooks_topics = fastwc_get_fast_webhook_topics();

	// Create the WHERE clause of the query.
	$where_topic        = 'AND topic IN (' . implode( ',', $webhooks_topics ) . ')';
	$where_delivery_url = $wpdb->prepare( 'AND delivery_url LIKE %s', '%' . $wpdb->esc_like( sanitize_text_field( $fast_app_id ) )  '%' );

}

/**
 * Add a disabled webhook to the list of disabled webhooks.
 *
 * @param WC_Webhook $webhook The disabled webhook to add.
 */
function fastwc_log_disabled_webhook( $webhook ) {
	$fastwc_disabled_webhooks = fastwc_get_disabled_webhooks();

	// Make sure that the value is an array, or re-initialize it as an empty array.
	if ( ! is_array( $fastwc_disabled_webhooks ) ) {
		$fastwc_disabled_webhooks = array();
	}

	$fastwc_disabled_webhooks[ $webhook->get_topic() ] = $webhook->get_id();

	update_option( FASTWC_OPTION_DISABLED_WEBHOOKS, $fastwc_disabled_webhooks, 'no' );
}
