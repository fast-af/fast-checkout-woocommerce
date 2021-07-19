/**
 * Product attributes component.
 */


import { getProductAttributes } from '../utilities';

import PropTypes from 'prop-types';

const { __ } = wp.i18n;
const { useState, useEffect } = wp.element;
const {
	SelectControl,
} = wp.components;

const FastWCProductAttributes = ( {
	onChange,
	product,
	selected,
} ) => {
	const [attributes, setAttributes] = useState([]);

	useEffect(
		() => {
			getProductAttributes( product )
				.then( ( list ) => {
					console.log( 'attributes', list );
				} );
		},
		[ product ]
	);

	return (
		<div>
			Attributes control
		</div>
	)
}

FastWCProductAttributes.propTypes = {
	onChange: PropTypes.func.isRequired,
	product: PropTypes.number,
	selected: PropTypes.array,
};

FastWCProductAttributes.defaultProps = {
	product: 0,
	selected: [],
}

export default FastWCProductAttributes;
