<?php
/**
 * Fast cart checkout template.
 *
 * @package Fast
 */

?>

		<div class="fast-cart-wrapper">
			<?php fastwc_load_template( 'buttons/fast-checkout-cart-button' ); ?>
			<div class="fast-cart-or"><?php esc_html_e( 'OR', 'fast' ); ?></div>
		</div>
