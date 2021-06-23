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
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_get_orders_with_refunds( WP_REST_Request $request ) {
	$refund_type = fastwc_get_refund_type( $request );

	$orders = fastwc_get_all_orders_with_refunds( $request, $refund_type );

	return new WP_REST_Response( $orders );
}

/**
 * Get the requested refund type.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return string
 */
function fastwc_get_refund_type( WP_REST_Request $request ) {
	$refund_types = array( 'partial', 'full', 'all' );
	$refund_type  = 'partial';

	if ( ! empty( $request['refund_type'] ) && in_array( $refund_type, $refund_types, true ) ) {
		$refund_type = $request['refund_type'];
	}

	return $refund_type;
}

/**
 * Get all orders with refunds.
 *
 * @param WP_REST_Request $request     JSON request for shipping endpoint.
 * @param string          $refund_type Flag to get partial, full, or all refunds.
 *
 * @return array
 */
function fastwc_get_all_orders_with_refunds( $request, $refund_type ) {
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
			$status = $order->get_status();

			if (
				( 'wc-refunded' === $status && 'full' === $refund_type ) ||
				( 'wc-refunded' !== $status && 'partial' === $refund_type ) ||
				'all' === $refund_type
			) {
				$orders[] = $order->get_data();
			}
		}
	}

	return $orders;
}
