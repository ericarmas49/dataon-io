const { registerPlugin } = wp.plugins;
 
import AWP_Custom_Plugin from './awp-custom-postmeta-fields';
 
registerPlugin( 'my-custom-postmeta-plugin', {
	render() {
		return(<AWP_Custom_Plugin />);
	}
} );