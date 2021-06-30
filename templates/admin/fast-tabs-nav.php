<?php
/**
 * Fast admin settings page nav template.
 *
 * @package Fast
 */

$fastwc_tabs       = fastwc_get_settings_tabs();
$fastwc_active_tab = fastwc_get_active_tab();

?>

<nav class="nav-tab-wrapper">
	<?php
	foreach ( $fastwc_tabs as $tab_name => $tab_label ) :
		$tab_url   = sprintf( 'admin.php?page=fast&tab=%s', $tab_name );
		$tab_class = array( 'nav-tab' );
		if ( $fastwc_active_tab === $tab_name ) {
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
		if ( 'fast_advanced' === $fastwc_active_tab ) {
			$tab_class[] = 'nav-tab-active';
		}
		$tab_class = implode( ' ', $tab_class );
		$tab_label = __( 'Advanced', 'fast' );
		?>
	<a href="<?php echo esc_url( $tab_url ); ?>" class="<?php echo esc_attr( $tab_class ); ?>"><?php echo esc_html( $tab_label ); ?></a>
	<?php endif; ?>
</nav>
