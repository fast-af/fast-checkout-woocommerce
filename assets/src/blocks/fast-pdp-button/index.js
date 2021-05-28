/**
 * Fast Checkout PDP Button
 */

import icons from '../components/icons.js';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Placeholder, ServerSideRender } = wp.components;

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
		context: {
			type: 'string',
			default: 'editor',
		},
	},

	edit: ( props ) => {
		return (
			<div>
				<Placeholder>
					{ __( 'Fast Checkout Product Button' ) }
				</Placeholder>
			</div>
		);
	},

	save: () => {
		return null;
	},

} );
