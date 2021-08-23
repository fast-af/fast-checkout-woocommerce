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

$fastwc_debug_mode_status = empty( $fastwc_debug_mode ) ? $status_enabled : $status_disabled;
$fastwc_debug_mode_class  = empty( $fastwc_debug_mode ) ? 'yes-alt' : 'dismiss';
$fastwc_test_mode_status  = empty( $fastwc_test_mode ) ? $status_enabled : $status_disabled;
$fastwc_test_mode_class   = empty( $fastwc_test_mode ) ? 'yes-alt' : 'dismiss';
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
			<tr>
				<td>
					<p><strong><?php esc_html_e( 'Fast App ID', 'fast' ); ?></strong></p>
				</td>
				<td>
					<?php if ( ! empty( $fastwc_setting_app_id ) ) : ?>
						<p><span class="dashicons dashicons-yes-alt"></span> <?php echo esc_html( $fastwc_setting_app_id ); ?></p>
					<?php else : ?>
						<p><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'No Fast App ID has been entered.', 'fast' ); ?></p>
					<?php endif; ?>
				</td>
			</tr>

			<tr>
				<td>
					<p><strong><?php esc_html_e( 'Test Mode', 'fast' ); ?></strong></p>
				</td>
				<td>
					<p><span class="dashicons dashicons-<?php echo esc_attr( $fastwc_test_mode_class ); ?>"></span> <?php echo esc_html( $fastwc_test_mode_status ); ?></p>
				</td>
			</tr>

			<tr>
				<td>
					<p><strong><?php esc_html_e( 'Debug Mode', 'fast' ); ?></strong></p>
				</td>
				<td>
					<p><span class="dashicons dashicons-<?php echo esc_attr( $fastwc_debug_mode_class ); ?>"></span> <?php echo esc_html( $fastwc_debug_mode_status ); ?></p>
				</td>
			</tr>

			<tr>
				<td>
					<p><strong><?php esc_html_e( 'Webhooks', 'fast' ); ?></strong></p>
				</td>
				<td>
					<?php if ( empty( $fastwc_disabled_webhooks ) ) : ?>
						<p><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'All Fast webhooks are enabled.', 'fast' ); ?></p>
					<?php else : ?>
						<p><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'The following Fast WooCommerce webhooks are disabled', 'fast' ); ?>:</p>
						<ul class="ul-disc">
						<?php foreach ( $fastwc_disabled_webhooks as $webhook_topic => $webhook_id ) : ?>
							<li><?php echo esc_html( $webhook_topic ); ?></li>
						<?php endforeach ?>
						</ul>
					<?php endif; ?>
					<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' ) ); ?>"><?php esc_html_e( 'View all WooCommerce webhooks', 'fast' ); ?></a></p>
				</td>
			</tr>
		</tbody>
	</table>
</div>
