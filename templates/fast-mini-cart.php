<?php
/**
 * Fast mini cart checkout template.
 *
 * @package Fast
 */

$fast_mini_cart_button_styles = fast_get_option_or_set_default( FAST_SETTING_MINI_CART_BUTTON_STYLES, FAST_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-mini-cart-wrapper">
			<?php fast_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-mini-cart-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
			<?php echo esc_html( $fast_mini_cart_button_styles ); ?>
		</style>
