<?php
/**
 * Fast checkout template.
 *
 * @package Fast
 */

$fast_checkout_button_styles = fast_get_option_or_set_default( FAST_SETTING_CHECKOUT_BUTTON_STYLES, FAST_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-checkout-wrapper">
			<?php fast_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-checkout-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
			<?php echo esc_html( $fast_checkout_button_styles ); ?>
		</style>
