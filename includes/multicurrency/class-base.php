<?php
/**
 * Multicurrency support plugin base class.
 *
 * @package Fast
 */

namespace FastWC\Multicurrency;

/**
 * Base class for supporting third party multicurrency plugins.
 */
class Base {

	/**
	 * Create the class.
	 */
	public function __construct() {
		add_filter( 'fastwc_update_price_for_multicurrency', array( $this, 'update_price' ), 10, 4 );
		add_filter( 'fastwc_update_shipping_rate_for_multicurrency', array( $this, 'update_shipping' ), 10, 3 );
		add_filter( 'fastwc_update_order_for_multicurrency', array( $this, 'update_order' ), 10, 2 );
	}

	/**
	 * Check if the third-party plugin is active.
	 *
	 * @return bool
	 */
	protected function is_active() {
		return false;
	}

	/**
	 * Filter handler for updating the product price for multicurrency.
	 *
	 * @param string     $price   Value of the price.
	 * @param WC_Product $product The product object.
	 * @param WC_Data    $order   The order to check.
	 * @param WC_Request $request Request object.
	 *
	 * @return string
	 */
	public function update_price( $price, $product, $order, $request ) {

		if ( $this->is_active() ) {
			return $this->do_update_price( $price, $product, $order, $request );
		}

		return $price;
	}

	/**
	 * Update the product price for multicurrency.
	 *
	 * @param string     $price   Value of the price.
	 * @param WC_Product $product The product object.
	 * @param WC_Data    $order   The order to check.
	 * @param WC_Request $request Request object.
	 *
	 * @return string
	 */
	protected function do_update_price( $price, $product, $order, $request ) {
		return $price;
	}

	/**
	 * Filter handler for updating the shipping rate for multicurrency.
	 *
	 * @param array           $rate_info The rate response information.
	 * @param string          $currency  The customer currency.
	 * @param WP_REST_Request $request   The request object.
	 *
	 * @return array
	 */
	public function update_shipping( $rate_info, $currency, $request ) {

		if ( $this->is_active() ) {
			return $this->do_update_shipping( $rate_info, $currency, $request );
		}

		return $rate_info;
	}

	/**
	 * Update the shipping rate for multicurrency.
	 *
	 * @param array           $rate_info The rate response information.
	 * @param string          $currency  The customer currency.
	 * @param WP_REST_Request $request   The request object.
	 *
	 * @return array
	 */
	protected function do_update_shipping( $rate_info, $currency, $request ) {
		return $rate_info;
	}

	/**
	 * Filter handler for updating the order for multicurrency.
	 *
	 * @param WC_Data         $order                The order to check.
	 * @param WP_REST_Request $request              Request object.
	 */
	public function update_order( $order, $request ) {

		if ( $this->is_active() ) {
			return $this->do_update_order( $order, $request );
		}

		return $order;
	}

	/**
	 * Update the order for multicurrency.
	 *
	 * @param WC_Data         $order                The order to check.
	 * @param WP_REST_Request $request              Request object.
	 */
	protected function do_update_order( $order, $request ) {
		return $order;
	}
}
