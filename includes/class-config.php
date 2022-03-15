<?php
/**
 * Fast Configuration object to define config keys and fetch configuration settings.
 *
 * @package Fast
 */

namespace FastWC;

/**
 * Fast Configuration object.
 */
class Config {
	/**
	 * Define configuration keys.
	 */
	const KEY_APP_ID                          = 'fast_app_id';
	const KEY_TEST_MODE                       = 'fast_test_mode';
	const KEY_TEST_MODE_USERS                 = 'fastwc_test_mode_users';
	const KEY_DEBUG_MODE                      = 'fastwc_debug_mode';
	const KEY_FAST_JS_URL                     = 'fast_fast_js_url';
	const KEY_FAST_JWKS_URL                   = 'fast_fast_jwks_url';
	const KEY_ONBOARDING_URL                  = 'fast_onboarding_url';
	const KEY_DASHBOARD_URL                   = 'fastwc_dashboard_url';
	const KEY_LOAD_BUTTON_STYLES              = 'fastwc_load_button_styles';
	const KEY_USE_DARK_MODE                   = 'fastwc_use_dark_mode';
	const KEY_PDP_BUTTON_STYLES               = 'fast_pdp_button_styles';
	const KEY_CART_BUTTON_STYLES              = 'fast_cart_button_styles';
	const KEY_MINI_CART_BUTTON_STYLES         = 'fast_mini_cart_button_styles';
	const KEY_CHECKOUT_BUTTON_STYLES          = 'fast_checkout_button_styles';
	const KEY_LOGIN_BUTTON_STYLES             = 'fast_login_button_styles';
	const KEY_SHOW_LOGIN_BUTTON_FOOTER        = 'fastwc_show_login_button_footer';
	const KEY_DISABLE_MULTICURRENCY           = 'fastwc_disable_multicurrency';
	const KEY_HIDE_BUTTON_PRODUCTS            = 'fast_hide_button_products';
	const KEY_CHECKOUT_REDIRECT_PAGE          = 'fastwc_checkout_redirect_page';
	const KEY_REDIRECT_AFTER_PDP              = 'fastwc_redirect_after_pdp_order';
	const KEY_CLEAR_CART_AFTER_PDP            = 'fastwc_clear_cart_after_pdp';
	const KEY_PDP_BUTTON_HOOK                 = 'fast_pdp_button_hook';
	const KEY_PDP_BUTTON_HOOK_OTHER           = 'fast_pdp_button_hook_other';
	const KEY_BUTTON_WRAPPER_CONTENT          = 'fastwc_button_wrapper_content';
	const KEY_BUTTON_WRAPPER_CONTENT_LOCATION = 'fastwc_button_wrapper_content_location';
	const KEY_HIDE_REGULAR_CHECKOUT_BUTTONS   = 'fastwc_hide_regular_checkout_buttons';
	const KEY_TIMESTAMPS                      = 'fastwc_settings_timestamps';
	const KEY_PLUGIN_DO_INIT_FORMAT           = 'fastwc_do_init_%s';

	/**
	 * Define default values.
	 */
	const DEFAULT_PDP_BUTTON_HOOK = 'woocommerce_after_add_to_cart_quantity';
	const DEFAULT_FAST_JWKS_URL   = 'https://api.fast.co/v1/oauth2/jwks';
	const DEFAULT_FAST_JS_URL     = 'https://js.fast.co/fast-woocommerce.js';
	const DEFAULT_ONBOARDING_URL  = 'https://fast.co/business-sign-up';
	const DEFAULT_DASHBOARD_URL   = 'https://fast.co/business';

