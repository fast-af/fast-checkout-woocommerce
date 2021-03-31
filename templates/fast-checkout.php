<?php
/**
 * Fast checkout template.
 *
 * @package Fast
 */

$fastwc_checkout_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_CHECKOUT_BUTTON_STYLES, FASTWC_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-checkout-wrapper">
			<?php fastwc_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-checkout-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
			<?php echo esc_html( $fastwc_checkout_button_styles ); ?>
		</style>
