<?php
/**
 * Fast settings Advanced tab template.
 *
 * @package Fast
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'fast_advanced' );
	do_settings_sections( 'fast_advanced' );
	submit_button();
	?>
</form>
