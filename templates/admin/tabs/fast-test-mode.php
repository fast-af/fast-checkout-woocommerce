<?php
/**
 * Fast settings Test Mode tab template.
 *
 * @package Fast
 */

?>
<form method="post" action="options.php">
	<?php
	settings_fields( 'fast_test_mode' );
	do_settings_sections( 'fast_test_mode' );
	submit_button();
	?>
</form>
