/**
 * Fast Checkout Headless Link Generator.
 */

import icons from '../components/icons';
import isEditorReady from '../components/editor-ready';
import edit from './edit';

const { __ } = wp.i18n;
const {
	registerBlockType,
	unregisterBlockType,
} = wp.blocks;

const blockTypeName = 'fastwc/fast-headless';

// =========================================
// Register the block with the block editor.
// =========================================

registerBlockType( blockTypeName, {

	title: __( 'Fast Headless Checkout Link', 'fast' ),

	icon: icons.fast,

	keywords: [
		__( 'fast' ),
		__( 'checkout' ),
		__( 'headless' ),
	],

	attributes: {
		productId: {
			type: 'integer',
			default: 0,
		},
		variantId: {
			type: 'integer',
			default: 0,
		},
		quantity: {
			type: 'integer',
			default: 1,
		},
		productOptions: {
			type: 'object',
			default: {},
		},
	},

	edit,

	save: () => {
		return null;
	},

} );

// ================================================================
// Unregister the block for all but fastwc_headless_link post type.
// ================================================================

const checkPostType = async () => {
	await isEditorReady();

	const currentPostType = wp.data.select( 'core/editor' ).getCurrentPostType(); 
	
	if ( 'fastwc_headless_link' !== currentPostType ) {
		unregisterBlockType( blockTypeName );
	}
};

checkPostType();
