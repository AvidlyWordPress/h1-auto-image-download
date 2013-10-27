<?php

class H1_AutoImageDownload {
    private $options;
    private $content_base_url;
    private $content_base_dir;

    public static $option_name = 'h1_auto_image_download';

    function __construct() {
    	$this->options = get_option( self::$option_name );
    	//validate options
		if ( $this->options === false
			|| ! $this->options[ 'url' ]
			|| ! $this->options[ 'allowed_extensions' ] ) {
			//plugin not set up properly, cancel execution
			return;
		}

		$wp_upload_dir = wp_upload_dir();
		$this->content_base_url = $wp_upload_dir[ 'baseurl' ];
		$this->content_base_dir = $wp_upload_dir[ 'basedir' ];

    	add_action( 'template_redirect', array( $this, 'h1_auto_image_download_filter_404' ), 0 );
    }

	function h1_auto_image_download_filter_404() {
		if ( ! is_404() ) return false;

		$mirror_path = $this->get_valid_mirror_path();
		if ( $mirror_path === false ) return;

		$path_parts = pathinfo( $mirror_path );

		// the domain and path images are downloaded from
		// for example http://example.com/wp-content/uploads/
		$image_mirror_root = $this->options[ 'url' ];

		//download
		$todownload = untrailingslashit( $image_mirror_root ) . $mirror_path;
		$response = wp_remote_get( $todownload );
		if ( is_wp_error( $response ) ) {
			return;
		}
		$body = wp_remote_retrieve_body( $response );

		//create folders, if not exists
		$full_path = $this->content_base_dir . $path_parts[ 'dirname' ];
		if ( ! file_exists( $full_path ) ) {
			$root_permissions = substr( sprintf( '%o', fileperms( $this->content_base_dir ) ), -4 );
			mkdir( $this->content_base_dir . $path_parts[ 'dirname' ], $root_permissions, true );
		}

		//save
		file_put_contents( trailingslashit( $full_path ) . $path_parts[ 'basename' ], $body );

		//redirect to the same file, but with a querystring to prevent unintended redirect loops
		header( 'Location: ' . $this->content_base_url . $mirror_path . '?downloaded' );
		die();
	}

	function get_valid_mirror_path() {
		//get request path
		$path = urldecode( $_SERVER[ 'REQUEST_URI' ] );
		if ( substr( $path, 0, 1 ) == '/' ) {
			$path = substr( $path, 1 );
		}

		$path_remaining = $this->validate_common_path( $this->content_base_url, $path );
		if ( $path_remaining === false ) {
			return false;
		}

		$path_parts = pathinfo( $path_remaining );
		$extension = $path_parts[ 'extension' ];

		if ( ! $this->validate_extension(
			$extension,
			$this->options[ 'allowed_extensions' ] ) ) {
			return false;
		}

		return $path_remaining;
	}

	//find $content_directory_url's and $path's common part
	function validate_common_path( $content_directory_url, $path ) {
		$len = 1;
		while ( $len < strlen( $content_directory_url ) ) {
			if ( substr( $content_directory_url, -$len ) == substr($path, 0, $len ) )
				break;
			$len++;
		}
		if ( $len >= strlen( $content_directory_url ) ) {
			//no common part found
			return false;
		}
		//store the part after common part to $path_remaining
		$path_remaining = substr( $path, $len );

		return $path_remaining;
	}

	//make sure url ends with one of the allowed extensions
	function validate_extension( $extension, $allowed_extensions ) {
		//prepare allowed_extensions
		$extensions = explode( ',', $allowed_extensions );
		$extensions = array_map( 'trim', $extensions );
		$extensions = array_map( 'strtolower', $extensions );

		if ( ! in_array(
				strtolower( $extension ),
				$extensions )
			) {
			return false;
		}
		return true;
	}
}