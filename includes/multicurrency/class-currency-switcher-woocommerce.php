<?php
/**
 * Add support for Currency Switcher for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/currency-switcher-woocommerce/
 *
 * @package Fast
 */

namespace FastWC\Multicurrency;

/**
 * Class to support Currency Switcher for WooCommerce.
 */
class Currency_Switcher_Woocommerce extends Base {

	/**
	 * Check if the third-party plugin is active.
	 *
	 * @return bool
	 */
	protected function is_active() {
		return class_exists( 'Alg_WC_Currency_Switcher' );
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

		$order_currency = fastwc_get_order_currency( $order );
		$new_price      = alg_get_product_price_by_currency( $price, $order_currency, $product, true );

		if ( ! empty( $new_price ) ) {
			return $new_price;
		}

		return $price;
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

		$rate_info['price'] = alg_get_product_price_by_currency_global( $rate_info['price'], $currency );
		if ( ! empty( $rate_info['taxes'] ) ) {
			$rate_taxes = $rate_info['taxes'];

			foreach ( $rate_taxes as $rate_tax_id => $rate_tax ) {
				$rate_info['taxes'][ $rate_tax_id ] = alg_get_product_price_by_currency_global( $rate_tax, $currency );
			}
		}

		return $rate_info;
	}
}
