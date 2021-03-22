<?php
/**
 * Fast cart checkout template.
 *
 * @package Fast
 */

$fast_cart_button_styles = fast_get_option_or_set_default( FAST_SETTING_CART_BUTTON_STYLES, FAST_SETTING_CART_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-cart-wrapper">
			<?php fast_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-cart-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
			<?php echo esc_html( $fast_cart_button_styles ); ?>
		</style>
