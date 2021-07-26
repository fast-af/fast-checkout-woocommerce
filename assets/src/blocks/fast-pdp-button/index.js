/**
 * Fast Checkout PDP Button.
 */

import icons from '../components/icons';
import FastButton from '../components/button';
import FastWCProductSearch from '../components/product';
import FastWCProductVariant from '../components/product-variant';
import FastWCProductAttributes from '../components/product-attributes';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const {
	Panel,
	PanelBody,
	PanelRow,
	TextControl,
} = wp.components;

// =========================================
// Register the block with the block editor.
// =========================================

registerBlockType( 'fastwc/fast-pdp-button', {

	title: __( 'Fast Checkout Product Button', 'fast' ),

	icon: icons.fast,

	keywords: [
		__( 'fast' ),
		__( 'checkout' ),
		__( 'button' ),
	],

	attributes: {
		product_id: {
			type: 'integer',
			default: 0,
		},
		variant_id: {
			type: 'integer',
			default: 0,
		},
		quantity: {
			type: 'integer',
			default: 1,
		},
		product_attributes: {
			type: 'object',
			default: {},
		},
	},

	edit: ( props ) => {
		const {
			attributes,
			setAttributes,
		} = props;
		const {
			product_id,
			variant_id,
			quantity,
			product_attributes,
		} = attributes;

		return (
			<>
				<InspectorControls key="fast-pdp-inspector-controls">
					<Panel>
						<PanelBody title={ __( 'Product Details' )}>
							<FastWCProductSearch
								onChange={ ( value ) => {
									const int = parseInt( value, 10 );

									setAttributes( {
										product_id: isNaN( int ) ? undefined : int,
									} );
								} }
								selected={ product_id }
							/>
							<FastWCProductVariant
								onChange={ ( value ) => {
									const int = parseInt( value, 10 );

									setAttributes( {
										variant_id: isNaN( int ) ? undefined : int,
									} );
								} }
								product={ product_id }
								variant={ variant_id }
							/>
							<TextControl
								label={ __( 'Quantity' ) }
								type="number"
								onChange={ ( value ) => {
									const int = parseInt( value, 10 );

									setAttributes( {
										quantity: isNaN( int ) ? undefined : int,
									} );
								} }
								value={ Number.isInteger( quantity ) ? quantity.toString( 10 ) : '1' }
								step="1"
							/>
							<FastWCProductAttributes
								onChange={ ( atts ) => {
									setAttributes( {
										product_attributes: atts,
									} );
								} }
								product={ product_id }
								variant={variant_id}
								selected={ product_attributes }
							/>
						</PanelBody>
					</Panel>
				</InspectorControls>
				<FastButton type="checkout" />
			</>
		);
	},

	save: () => {
		return null;
	},

} );
