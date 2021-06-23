<?php
/**
 * Refunds API. Provides a list of orders that have refunds.
 * Includes partial and complete refunds.
 *
 * @package Fast
 */

/**
 * Returns an array of order IDs that have a refund associated with the order.
 *
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_get_orders_with_refunds() {
	$query_args = array(
		'fields'         => 'id=>parent',
		'post_type'      => 'shop_order_refund',
		'post_status'    => 'any',
		'posts_per_page' => -1,
	);

	$refunds = get_posts( $query_args );

	$order_ids = array_values( array_unique( $refunds ) );
	$orders    = array();

	foreach ( $order_ids as $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! empty( $order ) ) {
			$orders[] = $order->get_data();
		}
	}

	return new WP_REST_Response( $orders );
}
