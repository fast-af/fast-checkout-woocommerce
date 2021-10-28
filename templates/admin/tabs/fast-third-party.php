<?php
/**
 * Fast settings Third-Party Plugins tab template.
 *
 * @package Fast
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'fast_third_party' );
	do_settings_sections( 'fast_third_party' );
	submit_button();
	?>
</form>
