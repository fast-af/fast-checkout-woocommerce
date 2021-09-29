<?php
/**
 * Fast Plugin Settings
 *
 * Adds config UI for wp-admin.
 *
 * @package Fast
 */

// Load admin notices.
require_once FASTWC_PATH . 'includes/admin/notices.php';
// Load admin constants.
require_once FASTWC_PATH . 'includes/admin/constants.php';
// Load admin fields.
require_once FASTWC_PATH . 'includes/admin/fields.php';

/**
 * Add timestamp when an option is updated.
 *
 * @param string $option    Name of the updated option.
 * @param mixed  $old_value The old option value.
 * @param mixed  $value     The new option value.
 */
function fastwc_updated_option( $option, $old_value, $value ) {
	if ( $old_value === $value ) {
		return;
	}

	$stampable_options = array(
		FASTWC_SETTING_APP_ID,
		FASTWC_SETTING_FAST_JS_URL,
		FASTWC_SETTING_FAST_JWKS_URL,
		FASTWC_SETTING_ONBOARDING_URL,
		FASTWC_SETTING_PDP_BUTTON_STYLES,
		FASTWC_SETTING_CART_BUTTON_STYLES,
		FASTWC_SETTING_MINI_CART_BUTTON_STYLES,
		FASTWC_SETTING_CHECKOUT_BUTTON_STYLES,
		FASTWC_SETTING_LOGIN_BUTTON_STYLES,
		FASTWC_SETTING_HIDE_BUTTON_PRODUCTS,
		FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE,
		FASTWC_SETTING_PDP_BUTTON_HOOK,
		FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER,
	);

	if ( in_array( $option, $stampable_options, true ) ) {
		$fastwc_settings_timestamps = get_option( FASTWC_SETTINGS_TIMESTAMPS, array() );

		$fastwc_settings_timestamps[ $option ] = time();

		update_option( FASTWC_SETTINGS_TIMESTAMPS, $fastwc_settings_timestamps );
	}
}
add_action( 'updated_option', 'fastwc_updated_option', 10, 3 );

add_action( 'admin_menu', 'fastwc_admin_create_menu' );
add_action( 'admin_init', 'fastwc_maybe_redirect_after_activation', 1 );
add_action( 'admin_init', 'fastwc_admin_setup_sections' );
add_action( 'admin_init', 'fastwc_admin_setup_fields' );

/**
 * Add plugin action links to the Fast plugin on the plugins page.
 *
 * @param array  $plugin_meta The list of links for the plugin.
 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
 * @param array  $plugin_data An array of plugin data.
 * @param string $status      Status filter currently applied to the plugin list. Possible
 *                            values are: 'all', 'active', 'inactive', 'recently_activated',
 *                            'upgrade', 'mustuse', 'dropins', 'search', 'paused',
 *                            'auto-update-enabled', 'auto-update-disabled'.
 *
 * @return array
 */
function fastwc_admin_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( plugin_basename( FASTWC_PATH . 'fast.php' ) !== $plugin_file ) {
		return $plugin_meta;
	}

	// Add "Become a Seller!" CTA if the Fast App ID has not yet been set.
	if ( function_exists( 'fastwc_get_app_id' ) ) {
		$fast_app_id = fastwc_get_app_id();

		if ( empty( $fast_app_id ) ) {
			$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

			$plugin_meta[] = sprintf(
				'<a href="%1$s" target="_blank" rel="noopener"><strong>%2$s</strong></a>',
				esc_url( $fastwc_setting_fast_onboarding_url ),
				esc_html__( 'Become a Seller!', 'fast' )
			);
		}
	}

	$plugin_meta[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( admin_url( 'admin.php?page=fast' ) ),
		esc_html__( 'Settings', 'fast' )
	);

	return $plugin_meta;
}
add_action( 'plugin_row_meta', 'fastwc_admin_plugin_row_meta', 10, 4 );

/**
 * Registers the Fast menu within wp-admin.
 */
