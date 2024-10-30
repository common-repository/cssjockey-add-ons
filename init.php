<?php
/*
Plugin Name: WP Builder (formerly CSSJockey Add-ons)
Plugin URI: https://wordpress.org/plugins/cssjockey-add-ons
Description: WP Builder plugin acts as a base framework that allows you to install our various free and premium WordPress based products to enable different functionality for your WordPress website. The main goal is to utilize the common resources and reduce the server load and make it easy to use any of our WordPress based products.
Author: CSSJockey Team
Version: 3.0.7
Author URI: https://cssjockey.com
Text Domain: wp-builder-locale
*/
$is_theme = false;
$current_theme = wp_get_theme();
if( $current_theme->get( 'TextDomain' ) == 'wp-builder' ) {
	$active_theme_dir = get_template_directory();
	$framework_root_dir = dirname( dirname( __FILE__ ) );
	if( $active_theme_dir !== $framework_root_dir ) {
		// use theme framework instead
		return false;
	}
}
ob_start();
if( ! function_exists( 'cjwpbldr_init' ) ) {

	if( ! defined( 'CJWPBLDR_VERSION' ) ) {
		define( 'CJWPBLDR_VERSION', '3.0.7' );
	}
	if( ! defined( 'CJWPBLDR_TEXT_DOMAIN' ) ) {
		define( 'CJWPBLDR_TEXT_DOMAIN', 'wp-builder-locale' );
	}
	if( ! defined( 'CJWPBLDR_ROOT_DIR' ) ) {
		define( 'CJWPBLDR_ROOT_DIR', wp_normalize_path( dirname( __FILE__ ) ) );
	}
	if( ! defined( 'CJWPBLDR_ROOT_URL' ) ) {
		define( 'CJWPBLDR_ROOT_URL', str_replace( wp_normalize_path( ABSPATH ), site_url( '/' ), wp_normalize_path( dirname( __FILE__ ) ) ) );
	}

	if( ! defined( 'CJWPBLDR_WEBSITE_URL' ) ) {
		define( 'CJWPBLDR_WEBSITE_URL', 'https://getwpbuilder.com/' );
	}
	if( ! defined( 'CJWPBLDR_API_URL' ) ) {
		define( 'CJWPBLDR_API_URL', 'https://getwpbuilder.com/wp-json/cjwpbldr' );
	}
	if( ! defined( 'CJWPBLDR_TOOLS_URL' ) ) {
		define( 'CJWPBLDR_TOOLS_URL', 'http://wpbuilder.tools' );
	}
	if( ! defined( 'CJWPBLDR_TOOLS_API_URL' ) ) {
		define( 'CJWPBLDR_TOOLS_API_URL', 'http://wpbuilder.tools/api' );
	}

	function cjwpbldr_init() {
		require_once('framework/helpers.php');
		require_once('framework/framework.php');
		$cjwpbldr = cjwpbldr_framework::getInstance();
		$cjwpbldr->init();
		do_action( 'cjwpbldr_loaded' );
	}

	if( ! $is_theme ) {
		add_action( 'after_setup_theme', 'cjwpbldr_init', 1 );
	} else {
		cjwpbldr_init();
	}
}
