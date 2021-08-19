<?php
/**
 * Fast checkout button template.
 *
 * @package Fast
 */

$fastwc_app_id          = fastwc_get_app_id();
$currency               = get_woocommerce_currency();
$multicurrency_disabled = fastwc_is_multicurrency_support_disabled();
$product_id             = ! empty( $args['product_id'] ) ? absint( $args['product_id'] ) : 0;
$variant_id             = ! empty( $args['variant_id'] ) ? absint( $args['variant_id'] ) : 0;
$quantity               = ! empty( $args['quantity'] ) ? absint( $args['quantity'] ) : 0;
$fastwc_app_id   = fastwc_get_app_id();
$product_id      = ! empty( $args['product_id'] ) ? absint( $args['product_id'] ) : 0;
$variant_id      = ! empty( $args['variant_id'] ) ? absint( $args['variant_id'] ) : 0;
$quantity        = ! empty( $args['quantity'] ) ? absint( $args['quantity'] ) : 0;
$product_options = ! empty( $args['product_options'] ) ? fastwc_get_normalized_product_options( $args['product_options'] ) : '';
?>
	<fast-checkout-button
		app_id="<?php echo esc_attr( $fastwc_app_id ); ?>"
		<?php if ( false === $multicurrency_disabled ) : ?>
		currency="<?php echo esc_attr( $currency ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $product_id ) ) : ?>
		product_id="<?php echo esc_attr( $product_id ); ?>"
			<?php if ( ! empty( $variant_id ) ) : ?>
		variant_id="<?php echo esc_attr( $variant_id ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $quantity ) ) : ?>
		quantity="<?php echo esc_attr( $quantity ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $product_options ) ) : ?>
		product_options="<?php echo esc_attr( $product_options ); ?>"
			<?php endif; ?>
		<?php endif; ?>
	></fast-checkout-button>
