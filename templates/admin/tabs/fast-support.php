<?php
/**
 * Fast settings Support tab template.
 *
 * @package Fast
 */

$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

?>

<ul class="subsubsub">
	<li><a href="#become-seller">Become a Seller</a> | </li>
	<li><a href="#add-app-id">Item 2</a> | </li>
	<li><a href="#">Item 3</a> | </li>
	<li><a href="#">Item 4</a></li>
<ul>


<h2 id="become-seller">Become a Seller</h2>

<p>The first step to integrating Fast Checkout with your WooCommerce store is to become a seller with Fast.</p>

<p><a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener">Become a Seller on Fast.co →</a></p>

<p>
	<a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener">
		<img src="https://uc3be9bdf3dc637080b4511f2061.dl.dropboxusercontent.com/cd/0/inline/BQ24d94JJW7f9-PfcZFnB9-pRPlC01UiqBAz7Jqvydw0Fs0s0OxLJku9im3BDslpC_87XbGOGlY-q5e2mBvNVKubVuqOyixH3gziPGyzswoO9CYmA0OqNo8M6T8GvH6rMDglQAC6NTmnSOVnf473aPe9/file#" alt="Get started as a a seller" />
	</a>
</p>

<h2 id="add-app-id">Add your App ID</h2>

<p>Copy your App ID from your seller dashboard and enter it into the App ID field in the Fast plugin settings. Scroll down and click Save Changes at the bottom.</p>