function fastwc_admin_create_menu() {
	// Add the menu item and page.
	$page_title = __( 'Fast Settings', 'fast' );
	$menu_title = __( 'Fast', 'fast' );
	$capability = 'manage_options';
	$slug       = 'fast';
	$callback   = 'fastwc_settings_page_content';
	$icon       = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMjM4cHgiIGhlaWdodD0iMjM4cHgiIHZpZXdCb3g9IjAgMCAyMzggMjM4IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPHRpdGxlPkFydGJvYXJkPC90aXRsZT4KICAgIDxnIGlkPSJQYWdlLTEiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxnIGlkPSJBcnRib2FyZCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTEwOC4wMDAwMDAsIC02Ny4wMDAwMDApIiBmaWxsPSIjRkZGRkZGIiBmaWxsLXJ1bGU9Im5vbnplcm8iPgogICAgICAgICAgICA8cGF0aCBkPSJNMjc5LjYxMjEzOSwxMjkuODA5NTMxIEwyMDcuMTMzMTkzLDEyOS44MDk1MzEgTDIwNy4xMzMxOTMsMTcwLjA0NzY1OSBMMjY5Ljk0Nzk4MiwxNzAuMDQ3NjU5IEwyNjkuOTQ3OTgyLDE5OC44NTczMzQgTDIwNy4xMzMxOTMsMTk4Ljg1NzMzNCBMMjA3LjEzMzE5MywyNzEgTDE3NCwyNzEgTDE3NCwxMTQuODA5NTYxIEMxNzQsMTEwLjY4MjUyMyAxNzUuMzgwNTUzLDEwNy4zNDkyMSAxNzguMTQxNjUxLDEwNC44MDk1MzUgQzE4MC45MDI3NTYsMTAyLjI2OTg0NSAxODQuMjAwNzM1LDEwMSAxODguMDM1NTcxLDEwMSBMMjc5LjYxMjEzOSwxMDEgTDI3OS42MTIxMzksMTI5LjgwOTUzMSBaIiBpZD0iUGF0aCI+PC9wYXRoPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+';
	$position   = 100;

	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );

	if ( fastwc_gutenberg_is_active() ) {
		// Add "Settings" submenu that points to the top-level menu item.
		add_submenu_page( $slug, $page_title, __( 'Settings', 'fast' ), $capability, $slug );

		// Add submenu that  points to the fastwc-headless-link post type.
		add_submenu_page(
			$slug,
			__( 'Fast Headless Checkout', 'fast' ), // Page title.
			__( 'Headless Checkout', 'fast' ), // Menu title.
			$capability, // Capability.
			'edit.php?post_type=fastwc_headless_link' // Slug.
		);
	}
}

/**
 * Maybe redirect to the Fast settings page after activation.
 */
function fastwc_maybe_redirect_after_activation() {
	$activated = get_option( FASTWC_PLUGIN_ACTIVATED, false );

	if ( $activated ) {
		// Delete the flag to prevent an endless redirect loop.
		delete_option( FASTWC_PLUGIN_ACTIVATED );

		// Redirect to the Fast settings page.
		wp_safe_redirect(
			esc_url(
				admin_url( 'admin.php?page=fast' )
			)
		);
		exit;
	}
}

/**
 * Check if the plugin should show the Fast advanced settings.
 *
 * @return bool
 */
function fastwc_should_show_advanced_settings() {
	return preg_match( '/@fast.co$/i', wp_get_current_user()->user_email );
}

/**
 * Get the list of tabs for the Fast settings page.
 *
 * @return array
 */
function fastwc_get_settings_tabs() {
	return array(
		'fast_app_info'  => __( 'App Info', 'fast' ),
		'fast_styles'    => __( 'Styles', 'fast' ),
		'fast_options'   => __( 'Options', 'fast' ),
		'fast_test_mode' => __( 'Test Mode', 'fast' ),
		'fast_support'   => __( 'Support', 'fast' ),
		'fast_status'    => __( 'Status', 'fast' ),
	);
}

/**
 * Get the active tab in the Fast settings page.
 *
 * @return string
 */
function fastwc_get_active_tab() {
	return isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'fast_app_info'; // phpcs:ignore
}

/**
 * Renders content of Fast settings page.
 */
function fastwc_settings_page_content() {
	fastwc_load_template( 'admin/fast-settings' );
}

/**
 * Sets up sections for Fast settings page.
 */
