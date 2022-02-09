=== Fast Checkout for WooCommerce ===
Contributors: fastaf, corywebb
Tags: fast, fast checkout, checkout, woocommerce, woocommerce payment, woocommerce checkout, quick checkout, 1 click checkout, one click checkout
Requires at least: 5.1
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.1.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Install the Checkout button that increases conversion, boosts sales and delights customers.
 
== Description ==
 
Install the Checkout button that increases conversion, boosts sales and delights customers.

Fast Checkout enables a hassle-free, secure one-click checkout experience for your customers. Install it in minutes so your customers can check out in seconds.

Simply install & configure the Fast Checkout for WooCommerce WordPress plugin to start seeing fewer abandoned carts and more sales for your business. Our top sellers have seen a 20-35% uplift in conversion within one week of installing Fast Checkout.
 
== Installation ==
 
Follow our installation guide over on [GitHub](https://github.com/fast-af/fast-checkout-woocommerce#readme)
 
== Frequently Asked Questions ==
 
= How much does Fast cost? =
 
Fast replaces your current payment processor. You can see our fee schedule, similar to Stripe's, on our [pricing page](https://www.fast.co/pricing). 

 
== Screenshots ==
 

 
== Changelog ==

= 1.1.15 =

* Add additional hooks to help facilitate customization by developers.
* Add an option to be able to add content above or below the Fast Checkout buttons.

= 1.1.14 =

* Add exit after redirect.

= 1.1.13 =

* Update to use get_query_var in place of $_GET to retrieve URL query parameters.
* Fix PHP notice.

= 1.1.12 =

* Update order redirect functionality to enable custom redirect URL's.

= 1.1.11 =

* Update order endpoint to only apply coupons if they are different from the coupon already applied to the order.

= 1.1.10 =

* Update to add support for dynamic pricing plugin.
* Add option to select users to view Fast in Test Mode.
* Add option to hide WooCommerce cart checkout buttons.
* Enqueue Fast admin scripts only on Fast settings pages.

= 1.1.9 =

* Add tools to enable support for third-party plugins
* Update settings tab and include onboarding link to help new stores onboard with Fast

= 1.1.8 =

* Add an option to enable/disable dark mode for displaying the Fast buttons.
* Update the "Select Product Button Location" option to help the selected option stand out more.
* Update the webhook check to remove the check for product webhooks.

= 1.1.7 =

* Update shipping endpoint to get shipping address from order if no address passed in and order ID is passed in.

= 1.1.6 =

* Link to the Fast settings page from the plugins page.
* Redirect to the Fast settings page after activation.
* Add a [changelog.txt](https://raw.githubusercontent.com/fast-af/fast-checkout-woocommerce/main/changelog.txt) file.
* Add "Last updates" timestamps to some of the settings in the Fast settings page.
* Add opt-in tool to track active installations

= 1.1.5 =

* Update the webhook check to verify installation of the Fast WooCommerce webhooks.

= 1.1.4 =

* Add validation to shipping address parameter in shipping REST API route.

= 1.1.3 =
* Minor bug fixes.
* Add a plugin status tab.
* Display admin notice when webhooks are disabled.
* Refactor REST API routes.

= 1.1.2 =
* Update the function that checks for an active WooCommerce installation.

= 1.1.1 =
* Add API endpoint to support partial order refunds.
* Update how button styles are loaded.
* Add ability to include product options attribute on Fast Checkout product buttons.
* Add improvements to Fast Checkout product button Gutenberg block UI.

= 1.1.0 =
* Add widgets to display the Fast Checkout and Fast Login buttons.
* Add Gutenberg block types to display the Fast checkout and Fast Login buttons.
* Add an option to prevent the Fast Login button from being displayed in the footer.
* Display admin notices whenever the Fast plugin is in Test Mode or Debug Mode.
* Add a footer to the Fast settings page that includes useful links and the latest version number of the plugin.
* Replace inline admin CSS with enqueued CSS file.

[See changelog.txt for the complete Fast Checkout for WooCommerce changelog.](https://raw.githubusercontent.com/fast-af/fast-checkout-woocommerce/main/changelog.txt)
