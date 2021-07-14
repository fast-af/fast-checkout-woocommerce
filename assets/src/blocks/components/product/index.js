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
	products,
	isLoading,
	isCompact,
	isSingle,
} ) => {

	const [products, setProducts] = useState([]);
	const [isLoading, setLoading] = useState(true);

	useEffect( () => {
		getProducts( { selected } )
			.then( ( list ) => {
				setProducts( list );
				setLoading( false );
			} );
		// TODO: Maybe add error handling.
	} );

	const onSearch = ( search ) => {
		setLoading( true );

		getProducts( { selected, search } )
			.then( ( list ) => {
				setProducts( list );
				setLoading( false );
			} );
		// TODO: Maybe add error handling.
	};

	return (
		<SearchListControl
			className="woocommerce-products fast-wc-product-search"
			list={ products }
			isCompact={ isCompact }
			isLoading={ isLoading }
			onSearch={ onSearch }
			onChange={ onChange }
		/>
	);
};

FastWCProductSearch.propTypes = {
	onChange: PropTypes.func.isRequired,
	selected: PropTypes.array,
	products: PropTypes.array,
	isLoading: PropTypes.bool,
	isCompact: PropTypes.bool,
	isSingle: PropTypes.bool,
};

FastWCProductSearch.defaultProps = {
	selected: [],
	products: [],
	isLoading: true,
	isCompact: true,
	isSingle: true,
}

export default FastWCProductSearch;