function fastwc_admin_setup_sections() {

	$section_name = 'fast_app_info';
	add_settings_section( $section_name, '', false, $section_name );
	register_setting( $section_name, FASTWC_SETTING_APP_ID );

	$section_name = 'fast_styles';
	add_settings_section( $section_name, '', false, $section_name );
	register_setting( $section_name, FASTWC_SETTING_LOAD_BUTTON_STYLES );
	register_setting( $section_name, FASTWC_SETTING_PDP_BUTTON_STYLES );
	register_setting( $section_name, FASTWC_SETTING_CART_BUTTON_STYLES );
	register_setting( $section_name, FASTWC_SETTING_MINI_CART_BUTTON_STYLES );
	register_setting( $section_name, FASTWC_SETTING_CHECKOUT_BUTTON_STYLES );
	register_setting( $section_name, FASTWC_SETTING_LOGIN_BUTTON_STYLES );

	$section_name = 'fast_options';
	add_settings_section( $section_name, '', false, $section_name );
	register_setting( $section_name, FASTWC_SETTING_PDP_BUTTON_HOOK );
	register_setting( $section_name, FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER );
	register_setting( $section_name, FASTWC_SETTING_HIDE_BUTTON_PRODUCTS );
	register_setting( $section_name, FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE );
	register_setting( $section_name, FASTWC_SETTING_HEADLESS_LINK_SLUG );
	register_setting( $section_name, FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER );

	$section_name = 'fast_test_mode';
	add_settings_section( $section_name, '', false, $section_name );
	register_setting( $section_name, FASTWC_SETTING_TEST_MODE );
	register_setting( $section_name, FASTWC_SETTING_DEBUG_MODE );
	register_setting( $section_name, FASTWC_SETTING_DISABLE_MULTICURRENCY );

	// For now, only allow fast users to see advanced settings.
	if ( fastwc_should_show_advanced_settings() ) {
		$section_name = 'fast_advanced';
		add_settings_section( $section_name, '', false, $section_name );
		register_setting( $section_name, FASTWC_SETTING_FAST_JS_URL );
		register_setting( $section_name, FASTWC_SETTING_FAST_JWKS_URL );
		register_setting( $section_name, FASTWC_SETTING_ONBOARDING_URL );
		register_setting( $section_name, FASTWC_SETTING_HEADLESS_LINK_BASE );
		register_setting( $section_name, FASTWC_SETTING_HEADLESS_FAST_JS_URL );
	}
}

/**
 * Sets up fields for Fast settings page.
 */
function fastwc_admin_setup_fields() {
	// App Info settings.
	$settings_section = 'fast_app_info';
	add_settings_field( FASTWC_SETTING_APP_ID, __( 'App ID', 'fast' ), 'fastwc_app_id_content', $settings_section, $settings_section );

	// Button style settings.
	$settings_section = 'fast_styles';
	add_settings_field( FASTWC_SETTING_LOAD_BUTTON_STYLES, __( 'Load Button Styles', 'fast' ), 'fastwc_load_button_styles', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_PDP_BUTTON_STYLES, __( 'Product page button styles', 'fast' ), 'fastwc_pdp_button_styles_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_CART_BUTTON_STYLES, __( 'Cart page button styles', 'fast' ), 'fastwc_cart_button_styles_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_MINI_CART_BUTTON_STYLES, __( 'Mini cart widget button styles', 'fast' ), 'fastwc_mini_cart_button_styles_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_CHECKOUT_BUTTON_STYLES, __( 'Checkout page button styles', 'fast' ), 'fastwc_checkout_button_styles_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_LOGIN_BUTTON_STYLES, __( 'Login button styles', 'fast' ), 'fastwc_login_button_styles_content', $settings_section, $settings_section );

	// Button options settings.
	$settings_section = 'fast_options';
	add_settings_field( FASTWC_SETTING_PDP_BUTTON_HOOK, __( 'Select Product Button Location', 'fast' ), 'fastwc_pdp_button_hook', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER, __( 'Enter Alternate Product Button Location', 'fast' ), 'fastwc_pdp_button_hook_other', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_HIDE_BUTTON_PRODUCTS, __( 'Hide Buttons for these Products', 'fast' ), 'fastwc_hide_button_products', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE, __( 'Checkout Redirect Page', 'fast' ), 'fastwc_checkout_redirect_page', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_HEADLESS_LINK_SLUG, __( 'Headless Checkout Link Path', 'fast' ), 'fastwc_headless_link_slug', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER, __( 'Display Login in Footer', 'fast' ), 'fastwc_show_login_button_footer', $settings_section, $settings_section );

	// Test Mode settings.
	$settings_section = 'fast_test_mode';
	add_settings_field( FASTWC_SETTING_TEST_MODE, __( 'Test Mode', 'fast' ), 'fastwc_test_mode_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_DEBUG_MODE, __( 'Debug Mode', 'fast' ), 'fastwc_debug_mode_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_DISABLE_MULTICURRENCY, __( 'Disable Multicurrency Support', 'fast' ), 'fastwc_disable_multicurrency_content', $settings_section, $settings_section );

	// Advanced settings.
	$settings_section = 'fast_advanced';
	add_settings_field( FASTWC_SETTING_FAST_JS_URL, __( 'Fast JS URL', 'fast' ), 'fastwc_fastwc_js_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_FAST_JWKS_URL, __( 'Fast JWKS URL', 'fast' ), 'fastwc_fastwc_jwks_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_ONBOARDING_URL, __( 'Fast Onboarding URL', 'fast' ), 'fastwc_onboarding_url_content', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_HEADLESS_LINK_BASE, __( 'Headless Checkout Link Base', 'fast' ), 'fastwc_headless_link_base', $settings_section, $settings_section );
	add_settings_field( FASTWC_SETTING_HEADLESS_FAST_JS_URL, __( 'Headless Fast JS URL', 'fast' ), 'fastwc_headless_fast_js_url', $settings_section, $settings_section );
}

