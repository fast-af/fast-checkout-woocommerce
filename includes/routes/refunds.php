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
	$refund_type  = 'all';

	if (
		! empty( $request['refund_type'] ) &&
		in_array( $request['refund_type'], $refund_types, true )
	) {
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
	$refunds = fastwc_get_refunds( $request );
	$orders  = fastwc_parse_refunds( $refunds, $refund_type );

	return $orders;
}

/**
 * Get the query args to use for fetching refunds.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return array
 */
function fastwc_get_refunds_query_args( $request ) {
	// Set the default query args.
	$query_args = array(
		'fields'              => 'id=>parent',
		'post_type'           => 'shop_order_refund',
		'post_status'         => 'any',
		'posts_per_page'      => isset( $request['per_page'] ) ? $request['per_page'] : -1,
		'order'               => isset( $request['order'] ) ? $request['order'] : '',
		'orderby'             => isset( $request['orderby'] ) ? $request['orderby'] : '',
		'paged'               => isset( $request['page'] ) ? $request['page'] : '',
		'post_parent__in'     => isset( $request['include'] ) ? $request['include'] : '',
		'post_parent__not_in' => isset( $request['exclude'] ) ? $request['exclude'] : '',
		'date_query'          => array(),
	);

	if ( 'date' === $query_args['orderby'] ) {
		$query_args['orderby'] = 'date ID';
	}

	// Set before into date query. Date query must be specified as an array of an array.
	if ( isset( $request['before'] ) ) {
		$query_args['date_query'][0]['before'] = $request['before'];
	}

	// Set after into date query. Date query must be specified as an array of an array.
	if ( isset( $request['after'] ) ) {
		$query_args['date_query'][0]['after'] = $request['after'];
	}

	if ( 'include' === $query_args['orderby'] ) {
		$query_args['orderby'] = 'post__in';
	} elseif ( 'id' === $query_args['orderby'] ) {
		$query_args['orderby'] = 'ID'; // ID must be capitalized.
	} elseif ( 'slug' === $query_args['orderby'] ) {
		$query_args['orderby'] = 'name';
	}

	return $query_args;
}

/**
 * Get the list of refunds.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return WP_Query
 */
function fastwc_get_refunds( $request ) {
	$query_args = fastwc_get_refunds_query_args( $request );

	add_filter( 'posts_distinct', 'fastwc_refunds_distinct' );
	add_filter( 'posts_fields', 'fastwc_refunds_fields' );

	$refunds = new WP_Query( $query_args );

	remove_filter( 'posts_distinct', 'fastwc_refunds_distinct' );
	remove_filter( 'posts_fields', 'fastwc_refunds_fields' );

	return $refunds;
}

/**
 * Parse the list of refunds and return a list of orders.
 *
 * @param WP_Query $refunds     The list of refunds.
 * @param string   $refund_type Flag to get partial, full, or all refunds.
 *
 * @return array
 */
function fastwc_parse_refunds( $refunds, $refund_type ) {
	$orders = array();

	if ( ! empty( $refunds->posts ) ) {
		foreach ( $refunds->posts as $refund ) {
			$order = wc_get_order( $refund->post_parent );

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
	}

	return $orders;
}

/**
 * Make sure to return distinct values on the refunds.
 *
 * @return string
 */
function fastwc_refunds_distinct() {
	return 'DISTINCT';
}

/**
 * Swap the order of the fields in the query.
 *
 * @param string $fields The fields string from the query.
 *
 * @return string
 */
function fastwc_refunds_fields( $fields ) {
	$fields_arr = array_reverse( explode( ', ', $fields ) );

	return $fields_arr[0];
}
