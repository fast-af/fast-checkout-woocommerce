<?php
/**
 * Fast settings Support tab template.
 *
 * @package Fast
 */

$fastwc_setting_fast_onboarding_url = fastwc_get_option_or_set_default( FASTWC_SETTING_ONBOARDING_URL, FASTWC_ONBOARDING_URL );

$image_urls = array(
	'onboarding' => FASTWC_URL . '/assets/img/get-started-as-seller.jpg',
	'app-info'   => FASTWC_URL . '/assets/img/app-info-tab.jpg',
	'styles'     => FASTWC_URL . '/assets/img/styles-tab.jpg',
	'options'    => FASTWC_URL . '/assets/img/options-tab.jpg',
	'test'       => FASTWC_URL . '/assets/img/test-mode-tab.jpg',
);

?>

<ul class="subsubsub">
	<li><a href="#become-seller">Become a Seller</a> | </li>
	<li><a href="#add-app-id">Add App ID</a> | </li>
	<li><a href="#edit-appearance">Edit Appearance</a> | </li>
	<li><a href="#button-options">Button Options</a> | </li>
	<li><a href="#test-debug">Test and Debug</a> | </li>
	<li><a href="https://help.fast.co/hc/en-us/requests/new" target="_blank" rel="noopener">Submit Ticket</a></li>
</ul>

<div class="fast-support-docs">

	<h2 id="become-seller">Become a Seller</h2>

	<p>The first step to integrating Fast Checkout with your WooCommerce store is to become a seller with Fast.</p>

	<p><a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener">Become a Seller on Fast.co →</a></p>

	<p>
		<a href="<?php echo esc_url( $fastwc_setting_fast_onboarding_url ); ?>" target="_blank" rel="noopener">
			<img src="<?php echo esc_url( $image_urls['onboarding'] ); ?>" alt="Get started as a a seller" />
		</a>
	</p>

	<hr />

	<h2 id="add-app-id">Add your App ID</h2>

	<p>Copy your App ID from your seller dashboard and enter it into the <a href="<?php echo esc_url( admin_url( 'admin.php?page=fast&tab=fast_app_info' ) ); ?>">App ID field</a> in the Fast plugin settings. Click Save Changes at the bottom.</p>

	<p>
		<img src="<?php echo esc_url( $image_urls['app-info'] ); ?>" alt="App Info Tab" />
	</p>

	<hr />

	<h2 id="edit-appearance">Edit the appearance of the button</h2>

	<p>You can edit the default CSS of the Fast button in <a href="<?php echo esc_url( admin_url( 'admin.php?page=fast&tab=fast_styles' ) ); ?>">Styles tab</a>.</p>

	<p>
		<img src="<?php echo esc_url( $image_urls['styles'] ); ?>" alt="Styles Tab" />
	</p>

	<hr />

	<h2 id="button-options">Button Options</h2>

	<p>You can set additional options to customize the functionality of your Fast buttons in the <a href="<?php echo esc_url( admin_url( 'admin.php?page=fast&tab=fast_options' ) ); ?>">Options tab</a>.</p>

	<p>
		<img src="<?php echo esc_url( $image_urls['options'] ); ?>" alt="Options Tab" />
	</p>

	<h3>Select Product Button Location</h3>

	<p>The Fast Checkout button on the product details page can be displayed in 1 of 3 pre-set areas within the product's Add to Cart form:</p>

	<ul class="ul-disc">
		<li><strong>Before Quantity Selection:</strong> This option places the button just above the quantity selection field in the Add to Cart form.</li>
		<li><strong>After Quantity Selection:</strong> This option places the button between the quantity selection field and the Add to Cart button.</li>
		<li><strong>After Add to Cart Button:</strong> This option places the button ust below the Add to Cart button.</li>
	</ul>

	<p>A fourth option, Other, is available for users with advanced understanding of <a href="https://developer.wordpress.org/plugins/hooks/" target="_blank" rel="noopener">WordPress hooks</a>. If you select Other, you must enter a valid WordPress action hook in the following field, <strong>Enter Alternate Product Button Location</strong>.</p>

	<h3>Hide Buttons for these Products</h3>

	<p>This option provides a way to select products for which the Fast Checkout button will not be displayed. To select a product, begin typing the name of the product in the field and then select the product from the list that appears. This will hide the Fast Checkout button from the selected product pages as well as the cart and checkout pages if a selected product is added to the cart.</p>

	<h3>Display Login in Footer</h3>

	<p>The Fast Login button displays in the footer by default for non-logged in users. Uncheck this option to prevent the Fast Login button from displaying in the footer.</p>

	<hr />

	<h2 id="test-debug">Test and Debug</h2>

	<p>The <a href="<?php echo esc_url( admin_url( 'admin.php?page=fast&tab=fast_test_mode' ) ); ?>">Test Mode tab</a> provides options to enable testing and debugging of your Fast installation.</p>

	<p>
		<img src="<?php echo esc_url( $image_urls['test'] ); ?>" alt="Test Mode Tab" />
	</p>

	<h3>Test Mode</h3>

	<p>Select the Test mode option so that only logged-in admin users can see the Fast Checkout button.</p>

	<h3>Debug Mode</h3>

	<p>Select the Debug Mode option to enable logging in the Fast plugin. The Fast plugin will log info, debug, and error messages to the <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-status&tab=logs' ) ); ?>">WooCommerce logs</a> while Debug Mode is enabled.</p>

	<div class="fast-notice fast-notice-warning">
		<p>Debug Mode logs a lot of information, so it should only be used for debugging issues and only for short durations.</p>
	</div>

	<h3>Disable Multicurrency Support</h3>

	<p>This option can be used to disable multicurrency support. This is only necessary if the store uses a third-party multicurrency plugin to handle multicurrency in WooCommerce, but multicurrency support is not needed in the Fast Checkout process.</p>

</div>
