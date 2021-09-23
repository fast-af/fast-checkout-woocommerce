<?php
/**
 * Tools to manage Fast WooCommerce webhooks.
 *
 * @package Fast
 */

define( 'FASTWC_OPTION_WEBHOOKS', 'fastwc_webhooks' );
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
	fastwc_maybe_log_disabled_webhook( $webhook_id );
}
add_action( 'woocommerce_webhook_disabled_due_delivery_failures', 'fastwc_woocommerce_webhook_disabled_due_delivery_failures' );

/**
 * Handle the action when a webhook is saved to see if the webhook was disabled.
 *
 * @param int $webhook_id The ID of the webhook that was saved.
 */
function fastwc_woocommerce_webhook_options_save( $webhook_id ) {
	fastwc_maybe_log_disabled_webhook( $webhook_id );
}
add_action( 'woocommerce_webhook_options_save', 'fastwc_woocommerce_webhook_options_save' );

/**
 * Check if the webhook is disabled.
 *
 * @param WC_Webhook $webhook The webhook to check.
 *
 * @return bool
 */
function fastwc_is_disabled_webhook( $webhook ) {
	return 'disabled' === $webhook->get_status();
}

/**
 * Check if the webhook is a fast webhook.
 *
 * @param WC_Webhook $webhook The webhook to check.
 *
 * @return bool
 */
function fastwc_is_fast_webhook( $webhook ) {
	$fast_app_id = fastwc_get_app_id();

	// If there is no Fast App ID, then the webhook is not a Fast webhook.
	if ( empty( $fast_app_id ) ) {
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
		$did_unset = false;
		// Make sure that each webhook is still disabled.
		foreach ( $disabled_webhooks as $webhook_topic => $webhook_id ) {
			$webhook = wc_get_webhook( $webhook_id );

			if ( ! empty( $webhook ) && ! fastwc_is_disabled_webhook( $webhook ) ) {
				unset( $disabled_webhooks[ $webhook_topic ] );
				$did_unset = true;
			}
		}

		if ( $did_unset ) {
			update_option( FASTWC_OPTION_DISABLED_WEBHOOKS, $disabled_webhooks, 'no' );
		}
	}

	return $disabled_webhooks;
}

/**
 * Get webhooks option.
 *
 * @return array
 */
function fastwc_get_webhooks_option() {
	return get_option( FASTWC_OPTION_WEBHOOKS, array() );
}

/**
 * Build a query to fetch webhooks.
 *
 * @return string
 */
function fastwc_build_webhook_query() {
	global $wpdb;

	$webhooks_option = fastwc_get_webhooks_option();

	if ( ! empty( $webhooks_option ) ) {
		$webhooks_ids = implode( ',', wp_parse_id_list( $webhooks_option ) );
		$where_ids    = 'AND webhook_id IN (' . $webhooks_ids . ')';

		$query = trim(
			"SELECT webhook_id
			FROM {$wpdb->prefix}wc_webhooks
			WHERE 1=1
			{$where_ids}"
		);
	} else {
		$webhooks_topics = fastwc_get_fast_webhook_topics();

		// Create the WHERE clause of the query.
		$topics_in          = array_map(
			function( $topic ) {
				$topic = sanitize_text_field( $topic );
				return "'$topic'";
			},
			$webhooks_topics
		);
		$where_topic        = 'AND topic IN (' . implode( ',', $topics_in ) . ')';
		$where_delivery_url = $wpdb->prepare( 'AND delivery_url LIKE %s', '%' . $wpdb->esc_like( sanitize_text_field( $fast_app_id ) ) . '%' );

		$query = trim(
			"SELECT webhook_id
			FROM {$wpdb->prefix}wc_webhooks
			WHERE 1=1
			{$where_topic}
			{$where_delivery_url}"
		);
	}

	return $query;
}

/**
 * Get a list of Fast webhooks.
 *
 * @return array
 */
function fastwc_get_fast_webhooks() {
	$fast_app_id = fastwc_get_app_id();

	// If there is no Fast App ID, there is no way to check for valid webhooks.
	if ( empty( $fast_app_id ) ) {
		return array();
	}

	$cache_key   = 'fast_webhooks_cache_' . $fast_app_id;
	$cache_group = 'fast_webhooks';
	$cache_value = wp_cache_get( $cache_key, $cache_group );

	if ( ! empty( $cache_value ) ) {
		$webhooks = $cache_value;
	} else {
		global $wpdb;

		$query    = fastwc_build_webhook_query();
		$webhooks = wp_parse_id_list( $wpdb->get_col( $query ) ); // phpcs:ignore

		wp_cache_set( $cache_key, $webhooks, $cache_group, HOUR_IN_SECONDS );
	}

	if ( ! empty( $webhooks ) ) {
		update_option( FASTWC_OPTION_WEBHOOKS, $webhooks, 'no' );

		foreach ( $webhooks as $webhook_id ) {
			fastwc_maybe_log_disabled_webhook( $webhook_id );
		}
	} else {
		delete_option( FASTWC_OPTION_WEBHOOKS );
	}

	return $webhooks;
}

/**
 * Maybe clear the cache on the Fast webhooks cache.
 */
function fastwc_maybe_clear_fast_webhooks_cache() {
	$fast_app_id               = fastwc_get_app_id();
	$fast_clear_webhooks_cache = isset( $_GET['fast_clear_webhooks_cache'] ) ? absint( $_GET['fast_clear_webhooks_cache'] ) : false; // phpcs:ignore

	if ( empty( $fast_app_id ) || ! $fast_clear_webhooks_cache ) {
		return;
	}

	$cache_key   = 'fast_webhooks_cache_' . $fast_app_id;
	$cache_group = 'fast_webhooks';

	wp_cache_delete( $cache_key, $cache_group );
	delete_option( FASTWC_OPTION_WEBHOOKS );
}
add_action( 'init', 'fastwc_maybe_clear_fast_webhooks_cache' );

/**
 * Check to see if all Fast webhooks are installed and active.
 *
 * @return bool
 */
function fastwc_woocommerce_has_fast_webhooks() {
	$fast_app_id = fastwc_get_app_id();

	// If there is no Fast App ID, there is no way to check for valid webhooks.
	if ( empty( $fast_app_id ) ) {
		return false;
	}

	$webhooks       = fastwc_get_fast_webhooks();
	$webhooks_count = ! empty( $webhooks ) ? count( $webhooks ) : 0;

	if ( 6 <= $webhooks_count ) {
		return true;
	}

	return false;
}

/**
 * Check if a webhook is disabled and log it.
 *
 * @param int $webhook_id The ID of the webhook to check and maybe log.
 */
function fastwc_maybe_log_disabled_webhook( $webhook_id ) {
	$webhook = wc_get_webhook( $webhook_id );

	// If the webhook is a Fast webhook and disabled, log it.
	if ( fastwc_is_fast_webhook( $webhook ) && fastwc_is_disabled_webhook( $webhook ) ) {
		fastwc_log_disabled_webhook( $webhook );
	}
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
