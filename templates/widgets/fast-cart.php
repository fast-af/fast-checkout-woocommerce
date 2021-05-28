<?php
/**
 * Fast Checkout cart button widget template.
 *
 * @package Fast
 */

$description = ! empty( $args['instance']['description'] ) ? $args['instance']['description'] : '';

if ( ! empty( $description ) ) :
	?>
<div class="fast-checkout-button-widget-description fast-cart-checkout-button-widget-description">
	<?php echo wp_kses_post( wpautop( $description ) ); ?>
</div>
	<?php
endif;

fastwc_load_template( 'buttons/fast-checkout-cart-button' );
