/**
 * Fast Checkout PDP Button.
 */

import icons from '../components/icons.js';
import FastButton from '../components/button.js';

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
		}
	},

	edit: ( props ) => {
		const {
			attributes,
			setAttributes,
		} = props;
		const { product_id } = attributes;

		return (
			<>
				<InspectorControls key="fast-pdp-inspector-controls">
					<Panel>
						<PanelBody title={ __( 'Product ID' )}>
							<PanelRow>
								<TextControl
									label={ __( 'Product ID' ) }
									type="number"
									onChange={ (value ) => {
										const int = parseInt( value, 10 );

										setAttributes( {
											product_id: isNaN( int ) ? undefined : int,
										} );
									} }
									value={ Number.isInteger( product_id ) ? product_id.toString( 10 ) : '0' }
									step="1"
								/>
							</PanelRow>
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
