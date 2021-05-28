<?php
/**
 * Fast Checkout product button widget template.
 *
 * @package Fast
 */

$instance    = ! empty( $args['instance'] ) ? $args['instance'] : array();
$description = ! empty( $instance['description'] ) ? $instance['description'] : '';

if ( ! empty( $description ) ) :
	?>
<div class="fast-checkout-button-widget-description fast-pdp-checkout-button-widget-description">
	<?php echo wp_kses_post( wpautop( $description ) ); ?>
</div>
	<?php
endif;

fastwc_load_template( 'buttons/fast-checkout-button', $instance );
