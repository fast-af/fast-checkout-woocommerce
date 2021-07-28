/**
 * Product attributes component.
 */


import { getProductAttributes } from '../utilities';
import FastWCProductAttribute from './attribute'

import PropTypes from 'prop-types';

const { __ } = wp.i18n;
const { useState, useEffect } = wp.element;

const FastWCProductAttributes = ( {
	onChange,
	product,
	variant,
	selected,
} ) => {
	const [attKeys, setAttKeys] = useState([]);
	const [labels, setLabels] = useState([]);
	const [options, setOptions] = useState([]);

	useEffect(
		() => {
			getProductAttributes( product, variant )
				.then( ( list ) => {
					const newOptions = {};
					const newAttributes = {};

					Object.keys( selected ).map( ( selectedKey ) => {
						let availableValues = [];

						if ( list.options?.[ selectedKey ]?.length ) {
							availableValues = list.options[ selectedKey ].map( ( option ) => option.value );
						}

						if ( list.attKeys?.includes( selectedKey ) && availableValues.includes( selected[ selectedKey ] ) ) {
							newAttributes[ selectedKey ] = selected[ selectedKey ];
						}
					} );

					if ( list.attKeys?.length ) {
						list.attKeys.map( ( attKey ) => {
							if ( list.values[ attKey ] ) {
								newAttributes[ attKey ] = list.values[ attKey ];
								newOptions[ attKey ] = [
									{
										value: list.values[ attKey ],
										label: list.values[ attKey ],
									},
								];
							} else {
								newOptions[ attKey ] = list.options[ attKey ];
								if ( ! newAttributes[ attKey ] && list.options[ attKey ].length ) {
									const option = list.options[ attKey ][0];
									newAttributes[ attKey ] = option.value;
								}
							}
						} );



						setAttKeys( list.attKeys );
						setLabels( list.labels );
					}

					onChange( newAttributes );
					setOptions( newOptions );
				} );
		},
		[
			product,
			variant,
		]
	);

	const handleAttributeChange = ( attKey, value ) => {
		const newAttributes = { ...selected };
		newAttributes[ attKey ] = value;
		onChange( newAttributes );
	};

	let message = '';

	if ( ! product ) {
		message = __( 'Please select a product and a product variation to see the available options.' );
	} else if ( ! variant ) {
		message = __( 'Please select a product variation to see the available options.' );
	}

	return (
		<div>
			<h3>
				{ __( 'Product Options:' ) }
			</h3>
			{ message &&
				<div>{ message }</div>
			}
			{ attKeys && attKeys.map( ( attKey ) => (
				<FastWCProductAttribute
					key={ attKey }
					attKey={ attKey }
					label={ labels[ attKey ] }
					options={ options[ attKey ] }
					onChange={ handleAttributeChange }
					selected={ selected[ attKey ] ? selected[ attKey ] : '' }
				/>
			) ) }
		</div>
	);
};

FastWCProductAttributes.propTypes = {
	onChange: PropTypes.func.isRequired,
	product: PropTypes.number,
	variant: PropTypes.number,
	selected: PropTypes.object,
};

FastWCProductAttributes.defaultProps = {
	product: 0,
	variant: 0,
	selected: {},
}

export default FastWCProductAttributes;
