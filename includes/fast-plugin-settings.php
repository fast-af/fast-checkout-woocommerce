<?php
/**
 * Fast Plugin Settings
 *
 * Adds config UI for wp-admin.
 *
 * @package Fast
 */

define( 'FAST_SETTING_APP_ID', 'fast_app_id' );
define( 'FAST_SETTING_TEST_MODE', 'fast_test_mode' );
define( 'FAST_SETTING_FAST_JS_URL', 'fast_fast_js_url' );
define( 'FAST_SETTING_FAST_JWKS_URL', 'fast_fast_jwks_url' );
define( 'FAST_SETTING_ONBOARDING_URL', 'fast_onboarding_url' );
define( 'FAST_SETTING_PDP_BUTTON_STYLES', 'fast_pdp_button_styles' );
define( 'FAST_SETTING_CART_BUTTON_STYLES', 'fast_cart_button_styles' );
define( 'FAST_SETTING_MINI_CART_BUTTON_STYLES', 'fast_mini_cart_button_styles' );
define( 'FAST_SETTING_CHECKOUT_BUTTON_STYLES', 'fast_checkout_button_styles' );
define( 'FAST_SETTING_LOGIN_BUTTON_STYLES', 'fast_login_button_styles' );
define( 'FAST_JWKS_URL', 'https://api.fast.co/v1/oauth2/jwks' );
define( 'FAST_JS_URL', 'https://js.fast.co/fast-woocommerce.js' );
define( 'FAST_ONBOARDING_URL', 'https://fast.co/business' );

define(
	'FAST_SETTING_PDP_BUTTON_STYLES_DEFAULT',
	<<<CSS
.fast-pdp-wrapper {
  padding: 21px 0 20px 0;
  margin: 20px 0;
}

.fast-pdp-or {
  position: relative;
  top: 21px;
  width: 40px;
  height: 1px;
  line-height: 0;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #757575;
  background: white;
}

@media only screen and (max-width: 767px) {
  .fast-pdp-wrapper {
    border-bottom: 1px solid #dfdfdf;
    border-radius: none;
    padding-right: 1%;
    padding-left: 1%;
  }
}

@media only screen and (min-width: 768px) {
  .fast-pdp-wrapper {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    padding-right: 10%;
    padding-left: 10%;
  }
}
CSS
);

define(
	'FAST_SETTING_CART_BUTTON_STYLES_DEFAULT',
	<<<CSS
.fast-cart-wrapper {
  padding: 21px 0 20px 0;
  margin-bottom: 20px;
}

.fast-cart-or {
  position: relative;
  top: 21px;
  width: 40px;
  height: 1px;
  line-height: 0;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #757575;
  background: white;
}

@media only screen and (max-width: 767px) {
  .fast-cart-wrapper {
    border-bottom: 1px solid #dfdfdf;
    border-radius: none;
    padding-right: 1%;
    padding-left: 1%;
  }
}

@media only screen and (min-width: 768px) {
  .fast-cart-wrapper {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    padding-right: 10%;
    padding-left: 10%;
  }
}
CSS
);

define(
	'FAST_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT',
	<<<CSS
.fast-mini-cart-wrapper {
  height: 68px;
  clear: both;
  border-bottom: 1px solid #dfdfdf;
  padding-bottom: 0px;
}

.fast-mini-cart-or {
  position: relative;
  background: inherit;
  width: 40px;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #dfdfdf;
}
CSS
);

define(
	'FAST_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT',
	<<<CSS
.fast-checkout-wrapper {
  padding: 21px 0 20px 0;
  margin-bottom: 20px;
}

.fast-checkout-or {
  position: relative;
  top: 21px;
  width: 40px;
  height: 1px;
  line-height: 0;
  text-align: center;
  margin-left: auto;
  margin-right: auto;
  color: #757575;
  background: white;
}

@media only screen and (max-width: 767px) {
  .fast-checkout-wrapper {
    border-bottom: 1px solid #dfdfdf;
    border-radius: none;
    padding-right: 1%;
    padding-left: 1%;
  }
}

@media only screen and (min-width: 768px) {
  .fast-checkout-wrapper {
    border: 1px solid #dfdfdf;
    border-radius: 5px;
    padding-right: 10%;
    padding-left: 10%;
  }
}
CSS
);

