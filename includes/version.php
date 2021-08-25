<?php
/**
 * Functions to check for WooCommerce and its version.
 *
 * @package Fast
 */

/**
 * Check to see if WooCommerce is installed and active.
 *
 * @return bool
 */
function fastwc_woocommerce_is_active() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	$wc_is_active = is_plugin_active( 'woocommerce/woocommerce.php' );

	if ( ! $wc_is_active ) {
		// Add an admin notice that WooCommerce must be active in order for Fast to work.
		add_action(
			'admin_notices',
			'fastwc_settings_admin_notice_woocommerce_not_installed'
		);
	}

	return $wc_is_active;
}

/**
 * Prints the error message when woocommerce isn't installed.
 */
function fastwc_settings_admin_notice_woocommerce_not_installed() {
	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__( "Your Fast plugin won't work without an active WooCommerce installation.", 'fast' )
	);
}

/**
 * Check that the WooCommerce version is greater than a particular version.
 *
 * @param string $version The version number to compare.
 *
 * @return bool
 */
function fastwc_woocommerce_version_is_at_least( $version ) {
	if (
		defined( 'WC_VERSION' ) &&
		version_compare( WC_VERSION, $version, '>=' )
	) {
		return true;
	}

	return false;
}

/**
 * Check that Gutenberg is active.
 *
 * @return bool
 */
function fastwc_gutenberg_is_active() {
	return function_exists( 'register_block_type' );
}
