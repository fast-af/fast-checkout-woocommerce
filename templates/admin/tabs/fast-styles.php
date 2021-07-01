<?php
/**
 * Fast settings Styles tab template.
 *
 * @package Fast
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'fast_styles' );
	do_settings_sections( 'fast_styles' );
	submit_button();
	?>
</form>
