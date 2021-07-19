<?php
/**
 * Product Attributes API
 *
 * Provides an API that exposes product attributes.
 *
 * @package Fast
 */

/**
 * Utility to get information on installed plugins.
 *
 * @param array $data Options for the function.
 *
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_get_product_attributes( $data ) {
	error_log( print_r( $data, true ) );

	if ( empty( $data['id'] ) ) {
		return WP_Error( 'no_product_id', __( 'No Product ID', 'fast' ), array( 'status' => 500 ) );
	}

	$product = wc_get_product( $data['id'] );

	if ( emtpy( $product ) ) {
		return WP_Error( 'invalid_product_id', __( 'Invalid Product ID', 'fast' ), array( 'status' => 500 ) );
	}

	return new WP_REST_Response( $product->get_attributes(), 200 );
}
