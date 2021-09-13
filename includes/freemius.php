<?php
/**
 * Freemius analytics integration.
 *
 * @package Fast
 */

if ( ! function_exists( 'fastwc_freemius' ) ) {
	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return Freemius
	 */
	function fastwc_freemius() {
		global $fastwc_freemius;

		if ( ! isset( $fastwc_freemius ) ) {
			// Include Freemius SDK.
			require_once FASTWC_PATH . 'freemius/start.php';

			$fastwc_freemius = fs_dynamic_init( array(
				'id'                  => '9006',
				'slug'                => 'fast-checkout-for-woocommerce',
				'type'                => 'plugin',
				'public_key'          => 'pk_0b10aa6582d1b077705c203552010',
				'is_premium'          => false,
				'has_addons'          => false,
				'has_paid_plans'      => false,
				'menu'                => array(
					'slug'           => 'fast',
					'account'        => false,
					'contact'        => false,
					'support'        => false,
				),
			) );
		}

		return $fastwc_freemius;
	}

	// Init Freemius.
	fastwc_freemius();
	// Signal that SDK was initiated.
	do_action( 'fastwc_freemius_loaded' );
}
