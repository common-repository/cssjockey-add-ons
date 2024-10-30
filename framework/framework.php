<?php
if( ! class_exists( 'cjwpbldr_framework' ) ) {
	class cjwpbldr_framework {
		private static $instance;
		public $helpers;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __clone() {
			wp_die( __( 'Cheating Hun? This is not allowed!', CJWPBLDR_TEXT_DOMAIN ) );
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			// initiate cmb2
			$cmb2_init_file = sprintf( '%s/framework/lib/cmb2/init.php', $this->helpers->root_dir );
			if( ! file_exists( $cmb2_init_file ) ) {
				$cmb2_init_file = sprintf( '%s/framework/lib/CMB2/init.php', $this->helpers->root_dir );
			}
			if( ! class_exists( 'CMB2' ) && file_exists( $cmb2_init_file ) ) {
				require $cmb2_init_file;
			}


			require_once 'autoload.php';
			$autoload = cjwpbldr_autoload::getInstance();
			$autoload->load();
		}

		public function init() {
			add_filter( 'widget_text', 'do_shortcode' );
		}
	}
}
