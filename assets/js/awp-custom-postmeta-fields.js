const { __ } = wp.i18n;
const { compose } = wp.compose;
const { withSelect, withDispatch } = wp.data;
 
const { PluginDocumentSettingPanel } = wp.editPost;
const { ToggleControl, TextControl, PanelRow } = wp.components;
 
const AWP_Custom_Plugin = ( { postType, postMeta, setPostMeta } ) => {
	if ( 'post' !== postType ) return null;  // Will only render component for post type 'post'
	
	return(
		<PluginDocumentSettingPanel title={ __( 'My Custom Post meta', 'txtdomain') } icon="edit" initialOpen="true">
			<PanelRow>
				<ToggleControl
					label={ __( 'You can toggle me on or off', 'txtdomain' ) }
					onChange={ ( value ) => setPostMeta( { _my_custom_bool: value } ) }
					checked={ postMeta._my_custom_bool }
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					label={ __( 'Write some text, if you like', 'txtdomain' ) }
					value={ postMeta._my_custom_text }
					onChange={ ( value ) => setPostMeta( { _my_custom_text: value } ) }
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
 
export default compose( [
	withSelect( ( select ) => {		
		return {
			postMeta: select( 'core/editor' ).getEditedPostAttribute( 'meta' ),
			postType: select( 'core/editor' ).getCurrentPostType(),
		};
	} ),
	withDispatch( ( dispatch ) => {
		return {
			setPostMeta( newMeta ) {
				dispatch( 'core/editor' ).editPost( { meta: newMeta } );
			}
		};
	} )
] )( AWP_Custom_Plugin );