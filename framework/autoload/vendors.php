<?php
if( ! class_exists( 'cjwpbldr_vendors' ) ) {
	class cjwpbldr_vendors {
		private static $instance;
		public $helpers;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			/* add_filter( 'cmb2_enqueue_css', function () {
				$current_screen = get_current_screen();
				if( $current_screen->post_type == 'cjwpbldr-ui-blocks' && $current_screen->base == 'post' && isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
					return false;
				}
			} ); */

			add_filter( 'cmb2_show_on', array($this, 'cmb2ShowOnMetaValue'), 10, 2 );
			$this->loadFields();
		}

		public function cmb2ShowOnMetaValue( $display, $meta_box ) {
			if( ! isset( $meta_box['show_on']['meta_key'] ) ) {
				return $display;
			}
			$post_id = 0;
			// If we're showing it based on ID, get the current ID
			if( isset( $_GET['post'] ) ) {
				$post_id = $_GET['post'];
			} elseif( isset( $_POST['post_ID'] ) ) {
				$post_id = $_POST['post_ID'];
			}
			if( ! $post_id ) {
				return $display;
			}
			$value = get_post_meta( $post_id, $meta_box['show_on']['meta_key'], true );
			if( empty( $meta_box['show_on']['meta_value'] ) ) {
				return (bool) $value;
			}

			return $value == $meta_box['show_on']['meta_value'];
		}

		public function loadFields() {
			$path = sprintf( '%s/cmb2-custom-fields', dirname( __FILE__ ) );
			$fields_dirs = preg_grep( '/^([^.])/', scandir( $path ) );
			if( is_array( $fields_dirs ) && ! empty( $fields_dirs ) ) {
				foreach( $fields_dirs as $key => $file ) {
					if( $this->helpers->containString( '.php', $file ) ) {
						require_once $path . '/' . $file;
					}
				}
			}
		}
	}

	cjwpbldr_vendors::getInstance();
}