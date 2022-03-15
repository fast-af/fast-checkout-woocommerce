<?php
/**
 * Fast settings Status tab template.
 *
 * @package Fast
 */

use FastWC\Config;

$fastwc_setting_app_id    = Config::get_app_id();
$fastwc_debug_mode        = Config::is_debug_mode();
$fastwc_test_mode         = Config::is_test_mode();
$fastwc_has_webhooks      = fastwc_woocommerce_has_fast_webhooks();
$fastwc_disabled_webhooks = fastwc_get_disabled_webhooks();

$status_enabled  = __( 'enabled', 'fast' );
$status_disabled = __( 'disabled', 'fast' );

$fastwc_debug_mode_status = empty( $fastwc_debug_mode ) ? $status_disabled : $status_enabled;
$fastwc_debug_mode_class  = empty( $fastwc_debug_mode ) ? 'dismiss' : 'yes-alt';
$fastwc_test_mode_status  = empty( $fastwc_test_mode ) ? $status_disabled : $status_enabled;
$fastwc_test_mode_class   = empty( $fastwc_test_mode ) ? 'dismiss' : 'yes-alt';
?>

<div class="fast-status">

	<?php fastwc_maybe_render_cta(); ?>

	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<td style="vertical-align: top;">
					<p><strong><?php esc_html_e( 'Fast App ID', 'fast' ); ?></strong></p>
				</td>
				<td style="vertical-align: top;">
					<?php if ( ! empty( $fastwc_setting_app_id ) ) : ?>
						<p><span class="dashicons dashicons-yes-alt"></span> <?php echo esc_html( $fastwc_setting_app_id ); ?></p>
					<?php else : ?>
						<p><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'No Fast App ID has been entered.', 'fast' ); ?></p>
					<?php endif; ?>
				</td>
			</tr>

			<tr>
				<td style="vertical-align: top;">
					<p><strong><?php esc_html_e( 'Test Mode', 'fast' ); ?></strong></p>
				</td>
				<td style="vertical-align: top;">
					<p><span class="dashicons dashicons-<?php echo esc_attr( $fastwc_test_mode_class ); ?>"></span> <?php echo esc_html( $fastwc_test_mode_status ); ?></p>
				</td>
			</tr>

			<tr>
				<td style="vertical-align: top;">
					<p><strong><?php esc_html_e( 'Debug Mode', 'fast' ); ?></strong></p>
				</td>
				<td style="vertical-align: top;">
					<p><span class="dashicons dashicons-<?php echo esc_attr( $fastwc_debug_mode_class ); ?>"></span> <?php echo esc_html( $fastwc_debug_mode_status ); ?></p>
				</td>
			</tr>

			<?php if ( ! empty( $fastwc_setting_app_id ) ) : ?>
			<tr>
				<td style="vertical-align: top;">
					<p><strong><?php esc_html_e( 'Webhooks', 'fast' ); ?></strong></p>
				</td>
				<td style="vertical-align: top;">
					<?php if ( $fastwc_has_webhooks && empty( $fastwc_disabled_webhooks ) ) : ?>
						<p><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'All Fast webhooks are enabled.', 'fast' ); ?></p>
					<?php else : ?>
						<?php if ( ! $fastwc_has_webhooks ) : ?>
						<p><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'One ore more Fast WooCommerce webhooks are missing.', 'fast' ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $fastwc_disabled_webhooks ) ) : ?>
						<p><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'The following Fast WooCommerce webhooks are disabled', 'fast' ); ?>:</p>
						<ul class="ul-disc">
						<?php foreach ( $fastwc_disabled_webhooks as $webhook_topic => $webhook_id ) : ?>
							<li><?php echo esc_html( $webhook_topic ); ?></li>
						<?php endforeach ?>
						</ul>
						<?php endif; ?>
					<?php endif; ?>
					<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' ) ); ?>"><?php esc_html_e( 'View all WooCommerce webhooks', 'fast' ); ?></a></p>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
