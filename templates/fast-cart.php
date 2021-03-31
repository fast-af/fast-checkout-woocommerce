<?php
/**
 * Fast cart checkout template.
 *
 * @package Fast
 */

$fastwc_cart_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_CART_BUTTON_STYLES, FASTWC_SETTING_CART_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-cart-wrapper">
			<?php fastwc_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-cart-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
			<?php echo esc_html( $fastwc_cart_button_styles ); ?>
		</style>