define(
	'FAST_SETTING_LOGIN_BUTTON_STYLES_DEFAULT',
	<<<CSS
.fast-login-wrapper {
  border: 1.25px solid #d3ced2;
  padding: 16px 30% 16px 30%;
  margin-left: 16px;
  margin-right: 16px;
}

@media (min-width: 560px) {
  .fast-login-wrapper {
    width: 100%;
    padding: 16px 30% 16px 30%;
    margin-left: auto;
    margin-right: auto;
  }
}

@media (min-width: 1006px) {
  .fast-login-wrapper {
    width: 1006px;
    padding: 16px 300px 16px 300px;
  }
}
CSS
);

add_action( 'admin_head', 'fast_admin_styles' );
add_action( 'admin_menu', 'fast_admin_create_menu' );
add_action( 'admin_init', 'fast_admin_setup_sections' );
add_action( 'admin_init', 'fast_admin_setup_fields' );

/**
 * Registers the Fast menu within wp-admin.
 */
function fast_admin_create_menu() {
	// Add the menu item and page.
	$page_title = 'Fast Settings';
	$menu_title = 'Fast';
	$capability = 'manage_options';
	$slug       = 'fast';
	$callback   = 'fast_settings_page_content';
	$icon       = 'dashicons-superhero';
	$position   = 100;

	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
	register_setting( 'fast', FAST_SETTING_APP_ID );
	register_setting( 'fast', FAST_SETTING_PDP_BUTTON_STYLES );
	register_setting( 'fast', FAST_SETTING_CART_BUTTON_STYLES );
	register_setting( 'fast', FAST_SETTING_MINI_CART_BUTTON_STYLES );
	register_setting( 'fast', FAST_SETTING_CHECKOUT_BUTTON_STYLES );
	register_setting( 'fast', FAST_SETTING_LOGIN_BUTTON_STYLES );
	register_setting( 'fast', FAST_SETTING_TEST_MODE );
	register_setting( 'fast', FAST_SETTING_FAST_JS_URL );
	register_setting( 'fast', FAST_SETTING_FAST_JWKS_URL );
	register_setting( 'fast', FAST_SETTING_ONBOARDING_URL );

	// Check whether the woocommerce plugin is active.
	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', 'fast_settings_admin_notice_woocommerce_not_installed' );
	}
}

/**
 * Prints the error message when woocommerce isn't installed.
 */
function fast_settings_admin_notice_woocommerce_not_installed() {
	printf( '<div class="notice notice-error"><p>Your Fast plugin won\'t work without an active WooCommerce installation.</p></div>' );
}

/**
 * Renders content of Fast settings page.
 */
function fast_settings_page_content() {
	?>
		<div class="wrap">
			<h2>Fast Settings</h2>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'fast' );
					do_settings_sections( 'fast' );
					submit_button();
				?>
			</form>
		</div>
	<?php
}

/**
 * Sets up sections for Fast settings page.
 */
function fast_admin_setup_sections() {
	add_settings_section( 'fast_app_info', 'App Info', false, 'fast' );
	add_settings_section( 'fast_styles', 'Button Styles', false, 'fast' );
	add_settings_section( 'fast_test_mode', 'Test Mode', false, 'fast' );

	// For now, only allow fast users to see advanced settings.
	if ( preg_match( '/@fast.co$/i', wp_get_current_user()->user_email ) ) {
		add_settings_section( 'fast_advanced', 'Advanced', false, 'fast' );
	}
}