/**
 * Renders the App ID field.
 */
function fastwc_app_id_content() {
	$fastwc_setting_app_id              = fastwc_get_app_id();
	$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

	$description = '';
	if ( empty( $fastwc_setting_app_id ) ) {
		$description = sprintf(
			'%1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a>',
			esc_html__( "Don't have an app yet?", 'fast' ),
			esc_url( $fastwc_setting_fast_onboarding_url ),
			esc_html__( 'Become a seller on Fast.co to get an App ID and enter it here.', 'fast' )
		);
	}

	fastwc_settings_field_input(
		array(
			'name'        => 'fast_app_id',
			'value'       => $fastwc_setting_app_id,
			'description' => $description,
		)
	);
}

/**
 * Renders a checkbox to set whether or not to load the button styles.
 */
function fastwc_load_button_styles() {
	$fastwc_load_button_styles = get_option( FASTWC_SETTING_LOAD_BUTTON_STYLES, FASTWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET );

	if ( FASTWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET === $fastwc_load_button_styles ) {
		// If the option is FASTWC_SETTING_LOAD_BUTTON_STYLES_NOT_SET, then it hasn't yet been set. In this case, we
		// want to configure it to true.
		$fastwc_load_button_styles = '1';
		update_option( FASTWC_SETTING_LOAD_BUTTON_STYLES, $fastwc_load_button_styles );
	}

	fastwc_settings_field_checkbox(
		array(
			'name'        => FASTWC_SETTING_LOAD_BUTTON_STYLES,
			'current'     => $fastwc_load_button_styles,
			'label'       => __( 'Load the button styles as configured in the settings.', 'fast' ),
			'description' => __( 'When this box is checked, the styles configured below will be loaded to provide additional styling to the loading of the Fast buttons.', 'fast' ),
		)
	);
}

/**
 * Renders the PDP button styles field.
 */
function fastwc_pdp_button_styles_content() {
	$fastwc_setting_pdp_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_PDP_BUTTON_STYLES, FASTWC_SETTING_PDP_BUTTON_STYLES_DEFAULT );

	fastwc_settings_field_textarea(
		array(
			'name'  => 'fast_pdp_button_styles',
			'value' => $fastwc_setting_pdp_button_styles,
		)
	);
}

/**
 * Renders the cart button styles field.
 */
function fastwc_cart_button_styles_content() {
	$fastwc_setting_cart_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_CART_BUTTON_STYLES, FASTWC_SETTING_CART_BUTTON_STYLES_DEFAULT );

	fastwc_settings_field_textarea(
		array(
			'name'  => 'fast_cart_button_styles',
			'value' => $fastwc_setting_cart_button_styles,
		)
	);
}

