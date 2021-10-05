<?php
/**
 * Freemius analytics integration.
 *
 * @package Fast
 */

namespace FastWC;

/**
 * Freemius analytics integration class.
 */
class Freemius {

	/**
	 * Instance of this object.
	 *
	 * @var \FastWC\Freemius
	 */
	protected static $instance = null;

	/**
	 * Freemius ID.
	 *
	 * @var string
	 */
	protected $id = '9006';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $slug = 'fast-checkout-for-woocommerce';

	/**
	 * Public key.
	 *
	 * @var string
	 */
	protected $public_key = 'pk_0b10aa6582d1b077705c203552010';

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	protected $menu_slug = 'fast';

	/**
	 * First path.
	 *
	 * @var string
	 */
	protected $first_path = 'admin.php?page=fast';

	/**
	 * Freemius object.
	 *
	 * @var \Freemius
	 */
	protected $freemius = null;

	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return Freemius
	 */
	protected function __construct() {
		if ( ! isset( $this->freemius ) ) {
			// Include Freemius SDK.
			require_once FASTWC_PATH . 'freemius/start.php';

			$this->freemius = \fs_dynamic_init(
				array(
					'id'                  => $this->id,
					'slug'                => $this->slug,
					'type'                => 'plugin',
					'public_key'          => $this->public_key,
					'is_premium'          => false,
					'has_addons'          => false,
					'has_paid_plans'      => false,
					'menu'                => array(
						'slug'           => $this->menu_slug,
						'first-path'     => $this->first_path,
						'account'        => false,
						'contact'        => false,
						'support'        => false,
					),
				)
			);

			// Add filters to update the opt-in message.
			$this->freemius->add_filter( 'connect_message', array( $this, 'connect_message' ), 10, 6 );
			$this->freemius->add_filter( 'connect_message_on_update', array( $this, 'connect_message' ), 10, 6 );

			// Add filter to customize the lsit of permissions.
			$this->freemius->add_filter( 'permission_list', array( $this, 'permissions' ) );
		}
	}

	/**
	 * Return an instance of the FastWC\Freemius object.
	 *
	 * @return \FastWC\Freemius
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Freemius();
		}

		return self::$instance;
	}

	/**
	 * Create a custom opt-in connect message.
	 *
	 * @param string $message         The original message.
	 * @param string $user_first_name The first name of the current user.
	 * @param string $product_title   The title of the product.
	 * @param string $user_login      The user's login.
	 * @param string $site_link       A link to the site.
	 * @param string $freemius_link   A link to Freemius.
	 *
	 * @return string
	 *
	 * @see https://freemius.com/help/documentation/wordpress-sdk/opt-in-message/
	 */
	public function connect_message( $message, $user_first_name, $product_title, $user_login, $site_link, $freemius_link ) {
		// TODO: Update the message.

		return $message;
	}

	/**
	 * Filter the permissions list.
	 *
	 * @param array $permissions The list of permissions to filter.
	 *
	 * @return array
	 *
	 * @see https://freemius.com/help/documentation/wordpress-sdk/opt-in-message/
	 */
	public function permissions( $persmissions ) {
		// TODO: Filter the permissions list.

		return $persmissions;
	}
}

// Init Freemius.
Freemius::get_instance();
// Signal that SDK was initiated.
\do_action( 'fastwc_freemius_loaded' );