/**
 * Sets up fields for Fast settings page.
 */
function fast_admin_setup_fields() {
	add_settings_field( FAST_SETTING_APP_ID, 'App ID', 'fast_app_id_content', 'fast', 'fast_app_info' );

	add_settings_field( FAST_SETTING_PDP_BUTTON_STYLES, 'Product page button styles', 'fast_pdp_button_styles_content', 'fast', 'fast_styles' );
	add_settings_field( FAST_SETTING_CART_BUTTON_STYLES, 'Cart page button styles', 'fast_cart_button_styles_content', 'fast', 'fast_styles' );
	add_settings_field( FAST_SETTING_MINI_CART_BUTTON_STYLES, 'Mini cart widget button styles', 'fast_mini_cart_button_styles_content', 'fast', 'fast_styles' );
	add_settings_field( FAST_SETTING_CHECKOUT_BUTTON_STYLES, 'Checkout page button styles', 'fast_checkout_button_styles_content', 'fast', 'fast_styles' );
	add_settings_field( FAST_SETTING_LOGIN_BUTTON_STYLES, 'Login button styles', 'fast_login_button_styles_content', 'fast', 'fast_styles' );

	add_settings_field( FAST_SETTING_TEST_MODE, 'Test Mode', 'fast_test_mode_content', 'fast', 'fast_test_mode' );

	add_settings_field( FAST_SETTING_FAST_JS_URL, 'Fast JS URL', 'fast_fast_js_content', 'fast', 'fast_advanced' );
	add_settings_field( FAST_SETTING_FAST_JWKS_URL, 'Fast JWKS URL', 'fast_fast_jwks_content', 'fast', 'fast_advanced' );
	add_settings_field( FAST_SETTING_ONBOARDING_URL, 'Fast Onboarding URL', 'fast_onboarding_url_content', 'fast', 'fast_advanced' );
}

/**
 * Renders the App ID field.
 */
function fast_app_id_content() {
	$fast_setting_app_id              = fast_get_app_id();
	$fast_setting_fast_onboarding_url = fast_get_option_or_set_default( FAST_SETTING_ONBOARDING_URL, FAST_ONBOARDING_URL );
	?>
		<input
			name="fast_app_id"
			id="fast_app_id"
			type="text"
			class="input-field"
			value="<?php echo esc_attr( $fast_setting_app_id ); ?>"
		/>
		<p>Don't have an app yet? Click <a href="<?php echo esc_url( $fast_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener">here</a> to create one.</p>
	<?php
}

/**
 * Renders the PDP button styles field.
 */
