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
 * @param WP_REST_Request $request JSON request for shipping endpoint.
 *
 * @return array|WP_Error|WP_REST_Response
 */
function fastwc_get_product_attributes( WP_REST_Request $request ) {
	$product_id = $request->get_param( 'productId' );
	$variant_id = $request->get_param( 'variantId' );
	$return     = array();

	if ( ! empty( $product_id ) ) {
		$product = wc_get_product( $product_id );
	}

	if ( ! empty( $product ) ) {
		$return = fastwc_get_product_variant_attributes( $product, $variant_id );
	}

	return new WP_REST_Response( $return, 200 );
}

/**
 * Get the list of available attribute names for variants.
 *
 * @param WC_Product $product    The product.
 * @param int        $variant_id The variant.
 *
 * @return array
 */
function fastwc_get_product_variant_attributes( $product, $variant_id ) {
	$return = array();

	if ( $product->is_type( 'variable' ) && ! empty( $variant_id ) ) {
		
		$variations = $product->get_available_variations();
		if ( ! empty( $variations ) ) {

			// Get the variation attributes.
			foreach ( $variations as $variation ) {
				if ( (int) $variant_id === (int) $variation['variation_id'] ) {
					$return['attKeys']   = array_keys( $variation['attributes'] );
					$return['values'] = $variation['attributes'];
					break;
				}
			}

			// Get attribute labels.
			$attribue_labels = array();
			$attributes      = $product->get_attributes();
			foreach ( $attributes as $key => $attribute ) {
				$att_key                      = fastwc_standardize_attribute_key( $key );
				$attribute_labels[ $att_key ] = wc_attribute_label( $attribute->get_name(), $product );
			}
			$return['labels'] = $attribute_labels;

			$variation_attributes = $product->get_variation_attributes();
			$attribute_options    = array();
			foreach ( $variation_attributes as $key => $options ) {
				$att_key                       = fastwc_standardize_attribute_key( $key );
				$attribute_options[ $att_key ] = array();
				foreach ( $options as $option ) {
					$attribute_options[ $att_key ][] = array(
						'label' => $option,
						'value' => $option,
					);
				}
			}
			$return['options'] = $attribute_options;
		}
	}

	return $return;
}

/**
 * Standardize the attribute key.
 *
 * @param string $att_key Non-normalized key.
 *
 * @return array
 */
function fastwc_standardize_attribute_key( $att_key ) {
	return 'attribute_' . sanitize_title( $att_key );
}
