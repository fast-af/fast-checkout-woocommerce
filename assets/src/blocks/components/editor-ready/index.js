/**
 * Returns a promise that resolves when the editor is ready.
 */

const { select, subscribe } = wp.data;

const isEditorReady = async () =>
	new Promise( ( resolve ) => {
		const unsubscribe = subscribe( () => {
			// When the current post type is available, the editor is ready.
			const currentPostType = select( 'core/editor' ).getCurrentPostType();
			if ( currentPostType ) {
				unsubscribe();
				resolve();
			}
		} );
	} );

export default isEditorReady;
