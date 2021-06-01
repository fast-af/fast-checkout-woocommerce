<?php
/**
 * Fast Login button block.
 *
 * @package Fast
 */

namespace FastWC\Blocks;

/**
 * Fast Login button block class.
 */
class Login extends Block {

	/**
	 * The name of the block.
	 *
	 * @var string
	 */
	protected $name = 'fast-login-button';

	/**
	 * The name of the block template to render.
	 *
	 * @var string
	 */
	protected $template = 'fast-login';

	/**
	 * Check to see if the button should be hidden.
	 *
	 * @return bool
	 */
	protected function should_hide() {
		return fastwc_should_hide_login_button();
	}
}
