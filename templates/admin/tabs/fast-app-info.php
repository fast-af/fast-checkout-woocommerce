<?php
/**
 * Fast settings App Info tab template.
 *
 * @package Fast
 */

$fastwc_setting_app_id              = fastwc_get_app_id();
$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );
?>
<form method="post" action="options.php">
	<?php if ( empty( $fastwc_setting_app_id ) ) : ?>
		<div class="fast-notice fast-notice-success">
			<h2><?php esc_html_e( 'Welcome to Fast!', 'fast' ); ?></h2>
			<p><?php esc_html_e( 'The first step to integrating Fast Checkout with your WooCommerce store is to become a seller with Fast. If you already have a seller account and an App ID, you can enter your App ID below.', 'fast' ); ?></p>
			<p>
				<a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" class="button button-primary" target="_blank" rel="noopener">
					<?php esc_html_e( 'Become a seller on Fast.co', 'fast' ); ?> →
				</a>
			</p>
		</div>
	<?php endif; ?>

	<?php
	settings_fields( 'fast_app_info' );
	do_settings_sections( 'fast_app_info' );
	submit_button();
	?>
</form>
