// "use strict"
jQuery( function( $ ) {
	function get_path( src, base_url ) {
		if ( src.substr( 0, base_url.length ) === base_url ) {
			return src.substr( base_url.length );
		}
		return false;
	}
	$( 'img' ).each( function() {
		if ( ! this.complete || ( ! $.browser.msie && ( typeof this.naturalWidth == "undefined" || this.naturalWidth == 0 ) ) ) {
			var path = get_path( this.src, h1aimd.content_base_url );

			if ( path ) {
				this.src = h1aimd.home_url + '/' + h1aimd.endpoint + path + '/';
			}
		}
	});
});