<?php
/**
 * Add support for WooCommerce Currency Switcher plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-currency-switcher/
 *
 * @package fast
 */

namespace FastWC\Multicurrency;

/**
 * Class to support WooCommerce Currency Switcher.
 */
class Woocommerce_Currency_Switcher extends Base {

	/**
	 * Check if the third-party plugin is active.
	 *
	 * @return bool
	 */
	protected function is_active() {
		return class_exists( 'WOOCS_STARTER' );
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
		global $WOOCS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		$order_currency = fastwc_get_order_currency( $order );

		$_REQUEST['woocs_raw_woocommerce_price_currency'] = $order_currency;

		return $WOOCS->raw_woocommerce_price( $price, $product ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
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
		global $WOOCS; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		$_REQUEST['woocs_raw_woocommerce_price_currency'] = $currency;

		$rate_info['price'] = $WOOCS->raw_woocommerce_price( $rate_info['price'] ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		if ( ! empty( $rate_info['taxes'] ) ) {
			$rate_taxes = $rate_info['taxes'];

			foreach ( $rate_taxes as $rate_tax_id => $rate_tax ) {
				$rate_info['taxes'][ $rate_tax_id ] = $WOOCS->raw_woocommerce_price( $rate_tax ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			}
		}

		return $rate_info;
	}
}
