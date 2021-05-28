<?php
/**
 * Fast widget base template.
 *
 * @package Fast
 */

// Make sure a widget template is passed in. Otherwise, do nothing.
if ( empty( $args['template'] ) ) {
	return;
}

$before_widget = ! empty( $args['args']['before_widget'] ) ? $args['args']['before_widget'] : '';
$after_widget  = ! empty( $args['args']['after_widget'] ) ? $args['args']['after_widget'] : '';
$before_title  = ! empty( $args['args']['before_title'] ) ? $args['args']['before_title'] : '';
$after_title   = ! empty( $args['args']['after_title'] ) ? $args['args']['after_title'] : '';
$widget_title  = ! empty( $args['instance']['title'] ) ? $args['instance']['title'] : '';

echo wp_kses_post( $before_widget );
echo wp_kses_post( $before_title ) . esc_html( $widget_title ) . wp_kses_post( $after_title );
fastwc_load_template( $args['template'], $args );
echo wp_kses_post( $after_widget );
