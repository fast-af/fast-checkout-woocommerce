<?php
/**
 * Fast checkout button template.
 *
 * @package Fast
 */

$fast_app_id = fast_get_app_id();
$currency    = get_woocommerce_currency();
?>
	<fast-checkout-button
		app_id="<?php echo esc_attr( $fast_app_id ); ?>"
		currency="<?php echo esc_attr( $currency ); ?>"
	></fast-checkout-button>
