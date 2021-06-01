/**
 * Fast Login Button.
 */

import icons from '../components/icons.js';
import FastButton from '../components/button.js';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

// =========================================
// Register the block with the block editor.
// =========================================

registerBlockType( 'fastwc/fast-login-button', {

    title: __( 'Fast Login Button', 'fast' ),

    icon: icons.fast,

    keywords: [
        __( 'fast' ),
        __( 'login' ),
        __( 'button' ),
    ],

    attributes: {},

    edit: ( props ) => {
        return (
            <FastButton type="login" />
        );
    },

    save: () => {
        return null;
    },

} );
