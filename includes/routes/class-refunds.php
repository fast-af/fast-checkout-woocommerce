<?php
/**
 * Refunds API. Provides a list of orders that have refunds.
 * Includes partial and complete refunds.
 *
 * @package Fast
 */

namespace FastWC\Routes;

/**
 * Fast WooCommerce refunds route.
 */
class Refunds extends Base {

	/**
	 * Route name.
	 *
	 * @var string
	 */
	protected $route = 'refunds';

	/**
	 * Returns an array of order IDs that have a refund associated with the order.
	 *
	 * @param WP_REST_Request $request JSON request for shipping endpoint.
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function callback( $request ) {
		$this->request = $request;

		$refund_type = $this->get_refund_type();

		if ( 'full' === $refund_type ) {
			$orders = $this->get_orders_with_full_refunds();
		} else {
			$orders = $this->get_all_orders_with_refunds( $refund_type );
		}

		return new WP_REST_Response( $orders );
	}

	/**
	 * Get the requested refund type.
	 *
	 * @return string
	 */
	protected function get_refund_type() {
		$refund_types = array( 'partial', 'full', 'all' );
		$refund_type  = 'all';

		if (
			! empty( $this->request['refund_type'] ) &&
			in_array( $this->request['refund_type'], $refund_types, true )
		) {
			$refund_type = $this->request['refund_type'];
		}

		return $refund_type;
	}

	/**
	 * Get orders with full refunds.
	 *
	 * @param bool $get_ids Flag to fetch IDs.
	 *
	 * @return array
	 */
	protected function get_orders_with_full_refunds( $get_ids = false ) {
		$args   = $this->get_orders_query_args( $get_ids );
		$orders = \wc_get_orders( $args );

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
	 * @param bool $get_ids Flag to fetch IDs.
	 *
	 * @return array
	 */
	protected function get_orders_query_args( $get_ids = fasle ) {
		// Set the default query args.
		$args = array(
			'limit'    => isset( $this->request['per_page'] ) ? $this->request['per_page'] : -1,
			'order'    => isset( $this->request['order'] ) ? $this->request['order'] : 'DESC',
			'orderby'  => isset( $this->request['orderby'] ) ? $this->request['orderby'] : 'date',
			'paged'    => isset( $this->request['page'] ) ? $this->request['page'] : '',
			'exclude'  => isset( $this->request['exclude'] ) ? $this->request['exclude'] : array(),
			'paginate' => isset( $this->request['paginate'] ) ? $this->request['paginate'] : false,
		);

		if ( isset( $this->request['date'] ) ) {
			$args['date_created'] = $this->request['date'];
		} elseif ( isset( $this->request['after'] ) ) {
			$args['date_created'] = '>' . strtotime( $this->request['after'] );
		} elseif ( isset( $this->request['before'] ) ) {
			$args['date_created'] = '<' . strtotime( $this->request['before'] );
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
	 * @param string $refund_type Flag to get partial, full, or all refunds.
	 *
	 * @return array
	 */
	protected function get_all_orders_with_refunds( $refund_type ) {
		$refunds = $this->get_refunds();
		$orders  = $this->parse_refunds( $refunds, $refund_type );

		return $orders;
	}

	/**
	 * Get the query args to use for fetching refunds.
	 *
	 * @return array
	 */
	protected function get_refunds_query_args() {
		// Set the default query args.
		$query_args = array(
			'fields'              => 'id=>parent',
			'post_type'           => 'shop_order_refund',
			'post_status'         => 'any',
			'posts_per_page'      => isset( $this->request['per_page'] ) ? $this->request['per_page'] : -1,
			'order'               => isset( $this->request['order'] ) ? $this->request['order'] : '',
			'orderby'             => isset( $this->request['orderby'] ) ? $this->request['orderby'] : '',
			'paged'               => isset( $this->request['page'] ) ? $this->request['page'] : '',
			'post_parent__in'     => isset( $this->request['include'] ) ? $this->request['include'] : '',
			'post_parent__not_in' => isset( $this->request['exclude'] ) ? $this->request['exclude'] : '',
			'date_query'          => array(),
		);

		// For partial refunds, exclude full refunded orders from the list.
		$refund_type = $this->get_refund_type();
		if ( 'partial' === $refund_type ) {
			// Get ID's of orders with full refunds.
			$full_refund_orders = $this->get_orders_with_full_refunds( true );

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
		if ( isset( $this->request['before'] ) ) {
			$query_args['date_query'][0]['before'] = $this->request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $this->request['after'] ) ) {
			$query_args['date_query'][0]['after'] = $this->request['after'];
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
	 * @return WP_Query
	 */
	protected function get_refunds() {
		$query_args = $this->get_refunds_query_args();

		\add_filter( 'posts_distinct', array( $this, 'refunds_distinct' ) );
		\add_filter( 'posts_fields', array( $this, 'refunds_fields' ) );

		$refunds = new \WP_Query( $query_args );

		\remove_filter( 'posts_distinct', array( $this, 'refunds_distinct' ) );
		\remove_filter( 'posts_fields', array( $this, 'refunds_fields' ) );

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
	protected function parse_refunds( $refunds, $refund_type ) {
		$orders = array();

		if ( ! empty( $refunds->posts ) ) {
			foreach ( $refunds->posts as $refund ) {
				$order = \wc_get_order( $refund->post_parent );

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
	public function refunds_distinct() {
		return 'DISTINCT';
	}

	/**
	 * Swap the order of the fields in the query.
	 *
	 * @param string $fields The fields string from the query.
	 *
	 * @return string
	 */
	public function refunds_fields( $fields ) {
		$fields_arr = array_reverse( explode( ', ', $fields ) );

		return $fields_arr[0];
	}
}
