/**
 * Product variant select component.
 */

import { getProductVariations } from '../utilities';

import PropTypes from 'prop-types';

const { __ } = wp.i18n;
const { useState, useEffect } = wp.element;
const {
	SelectControl,
} = wp.components;

const FastWCProductVariant = ( {
	onChange,
	product,
	variant,
} ) => {
	const selectVariationOption = {
		label: __( 'Select a variation...' ),
		value: '',
	};
	const noVariantsOption = {
		label: __( 'No variations available' ),
		value: '',
	};
	const defaultOptions = [
		{
			label: __( 'Loading variations...' ),
			value: '',
		},
	];

	const [options, setOptions] = useState( defaultOptions );

	const getVariations = async () => {
		const list = await getProductVariations( product );

		let options = [];
		let variantIds = [];

		if ( list.length ) {
			options = list.map( ( variant ) => ( {
				label: variant.variation,
				value: variant.id,
			} ) );
			variantIds = list.map( ( variant ) => variant.id );
			options.unshift( selectVariationOption );
		} else {
			options = [ noVariantsOption ];
		}

		if ( ! variant || ! variantIds.includes( variant ) ) {
			onChange( '' );
		}

		setOptions( options );
	};

	useEffect(
		() => {
			getVariations();
		},
		[ product ]
	);

	const handleSelect = ( variant ) => {
		onChange( variant );
	};

	return (
		<div>
			<SelectControl
				label={ __( 'Variation' ) }
				options={ options }
				onChange={ handleSelect }
				value={ variant }
			/>
		</div>
	)
};

FastWCProductVariant.propTypes = {
	onChange: PropTypes.func.isRequired,
	product: PropTypes.number,
};

FastWCProductVariant.defaultProps = {
	product: 0,
}

export default FastWCProductVariant;
