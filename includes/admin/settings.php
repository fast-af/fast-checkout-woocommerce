<?php
/**
 * Fast Plugin Settings
 *
 * Adds config UI for wp-admin.
 *
 * @package Fast
 */

define( 'FASTWC_SETTING_APP_ID', 'fast_app_id' );
define( 'FASTWC_SETTING_TEST_MODE', 'fast_test_mode' );
define( 'FASTWC_SETTING_TEST_MODE_NOT_SET', 'fast test mode not set' );
define( 'FASTWC_SETTING_FAST_JS_URL', 'fast_fast_js_url' );
define( 'FASTWC_SETTING_FAST_JWKS_URL', 'fast_fast_jwks_url' );
define( 'FASTWC_SETTING_ONBOARDING_URL', 'fast_onboarding_url' );
define( 'FASTWC_SETTING_PDP_BUTTON_STYLES', 'fast_pdp_button_styles' );
define( 'FASTWC_SETTING_CART_BUTTON_STYLES', 'fast_cart_button_styles' );
define( 'FASTWC_SETTING_MINI_CART_BUTTON_STYLES', 'fast_mini_cart_button_styles' );
define( 'FASTWC_SETTING_CHECKOUT_BUTTON_STYLES', 'fast_checkout_button_styles' );
define( 'FASTWC_SETTING_LOGIN_BUTTON_STYLES', 'fast_login_button_styles' );
define( 'FASTWC_SETTING_DISABLE_MULTICURRENCY', 'fastwc_disable_multicurrency' );
define( 'FASTWC_JWKS_URL', 'https://api.fast.co/v1/oauth2/jwks' );
define( 'FASTWC_JS_URL', 'https://js.fast.co/fast-woocommerce.js' );
define( 'FASTWC_ONBOARDING_URL', 'https://fast.co/business' );

