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
	<?php endif; ?>

	<table class="form-table" role="presentation">
		<tbody>
			<?php if ( ! empty( $fastwc_setting_app_id ) ) : ?>
			<tr>
				<td>
					<strong><?php esc_html_e( 'Fast App ID', 'fast' ); ?></strong>
				</td>
				<td>
					<?php echo esc_html( $fastwc_setting_app_id ); ?></strong>
				</td>
			</tr>
			<?php endif; ?>

			<tr>
				<td>
					<strong><?php esc_html_e( 'Test Mode', 'fast' ); ?></strong>
				</td>
				<td>
					<?php echo esc_html( $fastwc_test_mode_status ); ?>
				</td>
			</tr>

			<tr>
				<td>
					<strong><?php esc_html_e( 'Debug Mode', 'fast' ); ?></strong>
				</td>
				<td>
					<?php echo esc_html( $fastwc_debug_mode_status ); ?>
				</td>
			</tr>

			<tr>
				<td>
					<strong><?php esc_html_e( 'Webhooks', 'fast' ); ?></strong>
				</td>
				<td>
					<?php if ( empty( $fastwc_disabled_webhooks ) ) : ?>
						<?php esc_html_e( 'All Fast webhooks are enabled.', 'fast' ); ?>
					<?php else : ?>
						<?php esc_html_e( 'The following Fast WooCommerce webhooks are disabled', 'fast' ); ?>:
						<ul class="ul-disc">
						<?php foreach ( $fastwc_disabled_webhooks as $webhook_topic => $webhook_id ) : ?>
							<li><?php echo esc_html( $webhook_topic ); ?></li>
						<?php endforeach ?>
						</ul>
					<?php endif; ?>
					<br />
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' ) ); ?>"><?php esc_html_e( 'View all WooCommerce webhooks', 'fast' ); ?></a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
