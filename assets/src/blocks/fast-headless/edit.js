/**
 * Edit component for Fast Headless block.
 */

import './edit.scss';

import isEditorReady from '../components/editor-ready';
import FastWCProductSearch from '../components/product';
import FastWCProductVariant from '../components/product-variant';
import FastWCProductAttributes from '../components/product-attributes';
import CopyButton from './copy-button';

const { __ } = wp.i18n;
const {
	select,
	dispatch,
	subscribe,
} = wp.data;
const {
	TextControl,
	TextareaControl,
} = wp.components;
const { useState, useEffect } = wp.element;
const { addQueryArgs } = wp.url;

const getPostStatus = () => select( 'core/editor' ).getEditedPostAttribute( 'status' );
const getPostMeta = () => select( 'core/editor' ).getEditedPostAttribute( 'meta' );
const getPermalink = () => select( 'core/editor' ).getPermalink();

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

	const postStatus = getPostStatus();
	const initialPermalink = getPermalink();

	const [hasCopiedPermalink, setHasCopiedPermalink] = useState( false );
	const [permalink, setPermalink] = useState( initialPermalink );
	const [hasCopiedButton, setHasCopiedButton] = useState( false );
	const [isPublished, setIsPublished] = useState( 'publish' === postStatus );

	subscribe( () => {

		const newPostStatus = getPostStatus();
		setIsPublished( 'publish' === newPostStatus );

		const newPermalink = getPermalink();
		setPermalink( newPermalink );

	} );

	const setMeta = ( metaKey, value ) => {
		const meta = getPostMeta();

		const newMeta = { ...meta };
		newMeta[ metaKey ] = value;

		dispatch( 'core/editor' ).editPost( {
			meta: newMeta,
		} );
	};

	const getMeta = ( metaKey, defaultValue ) => {
		const meta = getPostMeta();

		if ( meta && meta[ metaKey ] ) {
			return meta[ metaKey ];
		}

		setMeta( metaKey, defaultValue );

		return defaultValue;
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
			{ productId ? (
				<div className="fastwc-headless-link-generator--link-urls">
					{ isPublished ? (
						<>
							<h3>{ __( 'Headless Checkout Link URL' ) }</h3>
							<div className="fastwc-headless-link-generator--description">
								<div>
									<strong>{ __( 'WordPress Permalink' ) }</strong>
								</div>
								<TextControl
									disabled
									value={ permalink }
								/>
								<CopyButton
									copyText={ permalink }
									onSuccess={ () => setHasCopiedPermalink( true ) }
								>
									{ hasCopiedPermalink ? __( 'Copied!' ) : __( 'Click to Copy' ) }
								</CopyButton>
							</div>
							<hr />
						</>
					) : '' }
					<h3>{ __( 'Headless Checkout Button' ) }</h3>
					<div className="fastwc-headless-link-generator--description">
						<TextareaControl
							disabled
							value="This is the button string."
						/>
						<CopyButton
							copyText="This is the button string."
							onSuccess={ () => setHasCopiedButton( true ) }
						>
							{ hasCopiedButton ? __( 'Copied!' ) : __( 'Click to Copy' ) }
						</CopyButton>
					</div>
				</div>
			) : '' }
		</div>
	);
};

export default edit;