define(
	'FASTWC_SETTING_PDP_BUTTON_STYLES_DEFAULT',
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
	'FASTWC_SETTING_CART_BUTTON_STYLES_DEFAULT',
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
	'FASTWC_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT',
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
	'FASTWC_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT',
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
	'FASTWC_SETTING_LOGIN_BUTTON_STYLES_DEFAULT',
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

add_action( 'admin_head', 'fastwc_admin_styles' );
add_action( 'admin_menu', 'fastwc_admin_create_menu' );
add_action( 'admin_init', 'fastwc_admin_setup_sections' );
add_action( 'admin_init', 'fastwc_admin_setup_fields' );

/**
 * Registers the Fast menu within wp-admin.
 */
function fastwc_admin_create_menu() {
	// Add the menu item and page.
	$page_title = 'Fast Settings';
	$menu_title = 'Fast';
	$capability = 'manage_options';
	$slug       = 'fast';
	$callback   = 'fastwc_settings_page_content';
	$icon       = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjM4cHgiIGhlaWdodD0iMjM4cHgiIHZpZXdCb3g9IjAgMCAyMzggMjM4IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPHRpdGxlPkFydGJvYXJkPC90aXRsZT4KICAgIDxnIGlkPSJQYWdlLTEiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxnIGlkPSJBcnRib2FyZCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTEwOC4wMDAwMDAsIC02Ny4wMDAwMDApIiBmaWxsPSIjRkZGRkZGIiBmaWxsLXJ1bGU9Im5vbnplcm8iPgogICAgICAgICAgICA8cGF0aCBkPSJNMjc5LjYxMjEzOSwxMjkuODA5NTMxIEwyMDcuMTMzMTkzLDEyOS44MDk1MzEgTDIwNy4xMzMxOTMsMTcwLjA0NzY1OSBMMjY5Ljk0Nzk4MiwxNzAuMDQ3NjU5IEwyNjkuOTQ3OTgyLDE5OC44NTczMzQgTDIwNy4xMzMxOTMsMTk4Ljg1NzMzNCBMMjA3LjEzMzE5MywyNzEgTDE3NCwyNzEgTDE3NCwxMTQuODA5NTYxIEMxNzQsMTEwLjY4MjUyMyAxNzUuMzgwNTUzLDEwNy4zNDkyMSAxNzguMTQxNjUxLDEwNC44MDk1MzUgQzE4MC45MDI3NTYsMTAyLjI2OTg0NSAxODQuMjAwNzM1LDEwMSAxODguMDM1NTcxLDEwMSBMMjc5LjYxMjEzOSwxMDEgTDI3OS42MTIxMzksMTI5LjgwOTUzMSBaIiBpZD0iUGF0aCI+PC9wYXRoPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+';
	$position   = 100;

	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
	register_setting( 'fast', FASTWC_SETTING_APP_ID );
	register_setting( 'fast', FASTWC_SETTING_PDP_BUTTON_STYLES );
	register_setting( 'fast', FASTWC_SETTING_CART_BUTTON_STYLES );
	register_setting( 'fast', FASTWC_SETTING_MINI_CART_BUTTON_STYLES );
	register_setting( 'fast', FASTWC_SETTING_CHECKOUT_BUTTON_STYLES );
	register_setting( 'fast', FASTWC_SETTING_LOGIN_BUTTON_STYLES );
	register_setting( 'fast', FASTWC_SETTING_TEST_MODE );
	register_setting( 'fast', FASTWC_SETTING_DISABLE_MULTICURRENCY );
	register_setting( 'fast', FASTWC_SETTING_FAST_JS_URL );
	register_setting( 'fast', FASTWC_SETTING_FAST_JWKS_URL );
	register_setting( 'fast', FASTWC_SETTING_ONBOARDING_URL );

	// Check whether the woocommerce plugin is active.
	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	if ( ! in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
		add_action( 'admin_notices', 'fastwc_settings_admin_notice_woocommerce_not_installed' );
	}
}

/**
 * Prints the error message when woocommerce isn't installed.
 */
function fastwc_settings_admin_notice_woocommerce_not_installed() {
	printf( '<div class="notice notice-error"><p>Your Fast plugin won\'t work without an active WooCommerce installation.</p></div>' );
}

/**
 * Renders content of Fast settings page.
 */
function fastwc_settings_page_content() {
	?>
		<div class="wrap fast-settings">
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
function fastwc_admin_setup_sections() {
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
function fastwc_admin_setup_fields() {
	$settings_page = 'fast';

	// App Info settings.
	$settings_section = 'fast_app_info';
	add_settings_field( FASTWC_SETTING_APP_ID, 'App ID', 'fastwc_app_id_content', $settings_page, $settings_section );

	// Button style settings.
	$settings_section = 'fast_styles';
	add_settings_field( FASTWC_SETTING_PDP_BUTTON_STYLES, 'Product page button styles', 'fastwc_pdp_button_styles_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_CART_BUTTON_STYLES, 'Cart page button styles', 'fastwc_cart_button_styles_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_MINI_CART_BUTTON_STYLES, 'Mini cart widget button styles', 'fastwc_mini_cart_button_styles_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_CHECKOUT_BUTTON_STYLES, 'Checkout page button styles', 'fastwc_checkout_button_styles_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_LOGIN_BUTTON_STYLES, 'Login button styles', 'fastwc_login_button_styles_content', $settings_page, $settings_section );

	// Test Mode settings.
	$settings_section = 'fast_test_mode';
	add_settings_field( FASTWC_SETTING_TEST_MODE, 'Test Mode', 'fastwc_test_mode_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_DISABLE_MULTICURRENCY, __( 'Disable Multicurrency Support', 'fast' ), 'fastwc_disable_multicurrency_content', $settings_page, $settings_section );

	// Advanced settings.
	$settings_section = 'fast_advanced';
	add_settings_field( FASTWC_SETTING_FAST_JS_URL, 'Fast JS URL', 'fastwc_fastwc_js_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_FAST_JWKS_URL, 'Fast JWKS URL', 'fastwc_fastwc_jwks_content', $settings_page, $settings_section );
	add_settings_field( FASTWC_SETTING_ONBOARDING_URL, 'Fast Onboarding URL', 'fastwc_onboarding_url_content', $settings_page, $settings_section );
}

/**
 * Renders the App ID field.
 */
function fastwc_app_id_content() {
	$fastwc_setting_app_id              = fastwc_get_app_id();
	$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );
	?>
		<input
			name="fast_app_id"
			id="fast_app_id"
			type="text"
			class="input-field"
			value="<?php echo esc_attr( $fastwc_setting_app_id ); ?>"
		/>
		<p>Don't have an app yet? Click <a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener">here</a> to create one.</p>
	<?php
}

/**
 * Renders the PDP button styles field.
 */
function fastwc_pdp_button_styles_content() {
	$fastwc_setting_pdp_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_PDP_BUTTON_STYLES, FASTWC_SETTING_PDP_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_pdp_button_styles" id="fast_pdp_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fastwc_setting_pdp_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the cart button styles field.
 */
function fastwc_cart_button_styles_content() {
	$fastwc_setting_cart_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_CART_BUTTON_STYLES, FASTWC_SETTING_CART_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_cart_button_styles" id="fast_cart_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fastwc_setting_cart_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the mini-cart button styles field.
 */
function fastwc_mini_cart_button_styles_content() {
	$fastwc_setting_mini_cart_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_MINI_CART_BUTTON_STYLES, FASTWC_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_mini_cart_button_styles" id="fast_mini_cart_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fastwc_setting_mini_cart_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the checkout button styles field.
 */
function fastwc_checkout_button_styles_content() {
	$fastwc_setting_checkout_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_CHECKOUT_BUTTON_STYLES, FASTWC_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_checkout_button_styles" id="fast_checkout_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fastwc_setting_checkout_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the login button styles field.
 */
function fastwc_login_button_styles_content() {
	$fastwc_setting_login_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_LOGIN_BUTTON_STYLES, FASTWC_SETTING_LOGIN_BUTTON_STYLES_DEFAULT );
	?>
		<textarea name="fast_login_button_styles" id="fast_login_button_styles" rows="10" cols="50"><?php echo esc_textarea( $fastwc_setting_login_button_styles ); ?></textarea>
	<?php
}

/**
 * Renders the Test Mode field.
 */
function fastwc_test_mode_content() {
	$fastwc_test_mode = get_option( FASTWC_SETTING_TEST_MODE, FASTWC_SETTING_TEST_MODE_NOT_SET );

	if ( FASTWC_SETTING_TEST_MODE_NOT_SET === $fastwc_test_mode ) {
		// If the option is FASTWC_SETTING_TEST_MODE_NOT_SET, then it hasn't yet been set. In this case, we
		// want to configure test mode to be on.
		$fastwc_test_mode = '1';
		update_option( FASTWC_SETTING_TEST_MODE, '1' );
	}

	?>
		<div>
			<input
				name="fast_test_mode"
				id="fast_test_mode"
				type="checkbox"
				value="1"
				<?php checked( 1, $fastwc_test_mode ); ?>
			/>
			<label for="fast_test_mode"><?php esc_html_e( 'Enable test mode', 'fast' ); ?></label>
		</div>
		<p class="description"><?php esc_html_e( 'When test mode is enabled, only logged-in admin users will see the Fast Checkout button.', 'fast' ); ?></p>
	<?php
}

/**
 * Renders the Disable Multicurrency field.
 */
function fastwc_disable_multicurrency_content() {
	$fastwc_disable_multicurrency = get_option( FASTWC_SETTING_DISABLE_MULTICURRENCY, 0 );

	?>
		<div>
			<input
				name="fastwc_disable_multicurrency"
				id="fastwc_disable_multicurrency"
				type="checkbox"
				value="1"
				<?php checked( 1, $fastwc_disable_multicurrency ); ?>
			/>
			<label for="fastwc_disable_multicurrency"><?php esc_html_e( 'Disable Multicurrency Support', 'fast' ); ?></label>
		</div>
		<p class="description"><?php esc_html_e( 'Disable multicurrency support in Fast Checkout.', 'fast' ); ?></p>
	<?php
}

/**
 * Renders the fast.js URL field.
 */
function fastwc_fastwc_js_content() {
	$fastwc_setting_fast_js_url = fastwc_get_option_or_set_default( FASTWC_SETTING_FAST_JS_URL, FASTWC_JS_URL );
	?>
		<input
		name="fast_fast_js_url"
		id="fast_fast_js_url"
		type="text"
		class="input-field"
		value="<?php echo esc_attr( $fastwc_setting_fast_js_url ); ?>"
	/>
	<?php
}

/**
 * Renders the Fast JWKS URL field.
 */
function fastwc_fastwc_jwks_content() {
	$fastwc_setting_fast_jwks_url = fastwc_get_option_or_set_default( FASTWC_SETTING_FAST_JWKS_URL, FASTWC_JWKS_URL );
	?>
		<input
			name="fast_fast_jwks_url"
			id="fast_fast_jwks_url"
			type="text"
			class="input-field"
			value="<?php echo esc_attr( $fastwc_setting_fast_jwks_url ); ?>"
		/>
	<?php
}

/**
 * Renders the onboarding URL field.
 */
function fastwc_onboarding_url_content() {
	$url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );
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
function fastwc_admin_styles() {
	$current_screen = get_current_screen();

	if ( ! empty( $current_screen ) && isset( $current_screen->id ) && 'toplevel_page_fast' !== $current_screen->id ) {
		return;
	}
	?>
		<style>
			.fast-settings,
			.fast-settings td,
			.fast-settings textarea,
			.fast-settings input,
			.fast-settings select {
				font-family: "Lucida Grande";
				font-size: 12px;
			}
			@media screen and (min-width: 783px) {
				.fast-settings .input-field {
					min-height: 40px;
					width: 400px;
				}
			}
			.fast-settings textarea {
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
function fastwc_get_option_or_set_default( $option, $default ) {
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
function fastwc_get_app_id() {
	return get_option( FASTWC_SETTING_APP_ID );
}
