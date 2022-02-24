<?php
/**
 * Fast admin settings footer.
 *
 * @package Fast
 */

$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );
$fastwc_setting_fast_dashboard_url  = fastwc_get_option_or_set_default( FASTWC_SETTING_DASHBOARD_URL, FASTWC_DASHBOARD_URL );
$fastwc_app_id                      = fastwc_get_app_id();
?>

<div class="fast-footer">
	<ul class="fast-footer-links">
		<li class="fast-footer-link">
			<a href="https://www.wordpress.org/plugins/fast-checkout-for-woocommerce" target="_blank" rel="noopener">
				<strong><?php esc_html_e( 'Fast Checkout for WooCommerce', 'fast' ); ?></strong>
			</a>
		</li>
		<li class="fast-footer-link alignright">
			<a href="https://www.wordpress.org/plugins/fast-checkout-for-woocommerce" target="_blank" rel="noopener">
				<?php
				printf(
					'%1$s %2$s',
					esc_html__( 'Version', 'fast' ),
					esc_html( FASTWC_VERSION )
				);
				?>
			</a>
		</li>
		<?php if ( empty( $fastwc_app_id ) ) : ?>
		<li class="fast-footer-link">
			<a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Become a Seller', 'fast' ); ?>">
				<?php esc_html_e( 'Become a Seller', 'fast' ); ?>
			</a>
		</li>
		<?php endif; ?>
		<li class="fast-footer-link">
			<a href="<?php echo esc_url( $fastwc_setting_fast_dashboard_url ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Login to the Fast Seller Dashboard', 'fast' ); ?>">
				<?php esc_html_e( 'Seller Login', 'fast' ); ?>
			</a>
		</li>
		<?php endif; ?>
		<li class="fast-footer-link">
			<a href="https://www.fast.co/home" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Learn more about Fast', 'fast' ); ?>">
				<?php esc_html_e( 'About', 'fast' ); ?>
			</a>
		</li>
		<li class="fast-footer-link">
			<a href="https://help.fast.co/" target="_blank" rel="noopener">
				<?php esc_html_e( 'Help', 'fast' ); ?>
			</a>
		</li>
		<li class="fast-footer-link">
			<a href="https://www.fast.co/terms/terms-home" target="_blank" rel="noopener">
				<?php esc_html_e( 'Terms', 'fast' ); ?>
			</a>
		</li>
		<li class="fast-footer-link">
			<a href="https://www.fast.co/privacy" target="_blank" rel="noopener">
				<?php esc_html_e( 'Privacy', 'fast' ); ?>
			</a>
		</li>
	</ul>
</div>
