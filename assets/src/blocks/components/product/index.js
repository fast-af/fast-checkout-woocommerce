/**
 * Product search component.
 */

import { getProducts, getProductAttributes } from '../utilities';

import PropTypes from 'prop-types';
import Select from 'react-select';

const { __ } = wp.i18n;
const { useState, useEffect } = wp.element;

const FastWCProductSearch = ( {
	onChange,
	selected,
} ) => {
	const defaultProducts = [];
	const defaultLoading = true;

	const [products, setProducts] = useState( defaultProducts );
	const [isLoading, setIsLoading] = useState( true );
	const [initialized, setInitialized] = useState( false );

	useEffect( () => {
		init();
	} );

	const init = () => {
		if ( ! initialized ) {
			setInitialized( true );
			setIsLoading( true );
			getProducts( { selected } )
				.then( ( list ) => {
					setProducts( list );
					setIsLoading( false );
				} );
		}
	};

	const onSearch = ( search, action ) => {
		console.log( 'action', action );
		setIsLoading( true );
		getProducts( { selected, search } )
			.then( ( list ) => {
				setProducts( list );
				setIsLoading( false );
			} );
		// TODO: Maybe add error handling.
	};

	const onSelectChange = ( product ) => {
		console.log( 'onSelectChange', product );
	};

	return (
		<div>
			<div>Test</div>
			<Select
				isClearable
				placeholder={ __( 'Select a product...' ) }
				onChange={ onSelectChange }
				options={ products }
				noOptionsMessage={ __( 'No products found' ) }
				isLoading={isLoading}
				onInputChange={onSearch}
			/>
		</div>
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