/**
 * Renders the mini-cart button styles field.
 */
function fastwc_mini_cart_button_styles_content() {
	$fastwc_setting_mini_cart_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_MINI_CART_BUTTON_STYLES, FASTWC_SETTING_MINI_CART_BUTTON_STYLES_DEFAULT );

	fastwc_settings_field_textarea(
		array(
			'name'  => 'fast_mini_cart_button_styles',
			'value' => $fastwc_setting_mini_cart_button_styles,
		)
	);
}

/**
 * Renders the checkout button styles field.
 */
function fastwc_checkout_button_styles_content() {
	$fastwc_setting_checkout_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_CHECKOUT_BUTTON_STYLES, FASTWC_SETTING_CHECKOUT_BUTTON_STYLES_DEFAULT );

	fastwc_settings_field_textarea(
		array(
			'name'  => 'fast_checkout_button_styles',
			'value' => $fastwc_setting_checkout_button_styles,
		)
	);
}

/**
 * Renders the login button styles field.
 */
function fastwc_login_button_styles_content() {
	$fastwc_setting_login_button_styles = fastwc_get_option_or_set_default( FASTWC_SETTING_LOGIN_BUTTON_STYLES, FASTWC_SETTING_LOGIN_BUTTON_STYLES_DEFAULT );

	fastwc_settings_field_textarea(
		array(
			'name'  => 'fast_login_button_styles',
			'value' => $fastwc_setting_login_button_styles,
		)
	);
}

/**
 * Renders the PDP Button Hook field.
 */
function fastwc_pdp_button_hook() {
	$fastwc_setting_pdp_button_hook = fastwc_get_option_or_set_default( FASTWC_SETTING_PDP_BUTTON_HOOK, FASTWC_DEFAULT_PDP_BUTTON_HOOK );

	$options = array(
		'woocommerce_before_add_to_cart_quantity' => array(
			'label' => __( 'Before Quantity Selection', 'fast' ),
			'image' => FASTWC_URL . 'assets/img/before-quantity-selection.png',
		),
		'woocommerce_after_add_to_cart_quantity'  => array(
			'label' => __( 'After Quantity Selection', 'fast' ),
			'image' => FASTWC_URL . 'assets/img/after-quantity-selection.png',
		),
		'woocommerce_after_add_to_cart_button'    => array(
			'label' => __( 'After Add to Cart Button', 'fast' ),
			'image' => FASTWC_URL . 'assets/img/after-atc-button.png',
		),
		'other'                                   => array(
			'label' => __( 'Other (for advanced users only)', 'fast' ),
			'image' => FASTWC_URL . 'assets/img/other.png',
		),
	);

	fastwc_settings_field_image_select(
		array(
			'name'        => FASTWC_SETTING_PDP_BUTTON_HOOK,
			'description' => __( 'Select a location within the Add to Cart form to display the Fast Product Checkout button.', 'fast' ),
			'value'       => $fastwc_setting_pdp_button_hook,
			'options'     => $options,
		)
	);
}

/**
 * Renders the PDP Button Hook alternate field.
 */
function fastwc_pdp_button_hook_other() {
	$fastwc_setting_pdp_button_hook_other = fastwc_get_option_or_set_default( FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER, '' );

	fastwc_settings_field_input(
		array(
			'name'        => FASTWC_SETTING_PDP_BUTTON_HOOK_OTHER,
			'description' => __( 'Enter an alternative location for displaying the Fast Product Checkout button. For advanced users only.', 'fast' ),
			'value'       => $fastwc_setting_pdp_button_hook_other,
		)
	);
}

/**
 * Renders the Hide Buttons for Products field.
 */
function fastwc_hide_button_products() {
	$fastwc_setting_hide_button_products = fastwc_get_option_or_set_default( FASTWC_SETTING_HIDE_BUTTON_PRODUCTS, array() );

	$selected = array();
	if ( ! empty( $fastwc_setting_hide_button_products ) ) {
		if ( ! is_array( $fastwc_setting_hide_button_products ) ) {
			$fastwc_setting_hide_button_products = array( $fastwc_setting_hide_button_products );
		}

		$fastwc_hide_products = wc_get_products(
			array(
				'include' => $fastwc_setting_hide_button_products,
			)
		);

		foreach ( $fastwc_hide_products as $fastwc_hide_product ) {
			$selected[ $fastwc_hide_product->get_id() ] = $fastwc_hide_product->get_name();
		}
	}

	fastwc_settings_field_ajax_select(
		array(
			'name'        => FASTWC_SETTING_HIDE_BUTTON_PRODUCTS,
			'selected'    => $selected,
			'class'       => 'fast-select fast-select--hide-button-products',
			'description' => __( 'Select products for which the Fast checkout button should be hidden', 'fast' ),
			'nonce'       => 'search-products',
		)
	);
}

