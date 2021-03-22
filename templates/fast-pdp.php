<?php
/**
 * Fast PDP checkout template.
 *
 * @package Fast
 */

$fast_pdp_button_styles = fast_get_option_or_set_default( FAST_SETTING_PDP_BUTTON_STYLES, FAST_SETTING_PDP_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-pdp-wrapper">
			<?php fast_load_template( 'buttons/fast-checkout-button' ); ?>
			<div class="fast-pdp-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
				<?php echo esc_html( $fast_pdp_button_styles ); ?>
		</style>
