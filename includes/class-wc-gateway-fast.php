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
	 * Gateway class constructor.
	 */
	public function __construct() {
		$this->id = 'Fast';
		$this->has_fields = false;
		$this->title = 'Fast';
		$this->method_title = 'Fast';
		$this->method_description = sprintf(
			'%1$s <a href="%2$s">%3$s</a>.<br />%4$s',
			__( 'Payment through', 'fast' ),
			esc_url( admin_url( 'admin.php?page=fast' ) ),
			__( 'Fast Checkout', 'fast' ),
			__( 'Note: Fast Checkout will continue to function even if it is disabled as a payment gateway. This option is here for integrations that require an active payment gateway code.', 'fast' )
		);
	}
}
