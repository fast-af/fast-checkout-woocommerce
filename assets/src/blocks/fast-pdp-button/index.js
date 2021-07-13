/**
 * Fast Checkout PDP Button.
 */

import icons from '../components/icons';
import FastButton from '../components/button';

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
		} = attributes;

		return (
			<>
				<InspectorControls key="fast-pdp-inspector-controls">
					<Panel>
						<PanelBody title={ __( 'Product Details' )}>
							<TextControl
								label={ __( 'Product ID' ) }
								type="number"
								onChange={ ( value ) => {
									const int = parseInt( value, 10 );

									setAttributes( {
										product_id: isNaN( int ) ? undefined : int,
									} );
								} }
								value={ Number.isInteger( product_id ) ? product_id.toString( 10 ) : '0' }
								step="1"
							/>
							<TextControl
								label={ __( 'Variation ID' ) }
								type="number"
								onChange={ ( value ) => {
									const int = parseInt( value, 10 );

									setAttributes( {
										variant_id: isNaN( int ) ? undefined : int,
									} );
								} }
								value={ Number.isInteger( variant_id ) ? variant_id.toString( 10 ) : '0' }
								step="1"
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
