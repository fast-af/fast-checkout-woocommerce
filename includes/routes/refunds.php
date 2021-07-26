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

	if ( 'full' === $refund_type ) {
		$orders = fastwc_get_orders_with_full_refunds( $request );
	} else {
		$orders = fastwc_get_all_orders_with_refunds( $request, $refund_type );
	}

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
 * Get orders with full refunds.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 * @param bool            $get_ids Flag to fetch IDs.
 *
 * @return array
 */
function fastwc_get_orders_with_full_refunds( $request, $get_ids = false ) {
	$args   = fastwc_get_orders_query_args( $request, $get_ids );
	$orders = wc_get_orders( $args );

	// Convert objects to their data.
	if ( ! empty( $orders ) && false === $get_ids ) {
		$orders_data = array();

		foreach ( $orders as $order ) {
			$orders_data[] = $order->get_data();
		}

		$orders = $orders_data;
	}

	return $orders;
}

/**
 * Get the query args for orders with full refunds.
 *
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 * @param bool            $get_ids Flag to fetch IDs.
 *
 * @return array
 */
function fastwc_get_orders_query_args( $request, $get_ids = fasle ) {
	// Set the default query args.
	$args = array(
		'limit'    => isset( $request['per_page'] ) ? $request['per_page'] : -1,
		'order'    => isset( $request['order'] ) ? $request['order'] : 'DESC',
		'orderby'  => isset( $request['orderby'] ) ? $request['orderby'] : 'date',
		'paged'    => isset( $request['page'] ) ? $request['page'] : '',
		'exclude'  => isset( $request['exclude'] ) ? $request['exclude'] : array(),
		'paginate' => isset( $request['paginate'] ) ? $request['paginate'] : false,
	);

	if ( isset( $request['date'] ) ) {
		$args['date_created'] = $request['date'];
	} elseif ( isset( $request['after'] ) ) {
		$args['date_created'] = '>' . strtotime( $request['after'] );
	} elseif ( isset( $request['before'] ) ) {
		$args['date_created'] = '<' . strtotime( $request['before'] );
	}

	$args['return'] = $get_ids ? 'ids' : 'objects';
	$args['status'] = array( 'wc-refunded' );

	if ( 'id' === $args['orderby'] ) {
		$args['orderby'] = 'ID'; // ID must be capitalized.
	}

	return $args;
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

	// For partial refunds, exclude full refunded orders from the list.
	$refund_type = fastwc_get_refund_type( $request );
	if ( 'partial' === $refund_type ) {
		// Get ID's of orders with full refunds.
		$full_refund_orders = fastwc_get_orders_with_full_refunds( $request, true );

		if ( ! empty( $full_refund_orders ) ) {
			if ( empty( $query_args['post_parent__not_in'] ) ) {
				$query_args['post_parent__not_in'] = $full_refund_orders;
			} else {
				$query_args['post_parent__not_in'] = array_merge(
					$query_args['post_parent__not_in'],
					$full_refund_orders
				);
			}
		}
	}

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
