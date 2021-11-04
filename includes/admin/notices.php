<?php
/**
 * Display admin notices.
 *
 * @package Fast
 */

/**
 * Check for conditions to display admin notices.
 */
function fastwc_maybe_display_admin_notices() {
	$fast_app_id              = fastwc_get_app_id();
	$fastwc_debug_mode        = get_option( FASTWC_SETTING_DEBUG_MODE, 0 );
	$fastwc_test_mode         = get_option( FASTWC_SETTING_TEST_MODE, '1' );
	$fastwc_has_webhooks      = fastwc_woocommerce_has_fast_webhooks();
	$fastwc_disabled_webhooks = fastwc_get_disabled_webhooks();

	if ( ! empty( $fastwc_debug_mode ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_debug_mode' );
	}

	if ( ! empty( $fastwc_test_mode ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_test_mode' );
	}

	if ( ! empty( $fast_app_id ) && ! $fastwc_has_webhooks ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_missing_webhooks' );
	}

	if ( ! empty( $fast_app_id ) && ! empty( $fastwc_disabled_webhooks ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_disabled_webhooks' );
	}
}
add_action( 'admin_init', 'fastwc_maybe_display_admin_notices' );

/**
 * Maybe render the Fast "Become a Seller" CTA.
 *
 * @param string $context Optional. The context in which the CTA is to be loaded.
 */
function fastwc_maybe_render_cta( $context = '' ) {
	$fast_app_id = fastwc_get_app_id();

	if ( empty( $fast_app_id ) ) {
		fastwc_load_template( 'admin/fast-cta', array( 'context' => $context ) );
	}
}

/**
 * Template for printing an admin notice.
 *
 * @param string $message The message to display.
 * @param string $type    Optional. The type of message to display.
 */
function fastwc_admin_notice( $message, $type = 'warning' ) {
	$class = 'notice notice-' . $type;

	printf(
		'<div class="%1$s"><p>%2$s</p></div>',
		esc_attr( $class ),
		esc_html( $message )
	);
}

/**
 * Print the Test Mode admin notice.
 */
function fastwc_settings_admin_notice_test_mode() {
	fastwc_admin_notice( __( 'Fast Checkout for WooCommerce is currently in Test Mode.', 'fast' ) );
}

/**
 * Print the Debug Mode admin notice.
 */
function fastwc_settings_admin_notice_debug_mode() {
	fastwc_admin_notice( __( 'Fast Checkout for WooCommerce is currently in Debug Mode.', 'fast' ) );
}

/**
 * Print the Disabled Webhooks admin notice.
 */
function fastwc_settings_admin_notice_disabled_webhooks() {
	fastwc_admin_notice( __( 'One or more WooCommerce webhooks used by Fast Checkout for WooCommerce are disabled.', 'fast' ) );
}

/**
 * Print the Missing Webhooks admin notice.
 */
function fastwc_settings_admin_notice_missing_webhooks() {
	fastwc_admin_notice( __( 'One or more WooCommerce webhooks used by Fast Checkout for WooCommerce are missing.', 'fast' ) );
}
