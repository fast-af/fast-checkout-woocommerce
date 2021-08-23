<?php
/**
 * Fast settings Status tab template.
 *
 * @package Fast
 */

$fastwc_setting_app_id    = fastwc_get_app_id();
$fastwc_debug_mode        = get_option( FASTWC_SETTING_DEBUG_MODE, 0 );
$fastwc_test_mode         = get_option( FASTWC_SETTING_TEST_MODE, '1' );
$fastwc_disabled_webhooks = fastwc_get_disabled_webhooks();

$status_enabled  = __( 'enabled', 'fast' );
$status_disabled = __( 'disabled', 'fast' );

$fastwc_debug_mode_status = empty( $fastwc_debug_mode ) ? $status_disabled : $status_enabled;
$fastwc_test_mode_status  = empty( $fastwc_test_mode ) ? $status_disabled : $status_enabled;
?>

<div class="fast-status">
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
	<?php else : ?>
		<p><?php esc_html_e( 'Fast App ID', 'fast' ); ?>: <?php echo esc_html( $fastwc_setting_app_id ); ?></p>
	<?php endif; ?>

	<p><?php esc_html_e( 'Test Mode', 'fast' ); ?>: <strong><?php echo esc_html( $fastwc_test_mode_status ); ?></strong></p>
	<p><?php esc_html_e( 'Debug Mode', 'fast' ); ?>: <strong><?php echo esc_html( $fastwc_debug_mode_status ); ?></strong></p>

	<h3><?php esc_html_e( 'Webhooks', 'fast' ); ?></h3>
	<?php if ( empty( $fastwc_disabled_webhooks ) ) : ?>
		<p><?php esc_html_e( 'All Fast webhooks are enabled.', 'fast' ); ?></p>
	<?php else : ?>
		<p><?php esc_html_e( 'The following Fast webhooks are disabled', 'fast' ); ?>:</p>
		<ul>
		<?php foreach ( $fastwc_disabled_webhooks as $webhook_topic => $webhook_id ) : ?>
			<li><?php echo esc_html( $webhook_topic ); ?></li>
		<?php endforeach ?>
		</ul>
	<?php endif; ?>
</div>
