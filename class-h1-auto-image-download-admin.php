<?php

class H1_AutoImageDownload_Admin {
	private $option_name;
	private $options_section;
	private $options_group;
	private $options_page_name;

	private $options;

	private $default_extensions;

	function __construct( $option_name ) {
		$this->option_name       = $option_name;
		$this->options_section   = $option_name . '_options_section';
		$this->options_group     = $option_name  . '_options_group';
		$this->options_page_name = str_replace( '_', '-', $option_name ) . '-options';

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_page_init' ) );

		$this->options = get_option( $this->option_name );

		$this->default_extensions = 'jpg, jpeg, png, gif';
	}

	function admin_page_init() {
		register_setting(
			$this->options_group,
			$this->option_name
		);

		add_settings_section(
			$this->options_section,
			__( 'Settings', 'h1_auto_image_download' ),
			array( $this, 'print_section_info' ),
			$this->options_page_name
		);

		add_settings_field(
			$this->option_name . '_content_root_url',
			__( 'Uploads Root Url', 'h1_auto_image_download' ),
			array( $this, 'download_content_root_callback' ),
			$this->options_page_name,
			$this->options_section
		);

		add_settings_field(
			$this->option_name . '_allowed_extensions',
			__( 'Allowed extensions', 'h1_auto_image_download' ),
			array( $this, 'allowed_extensions_callback' ),
			$this->options_page_name,
			$this->options_section
		);
	}

	function print_section_info() {
		print wpautop( __( "Uploads Root Url is the content folder of a site you want to download files from, for example http://example.com/wp-content/uploads. Without this setting this plugin will not work.\n\nAllowed extensions lets you set the file types you want to download automatically. Type a comma separated list of extensions here.", 'h1_auto_image_download' ) );
	}

	function download_content_root_callback() {
		printf(
			'<input type="url" id="' . $this->option_name . '_url" name="' . $this->option_name . '[url]" value="%s" />',
			isset( $this->options[ 'url' ] ) ? esc_attr( $this->options[ 'url' ] ) : ''
		);
	}

	function allowed_extensions_callback() {
		$extensions = isset( $this->options[ 'allowed_extensions' ] ) ? $this->options[ 'allowed_extensions' ] : $this->default_extensions;

		printf(
			'<input type="text" id="' . $this->option_name . '_allowed_extensions" name="' . $this->option_name . '[allowed_extensions]" value="%s" />',
			esc_attr( $extensions )
		);
	}

	function plugin_menu() {
		add_options_page(
			__( 'H1 Auto Image Download Options', 'h1_auto_image_download' ),
			__( 'H1 Auto Image Download', 'h1_auto_image_download' ),
			'manage_options',
			$this->options_page_name,
			array( $this, 'plugin_options' )
		);
	}

	function plugin_options() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><? _e( 'H1 Auto Image Download Options', 'h1_auto_image_download' ) ?></h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( $this->options_group );
				do_settings_sections( $this->options_page_name );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}
}