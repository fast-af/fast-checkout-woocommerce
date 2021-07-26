/**
 * Single product attribute.
 */

import PropTypes from 'prop-types';

const { useState, useEffect } = wp.element;
const {
	SelectControl
} = wp.components;

const FastWCProductAttribute = ( {
	onChange,
	attKey,
	label,
	options,
	selected,
} ) => {

	const onSelect = ( value ) => {
		onChange( attKey, value );
	};

	return (
		<SelectControl
			label={ label }
			onChange={ onSelect }
			options={ options }
			value={ selected }
		/>
	)
};

FastWCProductAttribute.propTypes = {
	onChange: PropTypes.func.isRequired,
	attKey: PropTypes.string,
	label: PropTypes.string,
	options: PropTypes.array,
	selected: PropTypes.string,
};

FastWCProductAttribute.defaultProps = {
	options: [],
};

export default FastWCProductAttribute;
