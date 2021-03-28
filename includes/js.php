<?php
/**
 * Loads fast-woocommerce.js in footer.
 *
 * @package Fast
 */

/**
 * Enqueue the Fast Woocommerce script.
 */
function fast_enqueue_script() {
	$fast_js = fast_get_option_or_set_default( FAST_SETTING_FAST_JS_URL, FAST_JS_URL );
	wp_enqueue_script( 'fast-woocommerce', $fast_js, array(), '1.20.0', true );
}
add_action( 'wp_enqueue_scripts', 'fast_enqueue_script' );
add_action( 'login_enqueue_scripts', 'fast_enqueue_script' );
