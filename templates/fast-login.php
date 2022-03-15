<?php
/**
 * Fast Login template.
 *
 * @package Fast
 */

$fastwc_app_id        = FastWC\Config::get_app_id();
$nonce                = wp_create_nonce( 'fast-backend-login-auth' );
$fastwc_use_dark_mode = fastwc_use_dark_mode();
?>

		<div class="fast-login-wrapper">
			<fast-login id="fastloginbutton"
				app_id="<?php echo esc_attr( $fastwc_app_id ); ?>"
				data-nonce="<?php echo esc_attr( $nonce ); ?>"
				<?php if ( $fastwc_use_dark_mode ) : ?>
				dark
				<?php endif; ?>
			></fast-login>
		</div>
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
