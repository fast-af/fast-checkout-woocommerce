/**
 * Product search component.
 */

import './index.scss';

import { getProducts } from '../utilities';

import PropTypes from 'prop-types';

const { __ } = wp.i18n;
const { useState, useEffect } = wp.element;
const {
	TextControl,
	RadioControl,
} = wp.components;

const FastWCProductSearch = ( {
	onChange,
	selected,
} ) => {
	const defaultProducts = [];

	const [products, setProducts] = useState( defaultProducts );
	const [isLoading, setIsLoading] = useState( true );
	const [product, setProduct] = useState( selected );

	useEffect(
		() => {
			fetchProducts( { selected } );
		},
		[] // Dependencies. Leave blank to avoid running more than once.
	);

	const handleSearch = ( search ) => {
		setIsLoading( true );
		fetchProducts( { selected: product, search } );
	};

	const fetchProducts = async ( {
		selected,
		search = '',
	} ) => {
		const list = await getProducts( { selected, search } );

		setProducts( list );
		setIsLoading( false );
	};

	return (
		<div>
			<TextControl
				label={ __( 'Product' ) }
				onChange={ handleSearch }
				className="fastwc-product-search"
				placeholder={ __( 'Search for product...' ) }
			/>
			<div className="fastwc-product-search--products-wrapper">
				{ isLoading
					? <div className="fastwc-product-search--loading">{ __( 'Loading...' ) }</div>
					: <RadioControl
						selected={ product }
						options={ products }
						onChange={ ( value ) => {
							const productId = parseInt( value, 10 );
							onChange( productId );
							setProduct( productId );
						} }
					/>
				}
			</div>
		</div>
	);
};

FastWCProductSearch.propTypes = {
	onChange: PropTypes.func.isRequired,
	selected: PropTypes.number,
};

FastWCProductSearch.defaultProps = {
	selected: 0,
}

export default FastWCProductSearch;
