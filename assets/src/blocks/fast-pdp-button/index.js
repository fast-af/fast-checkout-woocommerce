/**
 * Fast Checkout PDP Button.
 */

import icons from '../components/icons.js';
import FastButton from '../components/button.js';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

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

	attributes: {},

	edit: ( props ) => {
		return (
			<FastButton type="checkout" />
		);
	},

	save: () => {
		return null;
	},

} );
