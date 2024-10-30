<?php
if( ! defined( 'WPINC' ) ) {
	die;
}
if( ! class_exists( 'cjwpbldr_autoload' ) ) {
	class cjwpbldr_autoload {
		private static $instance;
		public $helpers;
		public $autoload_dir;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->autoload_dir = $this->helpers->framework_dir . '/autoload/';
		}

		public function load() {
			if( ! class_exists( 'GUMP' ) ) {
				require_once 'lib/gump/gump.class.php';
			}
			require_once 'lib/vendor/autoload.php';
			// classes
			$directory_path = $this->autoload_dir;
			if( is_dir( $directory_path ) ) {
				$autoload_dirs = preg_grep( '/^([^.])/', scandir( $directory_path ) );
				if( is_array( $autoload_dirs ) && ! empty( $autoload_dirs ) ) {
					foreach( $autoload_dirs as $index => $file ) {
						if( strpos( $file, '.php' ) && file_exists( $this->autoload_dir . $file ) ) {
							require_once $this->autoload_dir . $file;
						}
					}
				}
			}

			// cmb2 meta boxes
			$meta_boxes_dir = $this->helpers->root_dir . '/config/metaboxes';
			if( is_dir( $meta_boxes_dir ) ) {
				$meta_boxes_files = preg_grep( '/^([^.])/', scandir( $meta_boxes_dir ) );
				if( is_array( $meta_boxes_files ) && ! empty( $meta_boxes_files ) ) {
					foreach( $meta_boxes_files as $index => $file ) {
						$file_path = "{$meta_boxes_dir}/{$file}";
						if( file_exists( $file_path ) ) {
							require_once $file_path;
						}
					}
				}
			}

			// shortcodes
			$directory_path = $this->helpers->root_dir . '/shortcodes';
			if( is_dir( $directory_path ) ) {
				$shortcode_dirs = preg_grep( '/^([^.])/', scandir( $directory_path ) );
				if( is_array( $shortcode_dirs ) && ! empty( $shortcode_dirs ) ) {
					foreach( $shortcode_dirs as $index => $folder ) {
						$file_path = "{$this->helpers->root_dir}/shortcodes/{$folder}/init.php";
						if( file_exists( $file_path ) ) {
							require $file_path;
						}
					}
				}
			}

			// modules
			$directory_path = $this->helpers->root_dir . '/modules';
			if( is_dir( $directory_path ) ) {
				$modules_dirs = preg_grep( '/^([^.])/', scandir( $directory_path ) );
				if( is_array( $modules_dirs ) && ! empty( $modules_dirs ) ) {
					foreach( $modules_dirs as $index => $folder ) {
						$file_path = "{$this->helpers->root_dir}/modules/{$folder}/index.php";
						if( file_exists( $file_path ) ) {
							require $file_path;
						}
					}
				}
			}
		}
	}
}
