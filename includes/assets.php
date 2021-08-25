<?php
/**
 * Loads fast-woocommerce.js in footer.
 *
 * @package Fast
 */

/**
 * Enqueue the Fast Woocommerce script.
 */
function fastwc_enqueue_script() {
	$fastwc_js = fastwc_get_option_or_set_default( FASTWC_SETTING_FAST_JS_URL, FASTWC_JS_URL );
	wp_enqueue_script( 'fast-woocommerce', $fastwc_js, array(), '1.20.0', true );
}
add_action( 'wp_enqueue_scripts', 'fastwc_enqueue_script' );
add_action( 'login_enqueue_scripts', 'fastwc_enqueue_script' );

/**
 * Enqueue admin assets.
 */
function fastwc_admin_enqueue_scripts() {
	/**
	 * Load the Select2 library.
	 *
	 * @version 4.0.13
	 *
	 * @see https://select2.org/.
	 */
	$select2_version = '4.0.13';
	wp_enqueue_script(
		'select2',
		FASTWC_URL . 'assets/vendor/select2/select2.min.js',
		array( 'jquery' ),
		$select2_version,
		true
	);
	wp_enqueue_style(
		'select2',
		FASTWC_URL . 'assets/vendor/select2/select2.min.css',
		array(),
		$select2_version
	);

	wp_enqueue_script(
		'fastwc-admin-js',
		FASTWC_URL . 'assets/dist/scripts.min.js',
		array( 'jquery', 'select2' ),
		FASTWC_VERSION,
		true
	);

	$current_screen = get_current_screen();

	if ( ! empty( $current_screen ) && isset( $current_screen->id ) && 'toplevel_page_fast' !== $current_screen->id ) {
		return;
	}
	wp_enqueue_style(
		'fast-admin-css',
		FASTWC_URL . 'assets/dist/styles.css',
		array(),
		FASTWC_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'fastwc_admin_enqueue_scripts' );

/**
 * Load the styles in the head.
 */
function fastwc_wp_head() {
	$fastwc_load_button_styles = get_option( FASTWC_SETTING_LOAD_BUTTON_STYLES, true );

	if ( empty( $fastwc_load_button_styles ) ) {
		return;
	}

	$button_styles = fastwc_get_button_styles();

	if ( empty( $button_styles ) ) {
		return;
	}
	?>
<style>
	<?php echo esc_html( $button_styles ); ?>
</style>
	<?php
}
add_action( 'wp_head', 'fastwc_wp_head' );

/**
 * Enqueue block editor assets for the Gutenberg blocks.
 */
function fastwc_enqueue_block_editor_assets() {

	// Enqueue the script.
	$fastwc_block_editor_js = 'fastwc-block-editor-js';
	wp_enqueue_script(
		$fastwc_block_editor_js,
		FASTWC_URL . 'assets/dist/blocks/index.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-components', 'wp-element' ),
		FASTWC_VERSION,
		true
	);

	$fastwc_app_id = fastwc_get_app_id();
	wp_localize_script(
		$fastwc_block_editor_js,
		'fastwcHeadless',
		array(
			'appId' => $fastwc_app_id,
		)
	);

	// Enqueue the stylesheet.
	wp_enqueue_style(
		'fastwc-block-editor-css',
		FASTWC_URL . 'assets/dist/blocks/index.css',
		array(),
		FASTWC_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'fastwc_enqueue_block_editor_assets' );
