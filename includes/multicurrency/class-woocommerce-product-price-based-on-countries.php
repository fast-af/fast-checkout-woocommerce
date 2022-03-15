<?php
/**
 * Add support for Price Based on Country for WooCommerce plugin.
 *
 * @see https://wordpress.org/plugins/woocommerce-product-price-based-on-countries/
 *
 * @package fast
 */

namespace FastWC\Multicurrency;

/**
 * Class to support WooCommerce Currency Switcher.
 */
class Woocommerce_Product_Price_Based_On_Countries extends Base {

	/**
	 * Check if the third-party plugin is active.
	 *
	 * @return bool
	 */
	protected function is_active() {
		return class_exists( 'WC_Product_Price_Based_Country' );
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
		\fastwc_log_debug( 'Price Based on Country multicurrency plugin - Update price' );
		\fastwc_log_debug( 'Price Before Conversion: ' . $price );

		$zone = $this->get_zone( $request );
		\fastwc_log_debug( 'Zone: ' . print_r( $zone, true ) ); // phpcs:ignore

		if ( ! empty( $zone ) ) {
			\fastwc_log_debug( 'Setting price for zone' );
			$price = $zone->get_post_price( $product->get_id(), '_price' );
		}

		\fastwc_log_debug( 'Price After Conversion: ' . $price );

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
		$country = $this->get_country( $request );

		\fastwc_log_debug( 'Price Based on Country multicurrency plugin - Update shipping' );
		\fastwc_log_debug( 'Shipping Before Conversion: ' . print_r( $rate_info, true ) ); // phpcs:ignore

		$zone = $this->get_zone( $request );
		\fastwc_log_debug( 'Zone: ' . print_r( $zone, true ) ); // phpcs:ignore

		if ( ! empty( $zone ) ) {
			$rate_info['price'] = $zone->get_exchange_rate_price( $rate_info['price'] );

			if ( ! empty( $rate_info['taxes'] ) ) {
				$rate_taxes = $rate_info['taxes'];

				foreach ( $rate_taxes as $rate_tax_id => $rate_tax ) {
					$rate_info['taxes'][ $rate_tax_id ] = $zone->get_exchange_rate_price( $rate_tax );
				}
			}
		}

		\fastwc_log_debug( 'Shipping After Conversion: ' . print_r( $rate_info, true ) ); // phpcs:ignore

		return $rate_info;
	}

	/**
	 * Update the order for multicurrency.
	 *
	 * @param WC_Data         $order                The order to check.
	 * @param WP_REST_Request $request              Request object.
	 */
	protected function do_update_order( $order, $request ) {
		// Make sure the order currency is correct for the billing address zone.
		$country = $this->get_country( $request );

		if ( ! empty( $country ) ) {
			$zone           = $this->get_zone( $request, false );
			$order_currency = \fastwc_get_order_currency( $order );
			$zone_currency  = $zone->get_currency();

			// Change the order currency if it does not match the zone currenncy.
			if ( ! empty( $zone ) && $order_currency !== $zone_currency ) {
				$order->set_currency( $zone_currency );
			}
		}

		return $order;
	}

	/**
	 * Get the billing address country from the request.
	 *
	 * @param mixed $request The request object.
	 *
	 * @return string
	 */
	protected function get_country( $request ) {
		$country = '';

		$valid_based_on = array( 'billing', 'shipping' );
		$based_on       = \get_option( 'wc_price_based_country_based_on', 'billing' );

		// Make sure based on is billing or shipping.
		if ( ! in_array( $based_on, $valid_based_on, true ) ) {
			$based_on = 'billing';
		}

		if ( is_array( $request ) ) {
			if ( ! empty( $request[ $based_on ]['country'] ) ) {
				$country = $request[ $based_on ]['country'];
			}
		} elseif ( is_a( $request, 'WP_REST_Request' ) ) {
			$params = $request->get_params();

			if ( ! empty( $params[ $based_on ]['country'] ) ) {
				$country = $params[ $based_on ]['country'];
			}
		}

		return $country;
	}

	/**
	 * Get the pricing zone from the request.
	 *
	 * @param mixed $request              The request object.
	 * @param bool  $get_zone_by_currency Optional. Flag to get zone by currency.
	 *
	 * @return WCPBC_Pricing_Zone|WCPBC_Pricing_Zone_Pro
	 */
	protected function get_zone( $request, $get_zone_by_currency = true ) {
		$country = $this->get_country( $request );

		\fastwc_log_debug( 'Country: ' . $country );

		$zone = false;

		if ( ! empty( $country ) ) {
			$zone = \wcpbc_get_zone_by_country( $country );
			\fastwc_log_debug( 'Zone by country: ' . print_r( $zone, true ) ); // phpcs:ignore
		}

		// Maybe get the zone by currency.
		if (
			$get_zone_by_currency &&
			(
				empty( $zone ) ||
				$order_currency !== $zone->get_currency()
			)
		) {
			$zones = \WCPBC_Pricing_Zones::get_zones();

			// Loop through the zones and get a zone by the currency.
			foreach ( $zones as $_zone ) {
				if ( $order_currency === $_zone->get_currency() ) {
					$zone = $_zone;
					\fastwc_log_debug( 'Zone by currency: ' . print_r( $zone, true ) ); // phpcs:ignore

					break;
				}
			}
		}

		return $zone;
	}
}
