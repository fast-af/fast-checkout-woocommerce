<?php
/**
 * Fast third-party plugin class for WooCommerce Tiered Pricing Table.
 *
 * @see https://en-gb.wordpress.org/plugins/tier-pricing-table/
 *
 * @package Fast
 */

namespace FastWC\Third_Party;

/**
 * Fast base third-party plugin class.
 */
class WooCommerce_Tiered_Pricing_Table extends Plugin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $slug = 'tier-pricing-table/tier-pricing-table.php';

	/**
	 * Initialize the plugin.
	 */
	protected function init() {
		\add_action( 'woocommerce_rest_set_order_item', array( $this, 'woocommerce_rest_set_order_item' ), 10, 2 );
	}

	/**
	 * Get the setting title.
	 *
	 * @return string
	 */
	protected function get_setting_title() {
		return \__( 'WooCommerce Tiered Pricing Table', 'fast' );
	}

	/**
	 * Get the setting description.
	 *
	 * @return string
	 */
	protected function get_setting_description() {
		return \__( 'Activate tools to add support for the WooCommerce Tiered Pricing Table plugin', 'fast' );
	}

	/**
	 * Update the item price with the tiered price based on quantity when order created through REST API.
	 * Mitigates a conflict with the WooCommerce Tiered Price Table plugin.
	 *
	 * @see https://en-gb.wordpress.org/plugins/tier-pricing-table/
	 *
	 * @param mixed $item   The order item.
	 * @param array $posted The item provided in the request body.
	 */
	public function woocommerce_rest_set_order_item( $item, $posted ) {
		// Do nothing if this class doesn't exist. If it is not there, that means the plugin is not installed.
		if ( ! class_exists( '\TierPricingTable\PriceManager') ) {
			return;
		}

		if ( $item instanceof WC_Order_Item_Product ) {
			$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
			$qty        = $item->get_quantity();
			$new_price   = \TierPricingTable\PriceManager::getPriceByRules( $qty, $product_id );

			$new_price = $new_price ? $new_price : $item->get_product()->get_price();

			$item->get_product()->set_price( $new_price );
			$item->set_subtotal( $new_price * $qty );
			$item->set_total( $new_price * $qty );

			$item->save();
		}
	}
}
