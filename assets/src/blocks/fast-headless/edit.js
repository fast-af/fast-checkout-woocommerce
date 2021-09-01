/**
 * Edit component for Fast Headless block.
 */

import './edit.scss';

import isEditorReady from '../components/editor-ready';
import FastWCProductSearch from '../components/product';
import FastWCProductVariant from '../components/product-variant';
import FastWCProductAttributes from '../components/product-attributes';

const { __ } = wp.i18n;
const { select, dispatch } = wp.data;
const { TextControl, ClipboardButton } = wp.components;
const { useState, useEffect } = wp.element;
const { addQueryArgs } = wp.url;

/**
 * Edit component for the Fast Headless block.
 *
 * @param {object} props The properties of the Fast Headless block.
 *
 * @returns {object} The edit component output.
 */
const edit = ( props ) => {
	const {
		attributes,
		setAttributes,
	} = props;

	const {
		productId,
		variantId,
		quantity,
		productOptions,
	} = attributes;

	// Meta keys for storing data.
	const metaKeys = {
		productId: 'fastwc_product_id',
		variantId: 'fastwc_variant_id',
		quantity: 'fastwc_quantity',
		productOptions: 'fastwc_product_options',
	};

	// Default values for data.
	const defaultValues = {
		productId: 0,
		variantId: 0,
		quantity: 1,
		productOptions: {},
	};

	const [hasCopiedPermalink, setHasCopiedPermalink] = useState( false );
	const [hasCopiedFastLink, setHasCopiedFastLink] = useState( false );
	const [fastLink, setFastLink] = useState( '' );

	const setMeta = ( metaKey, value ) => {
		const meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		const newMeta = { ...meta };
		newMeta[ metaKey ] = value;

		dispatch( 'core/editor' ).editPost( {
			meta: newMeta,
		} );
	};

	const getMeta = ( metaKey, defaultValue ) => {
		const meta = select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		if ( meta && meta[ metaKey ] ) {
			return meta[ metaKey ];
		}

		setMeta( metaKey, defaultValue );

		return defaultValue;
	};

	const permalink = select( 'core/editor' ).getPermalink();

	const generateFastLink = ( newAttributes = {} ) => {
		const linkAttributes = { ...attributes, ...newAttributes };


		const fastwcHeadless = window.fastwcHeadless || {};
		const appId = fastwcHeadless.hasOwnProperty( 'appId' ) ? wcSettings.appId : '';

		let newFastLink = '';

		if ( appId && productId ) {
			const baseUrl = 'https://go.fast.co/';
			const queryArgs = {
				app_id: appId,
				product_id: linkAttributes.productId,
				quantity: linkAttributes.quantity,
			};

			if ( variantId ) {
				queryArgs.variant_id = variantId;
			}

			newFastLink = addQueryArgs(
				baseUrl,
				{}
			);
		}

		setFastLink( newFastLink );
	};

	return (
		<div className="fastwc-headless-link-generator">
			<h2 className="fastwc-headless-link-generator--header">{ __( 'Fast Headless Checkout Link Generator' ) }</h2>
			<p className="fastwc-headless-link-generator--description">{ __( 'Generate a headless checkout link by selecting a product and setting a quantity below. Optionally, you can select a product variation and product options if available.' ) }</p>
			<FastWCProductSearch
				onChange={ ( value ) => {
					const int = parseInt( value, 10 );
					const newProductId = isNaN( int ) ? defaultValues.productId : int;

					setMeta( metaKeys.productId, newProductId );
					setAttributes( { productId: newProductId } );
				} }
				selected={ productId }
			/>
			<FastWCProductVariant
				onChange={ ( value ) => {
					const int = parseInt( value, 10 );
					const newVariantId = isNaN( int ) ? defaultValues.variantId : int;

					setMeta( metaKeys.variantId, newVariantId );
					setAttributes( { variantId: newVariantId } );
				} }
				product={ productId }
				variant={ variantId }
			/>
			<TextControl
				label={ __( 'Quantity' ) }
				type="number"
				onChange={ ( value ) => {
					const int = parseInt( value, 10 );
					const newQuantity = isNaN( int ) ? defaultValues.quantity : int;

					setMeta( metaKeys.quantity, newQuantity );
					setAttributes( { quantity: newQuantity } );
				} }
				value={ Number.isInteger( quantity ) ? quantity.toString( 10 ) : defaultValues.quantity.toString( 10 ) }
				step="1"
			/>
			<FastWCProductAttributes
				onChange={ ( atts ) => {
					setMeta( metaKeys.productOptions, JSON.stringify( atts ) );
					setAttributes( { productOptions: atts } );
				} }
				product={ productId }
				variant={ variantId }
				selected={ productOptions }
			/>
			{ productId && (
				<div className="fastwc-headless-link-generator--link-urls">
					<h3>{ __( 'Headless Checkout Link URL\'s') }</h3>
					<div className="fastwc-headless-link-generator--description">
						<strong>{ __( 'WordPress Permalink' ) }</strong>
					</div>
					<ClipboardButton
						className="button-link fastwc-headless-link-generator--button"
						text={ permalink }
						onCopy={ () => setHasCopiedPermalink( true ) }
						onFinishCopy={ () => setHasCopiedPermalink( false ) }
					>
						{ `${ permalink } - ${ hasCopiedPermalink ? __( 'Copied!' ) : __( 'Click to Copy' ) }` }
					</ClipboardButton>
					{ fastLink && (
						<>
							<div className="fastwc-headless-link-generator--description">
								<strong>{ __( 'Fast Headless Link' ) }</strong>
							</div>
							<ClipboardButton
								className="button-link fastwc-headless-link-generator--button"
								text={ fastLink }
								onCopy={ () => setHasCopiedFastLink( true ) }
								onFinishCopy={ () => setHasCopiedFastLink( false ) }
							>
								{ `${ fastLink } - ${ hasCopiedFastLink ? __( 'Copied!' ) : __( 'Click to Copy' ) }` }
							</ClipboardButton>
						</>
					) }
				</div>
			) }
		</div>
	);
};

export default edit;