function fast_pdp_button_styles_content() {
	$fast_setting_pdp_button_styles = fast_get_option_or_set_default( FAST_SETTING_PDP_BUTTON_STYLES, FAST_SETTING_PDP_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_pdp_button_styles" id="fast_pdp_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fast_setting_pdp_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the cart button styles field.
 */
function fast_cart_button_styles_content() {
	$fast_setting_cart_button_styles = fast_get_option_or_set_default( FAST_SETTING_CART_BUTTON_STYLES, FAST_SETTING_CART_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_cart_button_styles" id="fast_cart_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fast_setting_cart_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the mini-cart button styles field.
 */
function fast_mini_cart_button_styles_content() {
	$fast_setting_mini_cart_button_styles = fast_get_option_or_set_default( FAST_SETTING_MINI_CART_BUTTON_STYLES, FAST_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_mini_cart_button_styles" id="fast_mini_cart_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fast_setting_mini_cart_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the checkout button styles field.
 */
function fast_checkout_button_styles_content() {
	$fast_setting_checkout_button_styles = fast_get_option_or_set_default( FAST_SETTING_CHECKOUT_BUTTON_STYLES, FAST_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_checkout_button_styles" id="fast_checkout_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fast_setting_checkout_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the login button styles field.
 */
function fast_login_button_styles_content() {
	$fast_setting_login_button_styles = fast_get_option_or_set_default( FAST_SETTING_LOGIN_BUTTON_STYLES, FAST_SETTING_LOGIN_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_login_button_styles" id="fast_login_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fast_setting_login_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the Test Mode field.
 */
function fast_test_mode_content() {
	$fast_test_mode = get_option( FAST_SETTING_TEST_MODE );

	if ( false === $fast_test_mode ) {
		// If the option is false specifically (not just any falsey value), then it hasn't yet been set. In this case, we
		// want to configure test mode to be on.
		$fast_test_mode = '1';
		update_option( FAST_SETTING_TEST_MODE, '1' );
	}

	?>
		<span>When test mode is enabled, only logged-in admin users will see the Fast Checkout button.</span>
		<div>
			<input
				name="fast_test_mode"
				id="fast_test_mode"
				type="checkbox"
				value="1"
				<?php echo checked( 1, $fast_test_mode, false ); ?>
			/>
			<label for="fast_test_mode">Enable test mode</label>
		</div>
	<?php
}

/**
 * Renders the fast.js URL field.
 */
function fast_fast_js_content() {
	$fast_setting_fast_js_url = fast_get_option_or_set_default( FAST_SETTING_FAST_JS_URL, FAST_JS_URL );
	?>
		<input
		name="fast_fast_js_url"
		id="fast_fast_js_url"
		type="text"
		class="input-field"
		value="<?php echo esc_attr( $fast_setting_fast_js_url ); ?>"
	/>
	<?php
}

/**
 * Renders the Fast JWKS URL field.
 */
function fast_fast_jwks_content() {
	$fast_setting_fast_jwks_url = fast_get_option_or_set_default( FAST_SETTING_FAST_JWKS_URL, FAST_JWKS_URL );
	?>
		<input
			name="fast_fast_jwks_url"
			id="fast_fast_jwks_url"
			type="text"
			class="input-field"
			value="<?php echo esc_attr( $fast_setting_fast_jwks_url ); ?>"
		/>
	<?php
}

/**
 * Renders the onboarding URL field.
 */
function fast_onboarding_url_content() {
	$url = fast_get_option_or_set_default( FAST_SETTING_ONBOARDING_URL, FAST_ONBOARDING_URL );
	?>
		<input
			name="fast_onboarding_url"
			id="fast_onboarding_url"
			type="text"
			class="input-field"
			value="<?php echo esc_attr( $url ); ?>"
		/>
	<?php
}

/**
 * Custom styles for Fast settings page.
 */
function fast_admin_styles() {
	$current_screen = get_current_screen();

	if ( ! empty( $current_screen ) && isset( $current_screen->id ) && 'toplevel_page_fast' !== $current_screen->id ) {
		return;
	}
	?>
		<style>
			body, td, textarea, input, select {
				font-family: "Lucida Grande";
				font-size: 12px;
			}
			@media screen and (min-width: 783px) {
				.input-field {
					min-height: 40px;
					width: 400px;
				}
			}
			textarea {
				resize: none;
			}
		</style>
	<?php
}

/**
 * Helper that returns the value of an option if it is set, and sets and returns a default if the option was not set.
 * This is similar to get_option($option, $default), except that it *sets* the option if it is not set instead of just returning a default.
 *
 * @see https://developer.wordpress.org/reference/functions/get_option/
 *
 * @param string $option Name of the option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Default value to set option to and return if the return value of get_option is falsey.
 * @return mixed The value of the option if it is truthy, or the default if the option's value is falsey.
 */
function fast_get_option_or_set_default( $option, $default ) {
	$val = get_option( $option );
	if ( $val ) {
		return $val;
	}
	update_option( $option, $default );
	return $default;
}

/**
 * Get the Fast APP ID.
 *
 * @return string
 */
function fast_get_app_id() {
	return get_option( FAST_SETTING_APP_ID );
}