/**
 * Renders the Checkout Redirect Page field.
 */
function fastwc_checkout_redirect_page() {
	$fastwc_setting_checkout_redirect_page = fastwc_get_option_or_set_default( FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE, 0 );

	$selected = array();
	if ( ! empty( $fastwc_setting_checkout_redirect_page ) ) {
		$fastwc_checkout_redirect_page = get_post( $fastwc_setting_checkout_redirect_page );

		if ( ! empty( $fastwc_checkout_redirect_page ) ) {
			$selected[ $fastwc_checkout_redirect_page->ID ] = $fastwc_checkout_redirect_page->post_title;
		}
	}

	fastwc_settings_field_ajax_select(
		array(
			'name'        => FASTWC_SETTING_CHECKOUT_REDIRECT_PAGE,
			'selected'    => $selected,
			'class'       => 'fast-select fast-select--checkout-redirect-page',
			'description' => __( 'Select a page to redirect to after a successful cart checkout. Leave blank to redirect to the cart.', 'fast' ),
			'nonce'       => 'search-pages',
			'multiple'    => false,
		)
	);
}

/**
 * Renders the Headless Checkout Link Path field.
 */
function fastwc_headless_link_slug() {
	$fastwc_headless_link_slug = fastwc_get_option_or_set_default( FASTWC_SETTING_HEADLESS_LINK_SLUG, FASTWC_DEFAULT_HEADLESS_LINK_SLUG );

	fastwc_settings_field_input(
		array(
			'name'        => FASTWC_SETTING_HEADLESS_LINK_SLUG,
			'description' => __( 'Enter the path prefix to use for headless checkout links.', 'fast' ),
			'value'       => $fastwc_headless_link_slug,
		)
	);
}

/**
 * Renders the show login in footer field.
 */
function fastwc_show_login_button_footer() {
	$fastwc_show_login_button_footer = get_option( FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER, FASTWC_SETTING_LOGIN_FOOTER_NOT_SET );

	if ( FASTWC_SETTING_LOGIN_FOOTER_NOT_SET === $fastwc_show_login_button_footer ) {
		// If the option is FASTWC_SETTING_LOGIN_FOOTER_NOT_SET, then it hasn't yet been set. In this case, we
		// want to configure it to true.
		$fastwc_show_login_button_footer = '1';
		update_option( FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER, $fastwc_show_login_button_footer );
	}

	fastwc_settings_field_checkbox(
		array(
			'name'        => FASTWC_SETTING_SHOW_LOGIN_BUTTON_FOOTER,
			'current'     => $fastwc_show_login_button_footer,
			'label'       => __( 'Display Fast Login Button in Footer', 'fast' ),
			'description' => __( 'The Fast Login button displays in the footer by default for non-logged in users. Uncheck this option to prevent the Fast Login button from displaying in the footer.', 'fast' ),
		)
	);
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

	fastwc_settings_field_checkbox(
		array(
			'name'        => 'fast_test_mode',
			'current'     => $fastwc_test_mode,
			'label'       => __( 'Enable test mode', 'fast' ),
			'description' => __( 'When test mode is enabled, only logged-in admin users will see the Fast Checkout button.', 'fast' ),
		)
	);
}

/**
 * Renders the Debug Mode field.
 */
