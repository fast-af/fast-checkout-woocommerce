<?php
/**
 * Common utility functions for the Fast plugin.
 *
 * @package Fast
 */

/**
 * Load a Fast temlate.
 *
 * @param string $template_name The name of the template to load.
 * @param array  $args          Optional. Args to pass to the template. Requires WP 5.5+.
 *
 * @uses load_template
 */
function fastwc_load_template( $template_name, $args = array() ) {
	$locations = array(
		// Child theme directory.
		get_stylesheet_directory() . '/templates/' . $template_name . '.php',

		// Parent theme directory.
		get_template_directory() . '/templates/' . $template_name . '.php',

		// Plugin directory.
		FASTWC_PATH . 'templates/' . $template_name . '.php',
	);

	// Check each file location and load the first one that exists.
	foreach ( $locations as $location ) {
		if ( file_exists( $location ) ) {
			/**
			 * WordPress load_template function to load the located template.
			 *
			 * @param string $location     Location of the template to load.
			 * @param bool   $require_once Flag to use require_once instead of require.
			 * @param array  $args         Array of args to pass to the tepmlate. Requires WP 5.5+.
			 */
			load_template( $location, false, $args );
			return;
		}
	}
}

/**
 * Checks if the Fast Checkout button should be hidden for the current user based on the Test Mode field and their email
 * The button should be hidden for all non-Fast users if Test Mode is enabled, and should be visible for everyone if
 * Test Mode is disabled.
 *
 * @return bool true if we should hide the button, false otherwise
 */
function fastwc_is_hidden_for_test_mode() {
	// If test mode option is not yet set (e.g. plugin was just installed), treat it as enabled.
	// There is code in the settings page that actually sets this to enabled the first time the user views the form.
	$fastwc_test_mode = get_option( FASTWC_SETTING_TEST_MODE, '1' );
	if ( $fastwc_test_mode ) {
		// In test mode, we only want to show the button if the user is an admin or their email ends with @fast.co.
		$current_user = wp_get_current_user();
		if ( ! preg_match( '/@fast.co$/i', $current_user->user_email ) && ! $current_user->has_cap( 'administrator' ) ) {
			// User is not an admin or a Fast employee. Return early so button never sees the light of day.
			return true;
		}
	} else {
		return false;
	}
}

/**
 * Checks if the store's app ID is empty
 *
 * @return bool true if the app ID is empty, false otherwise
 */
function fastwc_is_app_id_empty() {
	$fastwc_app_id = fastwc_get_app_id();
	return empty( $fastwc_app_id );
}
