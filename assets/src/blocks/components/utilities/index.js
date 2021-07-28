/* global wcSettings */
/**
 * Fast button block utilities.
 */

import { uniqBy } from 'lodash';

const apiFetch = wp.apiFetch;
const { addQueryArgs } = wp.url;

const productsBase = '/wc/store/products';

/**
 * Get the number of products in the WooCommerce store from the global wcSettings object.
 *
 * @returns {number}
 */
const getProductCount = () => {
	const wcSettings = window.wcSettings || {};
	const blocksConfig = wcSettings.hasOwnProperty( 'wcBlocksConfig' ) ? wcSettings.wcBlocksConfig : {};
	const productCount = blocksConfig.hasOwnProperty( 'productCount' ) ? blocksConfig.productCount : 100;

	return productCount;
};

/**
 * Get a promise that resolves to a list of products from the WooCommerce Store API.
 *
 * @param {Object} request          A query object with the list of selected products and search term.
 * @param {Array}  request.selected Currently selected products.
 * @param {string} request.search   Search string.
 *
 * @returns {Promise} Promise object resolves to a products formatted for use as options in a SelectControl component.
 */
export const getProducts = (
	{
		selected = 0,
		search = '',
	}
) => {
	const productCount = getProductCount();
	const isLargeCatalog = productCount > 100;

	const queryArgs = {
		per_page: isLargeCatalog ? 100 : 0,
		catalog_visibility: 'any',
		search,
		orderby: 'title',
		order: 'asc',
	};

	const requests = []

	// Add a query to make sure all selected products are included in the response.
	if ( selected ) {
		const include = [ selected ];
		requests.push(
			addQueryArgs(
				productsBase,
				{
					catalog_visibility: 'any',
					include,
				}
			)
		);
	}

	requests.push( addQueryArgs( productsBase, queryArgs ) );

	return Promise.all( requests.map( ( path ) => apiFetch( { path } ) ) )
		.then( ( data ) => {
			const products = uniqBy( data.flat(), 'id' );
			const list = products.map( ( product ) => ( {
				label: product.name,
				value: product.id,
			} ) );
			return list;
		} )
		.catch ( ( e ) => {
			throw e;
		} );
};

/**
 * Get a promise that resolves to a product object from the WooCommerce Store API.
 *
 * @param {number} productId Id of the product to retrieve.
 *
 * @returns {Promise} Promise object resolves to a WooCommerce product object.
 */
export const getProduct = ( productId ) => {
	return apiFetch( {
		path: `${ productsBase }/${ productId }`,
	} );
};

/**
 * Get the list of non-variation attributes from a product retrieved using the WooCommerce Store API.
 *
 * @param {number} productId Product ID.
 * @param {number} variantId Variant ID.
 *
 * @returns {Promise} Promise object resolves to a list of product attributes.
 */
export const getProductAttributes = async ( productId, variantId ) => {
	const path = addQueryArgs(
		'/wc/fast/v1/product/attributes',
		{
			productId: productId,
			variantId: variantId,
		}
	);
	const attributes = await apiFetch( { path } );

	return attributes;
};

/**
 * Get a promise that resolves to a list of variation objects from the WooCommerce Store API.
 *
 * @param {number} productId Product ID.
 *
 * @returns {Promise} Promise object resolves to a list of product variations.
 */
export const getProductVariations = ( productId ) => {
	return apiFetch( {
		path: addQueryArgs(
			productsBase,
			{
				per_page: 0,
				type: 'variation',
				parent: productId,
			}
		),
	} );
};
