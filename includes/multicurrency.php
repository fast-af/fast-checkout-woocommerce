<?php
/**
 * Add support for multicurrency functionality.
 *
 * @package fast
 */

// Add base multicurrency class.
require_once FASTWC_PATH . 'includes/multicurrency/class-base.php';
// Add support for Currency Switcher for WooCommerce plugin.
require_once FASTWC_PATH . 'includes/multicurrency/class-currency-switcher-woocommerce.php';
// Add support for WooCommerce Currency Switcher plugin.
require_once FASTWC_PATH . 'includes/multicurrency/class-woocommerce-currency-switcher.php';
// Add support for Price Based on Country for WooCommerce plugin.
require_once FASTWC_PATH . 'includes/multicurrency/class-woocommerce-product-price-based-on-countries.php';

/**
 * Checks if multicurrency support is disabled.
 *
 * @return bool True if multicurrency support is disabled. False otherwise.
 */
function fastwc_is_multicurrency_support_disabled() {
	$fastwc_disable_multicurrency = get_option( FASTWC_SETTING_DISABLE_MULTICURRENCY, false );

	if ( ! empty( $fastwc_disable_multicurrency ) ) {
		return true;
	}

	return false;

}

/**
 * Maybe update order for multicurrency.
 *
 * @param WC_Data         $order    The order to check.
 * @param WP_REST_Request $request  Request object.
 *
 * @return WC_Data
 */
function fastwc_maybe_update_order_for_multicurrency( $order, $request ) {
	$multicurrency_disabled = fastwc_is_multicurrency_support_disabled();

	// Do nothing if multicurrency is disabled.
	if ( false === $multicurrency_disabled ) {
		$wc_currency    = get_woocommerce_currency();
		$order_currency = fastwc_get_order_currency( $order );

		if (
			! empty( $order_currency ) // Make sure the order currency is set.
			&& $wc_currency !== $order_currency // Make sure the order currency is not the default currency.
		) {
			$order = fastwc_update_order_for_multicurrency( $order, $request );
		}
	}

	return $order;
}

/**
 * Get the order currency.
 *
 * @param WC_Data $order The order to check.
 *
 * @return string
 */
function fastwc_get_order_currency( $order ) {
	$wc_currency = get_woocommerce_currency();

	return method_exists( $order, 'get_currency' ) ? $order->get_currency() : $wc_currency;
}

/**
 * Update the order for multicurrency.
 *
 * @param WC_Data         $order                The order to check.
 * @param WP_REST_Request $request              Request object.
 *
 * @return WC_Data
 */
function fastwc_update_order_for_multicurrency( $order, $request ) {

	/**
	 * Maybe update the order from the multicurrency plugin.
	 *
	 * @param WC_Data         $order                The order to check.
	 * @param WP_REST_Request $request              Request object.
	 */
	$order = apply_filters(
		'fastwc_update_order_for_multicurrency',
		$order,
		$request
	);

	foreach ( $order->get_items() as $item_id => $item ) {
		$product  = method_exists( $item, 'get_product' ) ? $item->get_product() : null;
		$quantity = method_exists( $item, 'get_quantity' ) ? (int) $item->get_quantity() : 0;

		if ( ! empty( $product ) ) {
			/**
			 * Get the price from the multicurrency plugin.
			 *
			 * @param string     $price   Value of the price.
			 * @param WC_Product $product The product object.
			 * @param WC_Data    $order   The order to check.
			 * @param WC_Request $request Request object.
			 */
			$price = apply_filters(
				'fastwc_update_price_for_multicurrency',
				$product->get_price(),
				$product,
				$order,
				$request
			);

			// Calculate the price by multiplying by quantity.
			$new_price = $price * $quantity;

			// Set the new price.
			$item->set_subtotal( $new_price );
			$item->set_total( $new_price );

			// Make new tax calculations.
			$item->calculate_taxes();

			// Save the item data.
			$item->save();
		}
	}

	$order->calculate_totals();

	return $order;
}

/**
 * Maybe update shipping rates for multicurrency.
 *
 * @param array           $rate_info   The rate response information.
 * @param string          $wc_currency The default WooCommerce currency.
 * @param string          $currency    The customer currency.
 * @param WP_REST_Request $request     The request object.
 *
 * @return array
 */
function fastwc_maybe_update_shipping_rate_for_multicurrency( $rate_info, $wc_currency, $currency, $request ) {
	$multicurrency_disabled = fastwc_is_multicurrency_support_disabled();

	// Do nothing if multicurrency is disabled.
	if (
		false === $multicurrency_disabled
		&& ! empty( $currency ) // Make sure the customer currency is set.
		&& $wc_currency !== $currency // Make sure the customer currency is not the default currency.
	) {
		/**
		 * Update shipping rates for multicurrency.
		 *
		 * @param array           $rate_info The rate response information.
		 * @param string          $currency  The customer currency.
		 * @param WP_REST_Request $request   The request object.
		 *
		 * @return array
		 */
		$rate_info = apply_filters(
			'fastwc_update_shipping_rate_for_multicurrency',
			$rate_info,
			$currency,
			$request
		);
	}

	return $rate_info;
}
