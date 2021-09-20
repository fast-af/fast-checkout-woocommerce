/**
 * Button to copy text to the clipboard.
 */

const { Button } = wp.components;
const { useCopyToClipboard } = wp.compose;

const CopyButton = ( props ) => {

	const {
		copyText,
		children,
		onSuccess,
	} = props;

	const ref = useCopyToClipboard( copyText, onSuccess );

	return (
		<Button
			{...props}
			ref={ ref }
		>
			{ children }
		</Button>
	);

};

CopyButton.defaultProps = {
	copyText: '',
	onSuccess: () => {},
	isPrimary: true,
};

export default CopyButton;
