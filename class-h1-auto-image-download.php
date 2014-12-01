<?php

class H1_AutoImageDownload {
	private $options;
	private $content_base_url;
	private $content_base_dir;

	private $version = '1.1';
	private $endpoint = 'h1_auto_image_download';

	private $option_name = 'h1_auto_image_download';

	public function __construct() {
		$this->options = get_option( $this->option_name );

		// Validate options.
		if ( $this->options === false
			|| ! $this->options['url' ]
			|| ! $this->options['allowed_extensions'] ) {
			// Plugin not set up properly, cancel execution.
			return;
		}

		// Get WordPress installation base url and dir.
		$wp_upload_dir = wp_upload_dir();
		$this->content_base_url = $wp_upload_dir['baseurl'];
		$this->content_base_dir = $wp_upload_dir['basedir'];

		// Register script that will detect 404s.
		wp_register_script( 'h1_auto_image_download', plugins_url( 'h1-auto-image-download.js', __FILE__ ), 'jquery', $this->version, true );
		wp_localize_script( 'h1_auto_image_download', 'h1aimd', array(
			'home_url' => get_home_url(),
			'content_base_url' => $this->content_base_url,
			'endpoint' => $this->endpoint,
		) );
		wp_enqueue_script( 'h1_auto_image_download' );

		// Register endpoint url that the JavaScript will use.
		add_rewrite_endpoint( $this->endpoint, EP_ROOT );

		// Register the URL processor.
		add_action( 'parse_query', array( $this, 'process_url' ) );
	}

	/**
	 * Check if the URL contains the endpoint and then download the image or redirect.
	 */
	public function process_url() {
		global $wp_query;
		if ( isset( $wp_query->query_vars[ $this->endpoint ] ) ) {
			$img_path = $wp_query->query_vars[ $this->endpoint ];
			// check for setting whether to redirect or first download the image
			if ( $this->options[ 'download' ] ) {
				$this->try_download( $img_path );
			} else {
				$this->redirect( $img_path );
			}
		}
	}

	/**
	 * Redirect to mirror url by image path
	 * @param string $img_path Path of the image inside uploads folder
	 */
	public function redirect( $img_path ) {
		$mirror_url = $this->get_mirror_url( $img_path );
		if ( false === $mirror_url ) {
			status_header( 404 );
			exit;
		} else {
			wp_redirect( $mirror_url, 301 );
			exit;
		}
	}

	/**
	 * Try downloading and saving the image locally and then redirect to it.
	 * @param string $img_path Path of the image inside uploads folder
	 */
	public function try_download( $img_path ) {
		if ( ! function_exists('WP_Filesystem') ) {
			require ABSPATH . 'wp-admin/includes/file.php';
		}
		global $wp_filesystem;
		WP_Filesystem();

		$mirror_url = $this->get_mirror_url( $img_path );
		if ( $mirror_url === false ) {
			status_header( 404 );
			exit;
		}

		// Download
		$response = wp_remote_get( $mirror_url );

		// Die if not successful.
		if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {
			wp_die( __( 'Unable to download the file.', 'h1aid' ) );
		}

		$body = wp_remote_retrieve_body( $response );

		$abspath = $this->content_base_dir;
		$destination = trailingslashit( $abspath ) . $img_path;

		// Save to file system.
		$result = $wp_filesystem->put_contents( $destination, $response['body'], FS_CHMOD_FILE ); // predefined mode settings for WP files

		// Redirect if successful.
		if ( true === $result ) {
			wp_redirect( trailingslashit( $this->content_base_url ) . $img_path, 301 );
			exit;
		} else {
			wp_die( __( 'Unable to save file to filesystem.', 'h1aid' ) );
		}
	}

	/**
	 * Get the URL of the file on the site we are mirroring.
	 * @param string $path Path of the file, without the uploads root part
	 * @return string The mirror URL
	 */
	public function get_mirror_url( $path ) {
		if ( substr( $path, 0, 1 ) == '/' ) {
			$path = substr( $path, 1 );
		}

		$path_parts = pathinfo( $path );
		$extension = $path_parts[ 'extension' ];

		if ( ! $this->validate_extension(
			$extension,
			$this->options[ 'allowed_extensions' ] ) ) {
			return false;
		}

		return trailingslashit( $this->options['url'] ) . $path;
	}

	/**
	 * Make sure url ends with one of the allowed extensions.
	 * @param string $extension Extension to validate
	 * @param string $allowed_extensions Comma separated list of allowed extensions
	 * @return bool Whether the extension is allowed
	 */
	public function validate_extension( $extension, $allowed_extensions ) {
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