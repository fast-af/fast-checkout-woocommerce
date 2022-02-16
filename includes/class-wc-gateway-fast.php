<?php
/**
 * Class WC_Gateway_Fast file.
 *
 * @package Fast
 */

namespace FastWC;

/**
 * Fast Checkout payment gateway.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_Fast extends \WC_Payment_Gateway {

	/**
	 * Fast disclaimer text.
	 *
	 * @var string
	 */
	protected $fast_disclaimer = '';

	/**
	 * Gateway class constructor.
	 */
	public function __construct() {
		$this->fast_disclaimer = __( 'Note: Fast Checkout will continue to function even if it is disabled as a payment gateway. This option is here for integrations that require an active payment gateway code.', 'fast' );

		$this->id                 = 'Fast';
		$this->icon               = '';
		$this->has_fields         = true;
		$this->method_title       = 'Fast';
		$this->method_description = sprintf(
			'%1$s <a href="%2$s">%3$s</a>.<br />%4$s',
			__( 'Payment through', 'fast' ),
			esc_url( admin_url( 'admin.php?page=fast' ) ),
			__( 'Fast Checkout', 'fast' ),
			esc_html( $this->fast_disclaimer )
		);

		// Initialie the backend options fields.
		$this->init_form_fields();
		$this->init_settings();

		// Load the settings.
		$this->enabled     = $this->get_option( 'enabled' );

		// Action hook to saves the settings
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array( $this, 'process_admin_options' )
		);

		// Action hook to load custom JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
	}

	/**
	 * Initialize the form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'fast' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable Fast', 'fast' ),
				'description' => $this->fast_disclaimer,
				'default'     => 'no'
			),
		);
	}

	/**
	 * Initialize the payment fields.
	 */
	public function payment_fields() {
		?>
		<div class="fast-checkout-wrapper">
			<?php fastwc_load_template( 'buttons/fast-checkout-cart-button' ); ?>
		</div>
		<?php
	}

	/**
	 * Enqueue scripts.
	 */
	public function payment_scripts() {
		// Only load the script on the checkout page.
		if ( ! is_checkout() ) {
			return;
		}

		wp_enqueue_script(
			'fastwc-frontend-js',
			FASTWC_URL . 'assets/dist/frontend/scripts.min.js',
			array( 'jquery' ),
			FASTWC_VERSION,
			true
		);
	}
}
