/**
 * Fast button backend component.
 */

import './index.scss';

import icons from '../icons';

const { __ } = wp.i18n;
const { Icon } = wp.components;

const FastButton = ( props ) => {

	const {
		type,
	} = props;

	let label = __( 'Fast Checkout' );

	if ( 'login' === type ) {
		label = __( 'Fast Login' );
	}

	return (
		<button className="fastwc-button">
			<Icon icon={ icons.lock } />
			{ label }
		</button>
	);

};

export default FastButton;
