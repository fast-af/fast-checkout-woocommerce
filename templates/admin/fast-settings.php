<?php
/**
 * Fast admin settings page template.
 *
 * @package Fast
 */

$fastwc_tabs       = fastwc_get_settings_tabs();
$fastwc_active_tab = fastwc_get_active_tab();

?>
<div class="wrap fast-settings">
	<h2><?php esc_html_e( 'Fast Settings', 'fast' ); ?></h2>

	<?php
	// Load the tabs nav.
	fastwc_load_template( 'admin/fast-tabs-nav' );

	// Load the tab content for the active tab.
	$valid_tab_contents   = array_keys( $fastwc_tabs );
	$valid_tab_contents[] = 'fast_advanced';
	if ( ! in_array( $fastwc_active_tab, $valid_tab_contents, true ) ) {
		$fastwc_active_tab = 'fast_app_info';
	}
	$fastwc_tab_template = 'admin/tabs/' . str_replace( '_', '-', $fastwc_active_tab );
	fastwc_load_template( $fastwc_tab_template );

	// Load the Fast settings footer.
	fastwc_load_template( 'admin/fast-settings-footer' );
	?>
</div>