function fastwc_debug_mode_content() {
	$fastwc_debug_mode = get_option( FASTWC_SETTING_DEBUG_MODE, FASTWC_SETTING_DEBUG_MODE_NOT_SET );

	if ( FASTWC_SETTING_DEBUG_MODE_NOT_SET === $fastwc_debug_mode ) {
		// If the option is FASTWC_SETTING_DEBUG_MODE_NOT_SET, then it hasn't yet been set. In this case, we
		// want to configure debug mode to be off.
		$fastwc_debug_mode = 0;
		update_option( FASTWC_SETTING_DEBUG_MODE, $fastwc_debug_mode );
	}

	fastwc_settings_field_checkbox(
		array(
			'name'        => FASTWC_SETTING_DEBUG_MODE,
			'current'     => $fastwc_debug_mode,
			'label'       => __( 'Enable debug mode', 'fast' ),
			'description' => __( 'When debug mode is enabled, the Fast plugin will maintain an error log.', 'fast' ),
		)
	);
}

/**
 * Renders the Disable Multicurrency field.
 */
function fastwc_disable_multicurrency_content() {
	$fastwc_disable_multicurrency = get_option( FASTWC_SETTING_DISABLE_MULTICURRENCY, 0 );

	fastwc_settings_field_checkbox(
		array(
			'name'        => FASTWC_SETTING_DISABLE_MULTICURRENCY,
			'current'     => $fastwc_disable_multicurrency,
			'label'       => __( 'Disable Multicurrency Support', 'fast' ),
			'description' => __( 'Disable multicurrency support in Fast Checkout.', 'fast' ),
		)
	);
}

/**
 * Renders the fast.js URL field.
 */
function fastwc_fastwc_js_content() {
	$fastwc_setting_fast_js_url = fastwc_get_option_or_set_default( FASTWC_SETTING_FAST_JS_URL, FASTWC_JS_URL );

	fastwc_settings_field_input(
		array(
			'name'  => FASTWC_SETTING_FAST_JS_URL,
			'value' => $fastwc_setting_fast_js_url,
		)
	);
}

/**
 * Renders the Fast JWKS URL field.
 */
function fastwc_fastwc_jwks_content() {
	$fastwc_setting_fast_jwks_url = fastwc_get_option_or_set_default( FASTWC_SETTING_FAST_JWKS_URL, FASTWC_JWKS_URL );

	fastwc_settings_field_input(
		array(
			'name'  => FASTWC_SETTING_FAST_JWKS_URL,
			'value' => $fastwc_setting_fast_jwks_url,
		)
	);
}

/**
 * Renders the onboarding URL field.
 */
function fastwc_onboarding_url_content() {
	$url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

	fastwc_settings_field_input(
		array(
			'name'  => FASTWC_SETTING_ONBOARDING_URL,
			'value' => $url,
		)
	);
}

/**
 * Renders the headless link base field.
 */
function fastwc_headless_link_base() {
	$url = fastwc_get_option_or_set_default( FASTWC_SETTING_HEADLESS_LINK_BASE, FASTWC_HEADLESS_LINK_BASE );

	fastwc_settings_field_input(
		array(
			'name'  => FASTWC_SETTING_HEADLESS_LINK_BASE,
			'value' => $url,
		)
	);
}

/**
 * Renders the headless fast.js URL field.
 */
function fastwc_headless_fast_js_url() {
	$fastwc_headless_fast_js_url = fastwc_get_option_or_set_default( FASTWC_SETTING_HEADLESS_FAST_JS_URL, FASTWC_HEADLESS_FAST_JS_URL );

	fastwc_settings_field_input(
		array(
			'name'  => FASTWC_SETTING_HEADLESS_FAST_JS_URL,
			'value' => $fastwc_headless_fast_js_url,
		)
	);
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
	if ( false !== $val ) {
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

/**
 * Search pages to return for the page select Ajax.
 */
function fastwc_ajax_search_pages() {
	check_ajax_referer( 'search-pages', 'security' );

	$return = array();

	if ( isset( $_GET['term'] ) ) {
		$q_term = (string) sanitize_text_field( wp_unslash( $_GET['term'] ) );
	}

	if ( empty( $q_term ) ) {
		wp_die();
	}

	$search_results = new WP_Query(
		array(
			's'              => $q_term,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'posts_per_page' => -1,
		)
	);

	if ( $search_results->have_posts() ) {
		while ( $search_results->have_posts() ) {
			$search_results->the_post();

			$return[ get_the_ID() ] = get_the_title();
		}
		wp_reset_postdata();
	}

	wp_send_json( $return );
}
add_action( 'wp_ajax_fastwc_search_pages', 'fastwc_ajax_search_pages' );
