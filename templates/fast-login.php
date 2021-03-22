<?php
/**
 * Fast Login template.
 *
 * @package Fast
 */

$fast_app_id              = fast_get_app_id();
$fast_login_button_styles = fast_get_option_or_set_default( FAST_SETTING_LOGIN_BUTTON_STYLES, FAST_SETTING_LOGIN_BUTTON_STYLES_DEFAULT );
$nonce                    = wp_create_nonce( 'fast-backend-login-auth' );
?>

		<div class="fast-login-wrapper">
			<fast-login id="fastloginbutton" app_id="<?php echo esc_attr( $fast_app_id ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>"></fast-login>
		</div>
		<style>
			<?php echo esc_html( $fast_login_button_styles ); ?>
		</style>
		<script type="text/javascript">
			document
			.querySelector("#fastloginbutton")
			.addEventListener("complete", (event) => {
				const authToken = "auth=" + event.detail.token;
				const nonce = "&_wpnonce=" + document.querySelector("#fastloginbutton").dataset.nonce;
				if (window.location.search === "") {
					window.location.search = "?" + authToken + nonce;
				} else {
					window.location.search += "&" + authToken + nonce;
				}
			})
		</script>
