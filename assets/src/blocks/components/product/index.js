/**
 * Product search component.
 */

import { getProducts } from '../utilities';

import { SearchListControl } from '@woocommerce/components';
import PropTypes from 'prop-types';

const { useState, useEffect } = wp.element;

const FastWCProductSearch = ( {
	onChange,
	selected,
} ) => {
	const defaultProducts = [];
	const defaultLoading = true;

	const [products, setProducts] = useState( defaultProducts );

	useEffect( () => {
		getProducts( { selected } )
			.then( ( list ) => {
				setProducts( list );
			} );
		// TODO: Maybe add error handling.
	} );

	const onSearch = ( search ) => {
		getProducts( { selected, search } )
			.then( ( list ) => {
				setProducts( list );
			} );
		// TODO: Maybe add error handling.
	};

	return (
		<SearchListControl
			className="woocommerce-products fast-wc-product-search"
			list={ products }
			onChange={ onSearch }
		/>
	);
};

FastWCProductSearch.propTypes = {
	onChange: PropTypes.func.isRequired,
	selected: PropTypes.array,
};

FastWCProductSearch.defaultProps = {
	selected: [],
}

export default FastWCProductSearch;
