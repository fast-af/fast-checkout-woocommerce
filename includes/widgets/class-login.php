<?php
/**
 * Fast login button widget.
 *
 * @package Fast
 */

namespace FastWC\Widgets;

/**
 * Fast login button widget class.
 */
class Login extends Widget {

	/**
	 * Widget template name.
	 *
	 * @var string
	 */
	protected $template = 'widgets/fast-login';

	/**
	 * Widget constructor.
	 */
	public function __construct() {
		parent::__construct(
			'fastwc_login',
			__( 'Fast Login Button', 'fast' ),
			array(
				'description' => __( 'Display the Fast Login button.', 'fast' ),
			)
		);
	}

	/**
	 * Function to determine if this widget should be hidden.
	 *
	 * @param array $instance Widget options for the current instance.
	 */
	protected function should_hide( $instance ) {
		return \fastwc_should_hide_login_button();
	}
}
