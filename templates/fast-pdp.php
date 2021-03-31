<?php
/**
 * Fast PDP checkout template.
 *
 * @package Fast
 */

$fastwc_pdp_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_PDP_BUTTON_STYLES, FASTWC_SETTING_PDP_BUTTON_STYLES_DEFAULT );
?>

		<div class="fast-pdp-wrapper">
			<?php fastwc_load_template( 'buttons/fast-checkout-button' ); ?>
			<div class="fast-pdp-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
		<style>
				<?php echo esc_html( $fastwc_pdp_button_styles ); ?>
		</style>
