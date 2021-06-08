<?php
/**
 * Fast checkout button template.
 *
 * @package Fast
 */

$fastwc_app_id          = fastwc_get_app_id();
$currency               = get_woocommerce_currency();
$multicurrency_disabled = fastwc_is_multicurrency_support_disabled();
?>
	<fast-checkout-button
		app_id="<?php echo esc_attr( $fastwc_app_id ); ?>"
		<?php if ( false === $multicurrency_disabled ) : ?>
		currency="<?php echo esc_attr( $currency ); ?>"
		<?php endif; ?>
	></fast-checkout-button>
