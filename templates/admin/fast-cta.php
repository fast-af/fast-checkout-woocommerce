<?php
/**
 * Render the "Become a Seller" CTA.
 *
 * @package Fast
 */

$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

$cta_context = ! empty( $args['context'] ) ? $args['context'] : 'empty';
$cta_message = __( 'The first step to integrating Fast Checkout with your WooCommerce store is to become a seller with Fast. If you already have a seller account and an App ID, you can enter your App ID in the App Info tab.', 'fast' );
if ( 'tab-fast_app_info' === $cta_context ) {
	$cta_message = __( 'The first step to integrating Fast Checkout with your WooCommerce store is to become a seller with Fast. If you already have a seller account and an App ID, you can enter your App ID below.', 'fast' );
}
?>

	<div class="fast-notice fast-notice-success">
		<h2><?php esc_html_e( 'Welcome to Fast!', 'fast' ); ?></h2>
		<p><?php echo esc_html( $cta_message ); ?></p>
		<p>
			<a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" class="button button-primary" target="_blank" rel="noopener">
				<?php esc_html_e( 'Become a seller on Fast.co', 'fast' ); ?> →
			</a>
		</p>
	</div>