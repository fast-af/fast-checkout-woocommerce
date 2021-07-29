/**
 * Fast Checkout Cart Button.
 */

import icons from '../components/icons';
import FastButton from '../components/button';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

// =========================================
// Register the block with the block editor.
// =========================================

registerBlockType( 'fastwc/fast-cart-button', {

    title: __( 'Fast Checkout Cart Button', 'fast' ),

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
