<?php
/**
 * Fast Plugin Settings Constants
 *
 * @package Fast
 */

define( 'FASTWC_SETTING_APP_ID', 'fast_app_id' );
define( 'FASTWC_SETTING_TEST_MODE', 'fast_test_mode' );
define( 'FASTWC_SETTING_TEST_MODE_NOT_SET', 'fast test mode not set' );
define( 'FASTWC_SETTING_FAST_JS_URL', 'fast_fast_js_url' );
define( 'FASTWC_SETTING_FAST_JWKS_URL', 'fast_fast_jwks_url' );
define( 'FASTWC_SETTING_ONBOARDING_URL', 'fast_onboarding_url' );
define( 'FASTWC_SETTING_HEADLESS_LINK_BASE', 'fastwc_headless_link_base' );
define( 'FASTWC_SETTING_HEADLESS_FAST_JS_URL', 'fastwc_headless_fast_js_url' );
define( 'FASTWC_SETTING_DEBUG_MODE', 'fastwc_debug_mode' );
define( 'FASTWC_SETTING_DEBUG_MODE_NOT_SET', 'fast debug mode not set' );
define( 'FASTWC_SETTING_LOAD_BUTTON_STYLES', 'fastwc_load_button_styles' );
define( 'FASTWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET', 'fast load button styles not set' );
define( 'FASTWC_SETTING_PDP_BUTTON_STYLES', 'fast_pdp_button_styles' );
define( 'FASTWC_SETTING_CART_BUTTON_STYLES', 'fast_cart_button_styles' );
define( 'FASTWC_SETTING_MINI_CART_BUTTON_STYLES', 'fast_mini_cart_button_styles' );
define( 'FASTWC_SETTING_CHECKOUT_BUTTON_STYLES', 'fast_checkout_button_styles' );
define( 'FASTWC_SETTING_LOGIN_BUTTON_STYLES', 'fast_login_button_styles' );
define( 'FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER', 'fastwc_show_login_button_footer' );
define( 'FASTWC_SETTING_LOGIN_FOOTER_NOT_SET', 'fast login in footer not set' );
define( 'FASTWC_SETTING_DISABLE_MULTICURRENCY', 'fastwc_disable_multicurrency' );
define( 'FASTWC_SETTING_HIDE_BUTTON_PRODUCTS', 'fast_hide_button_products' );
define( 'FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE', 'fastwc_checkout_redirect_page' );
define( 'FASTWC_SETTING_PDP_BUTTON_HOOK', 'fast_pdp_button_hook' );
define( 'FASTWC_DEFAULT_PDP_BUTTON_HOOK', 'woocommerce_after_add_to_cart_quantity' );
define( 'FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER', 'fast_pdp_button_hook_other' );
define( 'FASTWC_SETTING_HEADLESS_LINK_SLUG', 'fastwc_headless_link_slug' );
define( 'FASTWC_DEFAULT_HEADLESS_LINK_SLUG', 'fast-checkout' );
define( 'FASTWC_SETTING_SAVED_HEADLESS_LINK_SLUG', 'fastwc_saved_headless_link_slug' );
define( 'FASTWC_JWKS_URL', 'https://api.fast.co/v1/oauth2/jwks' );
define( 'FASTWC_JS_URL', 'https://js.fast.co/fast-woocommerce.js' );
define( 'FASTWC_ONBOARDING_URL', 'https://fast.co/business' );
define( 'FASTWC_HEADLESS_LINK_BASE', 'https://go.fast.co/' );
define( 'FASTWC_HEADLESS_FAST_JS_URL', 'https://js.fast.co/fast.js' );

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
