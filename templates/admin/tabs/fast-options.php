<?php
/**
 * Fast settings Options tab template.
 *
 * @package Fast
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'fast_options' );
	do_settings_sections( 'fast_options' );
	submit_button();
	?>
</form>
