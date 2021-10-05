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
	 * Construct the \FastWC\Freemius object.
	 */
	protected function __construct() {
		if ( ! isset( $this->freemius ) ) {
			// Include Freemius SDK.
			require_once FASTWC_PATH . 'freemius/start.php';

			$this->freemius = \fs_dynamic_init(
				array(
					'id'             => $this->id,
					'slug'           => $this->slug,
					'type'           => 'plugin',
					'public_key'     => $this->public_key,
					'is_premium'     => false,
					'has_addons'     => false,
					'has_paid_plans' => false,
					'menu'           => array(
						'slug'       => $this->menu_slug,
						'first-path' => $this->first_path,
						'account'    => false,
						'contact'    => false,
						'support'    => false,
					),
				)
			);

			// Add filters to update the opt-in message.
			$this->freemius->add_filter( 'connect_message', array( $this, 'connect_message' ), 10, 6 );
			$this->freemius->add_filter( 'connect_message_on_update', array( $this, 'connect_message' ), 10, 6 );
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

		/* translators: %s: name (e.g. Hey John,) */
		$hey_x_text = \esc_html(
			sprintf(
				\fs_text_x_inline( 'Hey %s,', 'greeting', 'hey-x', 'fast' ),
				$user_first_name
			)
		);

		// Set the default message string.
		$default_message = \fs_text_inline( 'Never miss an important update - opt in to our security & feature updates along side diagnostic tracking with %4$s.', 'connect-message', 'fast' );
		if ( $this->freemius->is_plugin_update() ) {
			$default_message = \fs_text_inline( 'Never miss an important update - opt in to our security & feature updates along side diagnostic tracking with %4$s. If you skip this, that\'s okay! %1$s will still work just fine.', 'connect-message_on-update', 'fast' );
		}

		$message = $hey_x_text . '<br>' . sprintf(
			esc_html( $default_message ),
			'<b>' . esc_html( $product_title ) . '</b>',
			'<b>' . $user_login . '</b>',
			'<a href="' . $site_link . '" target="_blank" rel="noopener noreferrer">' . $site_link . '</a>',
			$freemius_link
		);

		return $message;
	}
}

// Init Freemius.
Freemius::get_instance();
// Signal that SDK was initiated.
\do_action( 'fastwc_freemius_loaded' );
