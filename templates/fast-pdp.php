<?php
/**
 * Fast PDP checkout template.
 *
 * @package Fast
 */

$fastwc_pdp_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_PDP_BUTTON_STYLES, FASTWC_SETTING_PDP_BUTTON_STYLES_DEFAULT );
$fastwc_pdp_button_hook   = fastwc_get_pdp_button_hook();
?>

		<div class="fast-pdp-wrapper">
			<?php if ( 'woocommerce_after_add_to_cart_button' === $fastwc_get_pdp_button_hook ) : ?>
			<div class="fast-pdp-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
			<?php endif; ?>
			<?php fastwc_load_template( 'buttons/fast-checkout-button' ); ?>
			<?php if ( 'woocommerce_after_add_to_cart_button' !== $fastwc_get_pdp_button_hook ) : ?>
			<div class="fast-pdp-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
			<?php endif; ?>
		</div>
		<style>
				<?php echo esc_html( $fastwc_pdp_button_styles ); ?>
		</style>
