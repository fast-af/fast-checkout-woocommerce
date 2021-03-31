<?php
/**
 * Fast mini cart checkout template.
 *
 * @package Fast
 */

$fastwc_mini_cart_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_MINI_CART_BUTTON_STYLES, FASTWC_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-mini-cart-wrapper">
			<?php fastwc_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-mini-cart-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
			<?php echo esc_html( $fastwc_mini_cart_button_styles ); ?>
		</style>
