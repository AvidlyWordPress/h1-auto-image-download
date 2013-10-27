<?php
/*
Plugin Name: H1 Auto Image Download
Version: 1.0
Author URI: http://h1.fi/
Plugin URI: http://h1.fi/plugins/h1-auto-image-download
Description: If an image is not found, this plugins downloads the image from another server. Good for development purposes, when you have copied the database over from production server, but not want to bother with dowloading all the images. <strong>Not intended for production use</strong>.
Author: Aki Björklund/H1
License: GPLv2
*/

require_once( 'class-h1-auto-image-download.php');
require_once( 'class-h1-auto-image-download-admin.php');

add_action( 'init', 'h1_auto_image_download_init' );

function h1_auto_image_download_init() {
	$auto_image_download = new H1_AutoImageDownload;

	if ( is_admin() ) {
		new H1_AutoImageDownload_Admin( H1_AutoImageDownload::$option_name );
	}
}