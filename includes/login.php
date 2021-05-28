<?php
/**
 * Fast Login
 *
 * Adds Fast Login button to the store.
 *
 * @package Fast
 */

$loader = require_once __DIR__ . '/../vendor/autoload.php';

define( 'FASTWC_PARAM_WP_NONCE', '_wpnonce' );
define( 'FASTWC_RESPONSE_401', '401 Unauthorized' );

use \Firebase\JWT\JWT;
use \Firebase\JWT\JWK;

/**
 * Inject Fast login component in the footer.
 */
function fastwc_add_login_to_footer() {
	if ( fastwc_should_hide_login_button() ) {
		return;
	}

	// The admin might want to disable this in favor of using a widget.
	$show_in_footer = get_option( FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER, 1 );

	if ( ! empty( $show_in_footer ) ) {
		fastwc_load_template( 'fast-login' );
	}
}
add_action( 'get_footer', 'fastwc_add_login_to_footer' );

/**
 * Init Fast login.
 */
function fastwc_login_init() {
	// If the "auth" query param isn't set, exit.
	if ( ! isset( $_GET['auth'] ) ) {
		return;
	}

	// Verify the nonce.
	if ( ! isset( $_GET[ FASTWC_PARAM_WP_NONCE ] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_GET[ FASTWC_PARAM_WP_NONCE ] ) );
	if ( ! wp_verify_nonce( $nonce, 'fast-backend-login-auth' ) ) {
		wp_die( '401 Unauthorized: Invalid nonce.', esc_attr( FASTWC_RESPONSE_401 ) );
	}

	$auth_token = sanitize_text_field( wp_unslash( $_GET['auth'] ) );

	$claims = null;
	try {
		$claims = fastwc_backend_verify_jwt( $auth_token );
	} catch ( Exception $e ) {
		wp_die( '401 Unauthorized: Failed to login.', esc_attr( FASTWC_RESPONSE_401 ) );
	}

	$user_to_login = get_user_by( 'email', $claims->email );

	if ( ! $user_to_login ) {
		wp_die( '401 Unauthorized: Failed to get user.', esc_attr( FASTWC_RESPONSE_401 ) );
	}

	// Some sanity checks before we use these variables.
	if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
		wp_die( '401 Unauthorized: Failed to get request URI.', esc_attr( FASTWC_RESPONSE_401 ) );
	}
	if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
		wp_die( '401 Unauthorized: Failed to get HTTP Host.', esc_attr( FASTWC_RESPONSE_401 ) );
	}

	// Get the path and query of the page being requested.
	$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	$target_page = wp_parse_url( $request_uri, PHP_URL_PATH );
	$get_query   = fastwc_backend_strip_auth_query_param( $_GET );

	wp_set_auth_cookie( $user_to_login->ID, true );
	wp_set_current_user( $user_to_login->ID );
	do_action( 'wp_login', $user_to_login->name, $user_to_login );

	// Force page not to be cached using https://stackoverflow.com/questions/1907653/how-to-force-page-not-to-be-cached-in-php.
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-cache, no-store, must-revalidate, private, max-age=0, s-maxage=0' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	header( 'Expires: Mon, 01 Jan 1990 01:00:00 GMT' );

	wp_safe_redirect( 'https://' . wp_unslash( $_SERVER['HTTP_HOST'] ) . $target_page . $get_query );
}
add_action( 'init', 'fastwc_login_init' );

/**
 * Removes the "auth" query parameter from the URL.
 *
 * @param object $get The GET request.
 */
function fastwc_backend_strip_auth_query_param( $get ) {
	$get_copy = $get;
	unset( $get_copy['auth'] );
	unset( $get_copy[ FASTWC_PARAM_WP_NONCE ] );
	$get_query = fastwc_backend_join_get_parameters( $get_copy );
	if ( strlen( $get_query ) > 0 ) {
		$get_query = '?' . $get_query;
	}
	return $get_query;
}

/**
 * Joins the GET parameters to re-construct the query part of the URL.
 *
 * @param object $parameters A copy of the GET request.
 */
function fastwc_backend_join_get_parameters( $parameters ) {
	$keys        = array_keys( $parameters );
	$assignments = array();
	foreach ( $keys as $key ) {
		$assignments[] = rawurlencode( $key ) . '=' . rawurlencode( $parameters[ $key ] );
	}
	return implode( '&', $assignments );
}

/**
 * Verifies the JWT using fast's public key.
 *
 * @param string $token_str The stringified token from the auth query param.
 * @throws UnexpectedValueException Thrown when JWT audience doesn't match this app's ID.
 */
function fastwc_backend_verify_jwt( $token_str ) {
	$res     = wp_remote_get( get_option( FASTWC_SETTING_FAST_JWKS_URL ) );
	$jwks    = wp_remote_retrieve_body( $res );
	$keys    = json_decode( $jwks, true );
	$key_set = JWK::parseKeySet( $keys );

	$claims = JWT::decode( $token_str, $key_set, array( 'RS256' ) );

	$app_id = fastwc_get_app_id();
	if ( $app_id !== $claims->aud ) {
		throw new UnexpectedValueException( 'Audience mismatch' );
	}

	return $claims;
}