	/**
	 * Default PDP button styles.
	 */
	const DEFAULT_PDP_BUTTON_STYLES = <<<CSS
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

.woocommerce_after_add_to_cart_button .fast-pdp-or {
  top: -22px;
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
CSS;

	/**
	 * Default cart button styles.
	 */
	const DEFAULT_CART_BUTTON_STYLES = <<<CSS
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
CSS;

	/**
	 * Default mini cart button styles.
	 */
	const DEFAULT_MINI_CART_BUTTON_STYLES = <<<CSS
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
CSS;

	/**
	 * Default checkout button styles.
	 */
	const DEFAULT_CHECKOUT_BUTTON_STYLES = <<<CSS
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
CSS;

	/**
	 * Default login button styles.
	 */
	const DEFAULT_LOGIN_BUTTON_STYLES = <<<CSS
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
CSS;

	/**
	 * Helper that returns the value of an option if it is set,
	 * and sets and returns a default if the option was not set.
	 * This is similar to get_option($option, $default), except
	 * that it *sets* the option if it is not set instead of just
	 * returning a default.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_option/
	 *
	 * @param string $option  Name of the option to retrieve. Expected to not be SQL-escaped.
	 * @param mixed  $default Default value to set option to and return if the return value of get_option is falsey.
	 *
	 * @return mixed The value of the option if it is truthy, or the default if the option's value is falsey.
	 */
	public static function get_or_set_default( $option, $default ) {
		$val = \get_option( $option );
		if ( false !== $val ) {
			return $val;
		}

		\update_option( $option, $default );
		return $default;
	}

	/**
	 * Get or set checkbox option.
	 *
	 * @param string $option  Name of the option to retrieve.
	 * @param mixed  $default The default value to use.
	 *
	 * @return mixed
	 */
	protected static function get_checkbox_option( $option, $default ) {
		$not_set = $option . ' not set';
		$val     = \get_option( $option, $not_set );

		if ( $not_set === $val ) {
			$val = $default;
			\update_option( $option, $val );
		}

		return $val;
	}

	/**
	 * Convert a checkbox value to a boolean.
	 *
	 * @param mixed $value The checkbox value to convert.
	 *
	 * @return bool
	 */
	protected static function get_checkbox_bool( $value ) {
		return 0 !== (int) $value;
	}

	/**
	 * Get the Fast APP ID.
	 *
	 * @return string
	 */
	public static function get_app_id() {
		return \get_option( static::KEY_APP_ID, '' );
	}

	/**
	 * Get the test mode flag.
	 *
	 * @return bool
	 */
	public static function is_test_mode() {
		$test_mode = static::get_checkbox_option( static::KEY_TEST_MODE, '1' );

		return static::get_checkbox_bool( $test_mode );
	}

	/**
	 * Get the debug mode flag.
	 *
	 * @return bool
	 */
	public static function is_debug_mode() {
		$debug_mode = static::get_checkbox_option( static::KEY_DEBUG_MODE, 0 );

		return static::get_checkbox_bool( $debug_mode );
	}

	/**
	 * Get the list of users who can see the Fast Checkout buttons in test mode.
	 *
	 * @return array
	 */
	public static function get_test_mode_users() {
		return \get_option( static::KEY_TEST_MODE_USERS, array() );
	}

	/**
	 * Get the fast.js URL.
	 *
	 * @return string
	 */
	public static function get_fast_js_url() {
		return static::get_or_set_default( static::KEY_FAST_JS_URL, static::DEFAULT_FAST_JS_URL );
	}

	/**
	 * Get the Fast JWKS URL.
	 *
	 * @return string
	 */
	public static function get_fast_jwks_url() {
		return static::get_or_set_default( static::KEY_FAST_JWKS_URL, static::DEFAULT_FAST_JWKS_URL );
	}

	/**
	 * Get the Fast onboarding URL.
	 *
	 * @return string
	 */
	public static function get_onboarding_url() {
		return static::get_or_set_default( static::KEY_ONBOARDING_URL, static::DEFAULT_ONBOARDING_URL );
	}

	/**
	 * Get the Fast dashboard URL.
	 *
	 * @return string
	 */
	public static function get_dashboard_url() {
		return static::get_or_set_default( static::KEY_DASHBOARD_URL, static::DEFAULT_DASHBOARD_URL );
	}

	/**
	 * Get the load button styles flag.
	 *
	 * @return bool
	 */
	public static function should_load_button_styles() {
		$load_button_styles = static::get_checkbox_option( static::KEY_LOAD_BUTTON_STYLES, '1' );

		return static::get_checkbox_bool( $load_button_styles );
	}

	/**
	 * Get the dark mode flag.
	 *
	 * @return bool
	 */
	public static function should_use_dark_mode() {
		$use_dark_mode = static::get_checkbox_option( static::KEY_USE_DARK_MODE, 0 );

		return static::get_checkbox_bool( $use_dark_mode );
	}

	/**
	 * Get PDP button styles.
	 *
	 * @return string
	 */
	public static function get_pdp_button_styles() {
		return static::get_or_set_default( static::KEY_PDP_BUTTON_STYLES, static::DEFAULT_PDP_BUTTON_STYLES );
	}

	/**
	 * Get cart button styles.
	 *
	 * @return string
	 */
	public static function get_cart_button_styles() {
		return static::get_or_set_default( static::KEY_CART_BUTTON_STYLES, static::DEFAULT_CART_BUTTON_STYLES );
	}

	/**
	 * Get mini cart button styles.
	 *
	 * @return string
	 */
	public static function get_mini_cart_button_styles() {
		return static::get_or_set_default( static::KEY_MINI_CART_BUTTON_STYLES, static::DEFAULT_MINI_CART_BUTTON_STYLES );
	}

	/**
	 * Get checkout button styles.
	 *
	 * @return string
	 */
	public static function get_checkout_button_styles() {
		return static::get_or_set_default( static::KEY_CHECKOUT_BUTTON_STYLES, static::DEFAULT_CHECKOUT_BUTTON_STYLES );
	}

	/**
	 * Get login button styles.
	 *
	 * @return string
	 */
	public static function get_login_button_styles() {
		return static::get_or_set_default( static::KEY_LOGIN_BUTTON_STYLES, static::DEFAULT_LOGIN_BUTTON_STYLES );
	}

	/**
	 * Get the flag to show the login button in the footer.
	 *
	 * @return bool
	 */
	public static function should_show_login_in_footer() {
		$login_button_footer = static::get_checkbox_option( static::KEY_SHOW_LOGIN_BUTTON_FOOTER, '1' );

		return static::get_checkbox_bool( $login_button_footer );
	}

	/**
	 * Get the flag to disable multicurrency.
	 *
	 * @return bool
	 */
	public static function should_disable_multicurrency() {
		$disable_multicurrency = static::get_checkbox_option( static::KEY_DISABLE_MULTICURRENCY, 0 );

		return static::get_checkbox_bool( $disable_multicurrency );
	}

	/**
	 * Get list of products for which the Fast buttons should be hidden.
	 *
	 * @return array
	 */
	public static function get_products_to_hide_buttons() {
		return static::get_or_set_default( static::KEY_HIDE_BUTTON_PRODUCTS, array() );
	}

	/**
	 * Get the checkout redirect page ID.
	 *
	 * @return int
	 */
	public static function get_checkout_redirect_page() {
		return static::get_or_set_default( static::KEY_CHECKOUT_REDIRECT_PAGE, 0 );
	}

	/**
	 * Get the flag to redirect after PDP checkout.
	 *
	 * @return bool
	 */
	public static function should_redirect_after_pdp_checkout() {
		$redirect_after_pdp = static::get_checkbox_option( static::KEY_REDIRECT_AFTER_PDP, 0 );

		return static::get_checkbox_bool( $redirect_after_pdp );
	}

	/**
	 * Get the flag to clear the cart after PDP checkout.
	 *
	 * @return bool
	 */
	public static function should_clear_cart_after_pdp_checkout() {
		$clear_cart_after_pdp = static::get_checkbox_option( static::KEY_CLEAR_CART_AFTER_PDP, 0 );

		return static::get_checkbox_bool( $clear_cart_after_pdp );
	}

	/**
	 * Get the WordPress action to use for the PDP button placement.
	 *
	 * @return string
	 */
	public static function get_pdp_button_hook() {
		return static::get_or_set_default( static::KEY_PDP_BUTTON_HOOK, static::DEFAULT_PDP_BUTTON_HOOK );
	}

	/**
	 * Get the value of the other button hook for when "other" is selected.
	 *
	 * @return string
	 */
	public static function get_other_pdp_button_hook() {
		return static::get_or_set_default( static::KEY_PDP_BUTTON_HOOK_OTHER, '' );
	}

	/**
	 * Get the content of the button wrapper.
	 *
	 * @return string
	 */
	public static function get_button_wrapper_content() {
		return static::get_or_set_default( static::KEY_BUTTON_WRAPPER_CONTENT, '' );
	}

	/**
	 * Get the location of the button wrapper content.
	 *
	 * @return string
	 */
	public static function get_button_wrapper_content_location() {
		return static::get_or_set_default( static::KEY_BUTTON_WRAPPER_CONTENT_LOCATION, '' );
	}

	/**
	 * Get the flag to hide regular checkout buttons.
	 *
	 * @return bool
	 */
	public static function should_hide_regular_checkout_buttons() {
		$hide_regular_checkout_buttons = static::get_checkbox_option( static::KEY_HIDE_REGULAR_CHECKOUT_BUTTONS, 0 );

		return static::get_checkbox_bool( $hide_regular_checkout_buttons );
	}

	/**
	 * Get the list of settings that are timestampable.
	 *
	 * @return array
	 */
	public static function get_stampable_options() {
		return array(
			static::KEY_APP_ID,
			static::KEY_FAST_JS_URL,
			static::KEY_FAST_JWKS_URL,
			static::KEY_ONBOARDING_URL,
			static::KEY_DASHBOARD_URL,
			static::KEY_PDP_BUTTON_STYLES,
			static::KEY_CART_BUTTON_STYLES,
			static::KEY_MINI_CART_BUTTON_STYLES,
			static::KEY_CHECKOUT_BUTTON_STYLES,
			static::KEY_LOGIN_BUTTON_STYLES,
			static::KEY_HIDE_BUTTON_PRODUCTS,
			static::KEY_CHECKOUT_REDIRECT_PAGE,
			static::KEY_PDP_BUTTON_HOOK,
			static::KEY_PDP_BUTTON_HOOK_OTHER,
			static::KEY_BUTTON_WRAPPER_CONTENT,
			static::KEY_BUTTON_WRAPPER_CONTENT_LOCATION,
			static::KEY_TEST_MODE_USERS,
		);
	}

	/**
	 * Get the list of timestamps.
	 *
	 * @return array
	 */
	public static function get_timestamps() {
		return \get_option( static::KEY_TIMESTAMPS, array() );
	}

	/**
	 * Add timestamp.
	 *
	 * @param string $option The option key for which to add a timestamp.
	 */
	public static function add_timestamp( $option ) {
		if ( in_array( $option, Config::get_stampable_options(), true ) ) {
			$timestamps = Config::get_timestamps();

			$timestamps[ $option ] = time();

			\update_option( Config::KEY_TIMESTAMPS, $timestamps );
		}
	}
}