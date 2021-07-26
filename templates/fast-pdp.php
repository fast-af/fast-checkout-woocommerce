<?php
/**
 * Fast PDP checkout template.
 *
 * @package Fast
 */

$fastwc_pdp_button_hook = fastwc_get_pdp_button_hook();
?>

		<div class="fast-pdp-wrapper <?php echo esc_attr( $fastwc_pdp_button_hook ); ?>">
			<?php if ( 'woocommerce_after_add_to_cart_button' === $fastwc_pdp_button_hook ) : ?>
			<div class="fast-pdp-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
			<?php endif; ?>
			<?php fastwc_load_template( 'buttons/fast-checkout-button' ); ?>
			<?php if ( 'woocommerce_after_add_to_cart_button' !== $fastwc_pdp_button_hook ) : ?>
			<div class="fast-pdp-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
			<?php endif; ?>
		</div>
