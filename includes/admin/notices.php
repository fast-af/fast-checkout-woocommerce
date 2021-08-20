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
	$fastwc_debug_mode        = get_option( FASTWC_SETTING_DEBUG_MODE, 0 );
	$fastwc_test_mode         = get_option( FASTWC_SETTING_TEST_MODE, '1' );
	$fastwc_disabled_webhooks = fastwc_get_disabled_webhooks();

	if ( ! empty( $fastwc_debug_mode ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_debug_mode' );
	}

	if ( ! empty( $fastwc_test_mode ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_test_mode' );
	}

	if ( ! empty( $fastwc_disabled_webhooks ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_disabled_webhooks' );
	}
}
add_action( 'admin_init', 'fastwc_maybe_display_admin_notices' );

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
function fastwc_settings_admin_notice_debug_mode() {
	$fastwc_disabled_webhooks = fastwc_get_disabled_webhooks();

	fastwc_admin_notice( __( 'Fast Checkout for WooCommerce has disabled webhooks.', 'fast' ) );
}
