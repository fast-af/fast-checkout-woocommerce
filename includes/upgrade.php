<?php
/**
 * Utility to handle any updates needed when upgrading to a new version of the plugin.
 *
 * @package Fast
 */

// Define a key to use for the Fast version option.
define( 'FASTWC_OPTION_VERSION', 'fastwc_version' );

/**
 * Handle Fast Checkout for WooCommerce upgrades.
 */
function fastwc_handle_upgrades() {
	$previous_version = get_option( FASTWC_OPTION_VERSION );

	if ( empty( $previous_version ) ) {
		$previous_version = '1.1.15';
		add_option( FASTWC_OPTION_VERSION, $previous_version );
	}

	// Maybe handle the upgrade to version 1.1.16.
	if ( version_compare( $previous_version, '1.1.16', '<' ) ) {
		fastwc_handle_1_1_16_upgrades();
	}

	// Store the current version of the Fast Checkout for WooCommerce plugin.
	if ( version_compare( FASTWC_VERSION, $previous_version, '!=' ) ) {
		update_option( FASTWC_OPTION_VERSION, FASTWC_VERSION );
	}
}
add_action( 'admin_init', 'fastwc_handle_upgrades' );

/**
 * Handle the upgrade to version 1.1.16.
 */
function fastwc_handle_1_1_16_upgrades() {
	// Update the onboarding URL setting value.
	$onboarding_url = get_option( FASTWC_SETTING_ONBOARDING_URL );
	$dashboard_url  = get_option( FASTWC_SETTING_DASHBOARD_URL );

	if ( false !== strpos( $onboarding_url, 'staging' ) ) {
		$onboarding_url = 'https://fast-site.staging.slowfast.co/business-sign-up';
		$dashboard_url  = 'https://fast-site.staging.slowfast.co/business';
	} elseif ( false !== strpos( $onboarding_url, 'sandbox' ) ) {
		$onboarding_url = 'https://fast-site.sandbox.fast.co/business-sign-up';
		$dashboard_url  = 'https://fast-site.sandbox.fast.co/business';
	} elseif ( false !== strpos( $onboarding_url, 'dev' ) ) {
		$onboarding_url = 'https://fast-site.dev.slow.dev/business-sign-up';
		$dashboard_url  = 'https://fast-site.dev.slow.dev/business';
	} else {
		$onboarding_url = FASTWC_ONBOARDING_URL;
		$dashboard_url  = FASTWC_DASHBOARD_URL;
	}

	update_option( FASTWC_SETTING_ONBOARDING_URL, $onboarding_url );
	update_option( FASTWC_SETTING_DASHBOARD_URL, $dashboard_url );
}
