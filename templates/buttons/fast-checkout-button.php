<?php
/**
 * Fast checkout button template.
 *
 * @package Fast
 */

$fastwc_app_id = fastwc_get_app_id();
$product_id    = ! empty( $args['product_id'] ) ? absint( $args['product_id'] ) : 0;
?>
	<fast-checkout-button
		app_id="<?php echo esc_attr( $fastwc_app_id ); ?>"
		<?php if ( ! empty( $product_id ) ) : ?>
		product_id="<?php echo esc_attr( $product_id ); ?>"
		<?php endif; ?>
	></fast-checkout-button>
