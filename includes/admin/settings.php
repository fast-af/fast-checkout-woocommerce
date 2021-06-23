<?php
/**
 * Fast Plugin Settings
 *
 * Adds config UI for wp-admin.
 *
 * @package Fast
 */

// Load admin constants.
require_once FASTWC_PATH . 'includes/admin/constants.php';
// Load admin fields.
require_once FASTWC_PATH . 'includes/admin/fields.php';

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
	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__( "Your Fast plugin won't work without an active WooCommerce installation.", 'fast' )
	);
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
 * Renders content of Fast settings page.
 */
function fastwc_settings_page_content() {
	$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'fast_app_info'; // phpcs:ignore

	$tabs = array(
		'fast_app_info'  => __( 'App Info', 'fast' ),
		'fast_styles'    => __( 'Styles', 'fast' ),
		'fast_options'   => __( 'Options', 'fast' ),
		'fast_test_mode' => __( 'Test Mode', 'fast' ),
	);
	?>
		<div class="wrap fast-settings">
			<h2><?php esc_html_e( 'Fast Settings', 'fast' ); ?></h2>

			<nav class="nav-tab-wrapper">
				<?php
				foreach ( $tabs as $tab_name => $tab_label ) :
					$tab_url   = sprintf( 'admin.php?page=fast&tab=%s', $tab_name );
					$tab_class = array( 'nav-tab' );
					if ( $active_tab === $tab_name ) {
						$tab_class[] = 'nav-tab-active';
					}
					$tab_class = implode( ' ', $tab_class );
					?>
				<a href="<?php echo esc_url( $tab_url ); ?>" class="<?php echo esc_attr( $tab_class ); ?>"><?php echo esc_html( $tab_label ); ?></a>
				<?php endforeach; ?>

				<?php
				if ( fastwc_should_show_advanced_settings() ) :
					$tab_url   = 'admin.php?page=fast&tab=fast_advanced';
					$tab_class = array( 'nav-tab' );
					if ( 'fast_advanced' === $active_tab ) {
						$tab_class[] = 'nav-tab-active';
					}
					$tab_class = implode( ' ', $tab_class );
					$tab_label = __( 'Advanced', 'fast' );
					?>
				<a href="<?php echo esc_url( $tab_url ); ?>" class="<?php echo esc_attr( $tab_class ); ?>"><?php echo esc_html( $tab_label ); ?></a>
				<?php endif; ?>
			</nav>

			<form method="post" action="options.php">
				<?php
				$valid_tab_contents   = array_keys( $tabs );
				$valid_tab_contents[] = 'fast_advanced';
				if ( ! in_array( $active_tab, $valid_tab_contents, true ) ) {
					$active_tab = 'fast_app_info';
				}
				settings_fields( $active_tab );
				do_settings_sections( $active_tab );
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

	$section_name = 'fast_app_info';
	add_settings_section( $section_name, '', false, $section_name );
	register_setting( $section_name, FASTWC_SETTING_APP_ID );

	$section_name = 'fast_styles';
	add_settings_section( $section_name, '', false, $section_name );
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
}

/**
 * Renders the App ID field.
 */
function fastwc_app_id_content() {
	$fastwc_setting_app_id              = fastwc_get_app_id();
	$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

	fastwc_settings_field_input(
		array(
			'name'        => 'fast_app_id',
			'value'       => $fastwc_setting_app_id,
			'description' => 'Don\'t have an app yet? Click <a href="' . esc_url( $fastwc_setting_fast_onboarding_url ) . '" target="_blank" rel="noopener">here</a> to create one.',
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
			'name'        => 'fastwc_disable_multicurrency',
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
			'name'  => 'fast_fast_js_url',
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
			'name'  => 'fast_fast_jwks_url',
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
			'name'  => 'fast_onboarding_url',
			'value' => $url,
		)
	);
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

			.fast-image-select {
				display: flex;
				flex-wrap: wrap;
				margin: 0 -10px;
				padding-top: 10px;
			}
			.fast-image-select--item {
				flex-basis: 50%;
				padding: 0 10px;
				margin: 0 0 20px;
				box-sizing: border-box;
			}
			@media screen and (max-width: 480px) {
				.fast-image-select--item {
					flex-basis: 100%;
				}
			}
			.fast-image-select--label-text {
				display: block;
				margin-bottom: 6px;
			}
			.fast-image-select--image {
				max-width: 100%;
				height: auto;
				border: 1px solid #bdbdbd;
			}
			.fast-image-select--input:checked + label .fast-image-select--image {
				border: 1px solid #666;
				box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
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
