<?php
if( ! class_exists( 'cjwpbldr_uninstall' ) ) {
	class cjwpbldr_uninstall {
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
			add_action( 'admin_init', array($this, 'restoreSettings') );
		}

		public function restoreSettings() {
			if( isset( $_GET['cjwpbldr_action'] ) && $_GET['cjwpbldr_action'] == 'restore-settings' ) {
				if( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'You are not authorised to perform this action.', 'wp-builder-locale' ) );
				}

				$options = $this->helpers->dbSelect( 'options', '*', 'option_name LIKE "%cjwpbldr%"' );
				if( is_array( $options ) && ! empty( $options ) ) {
					foreach( $options as $key => $option ) {
						delete_option( $option['option_name'] );
					}
				}
				wp_redirect( $this->helpers->callbackUrl( 'core-uninstall' ) );
				die();
			}

			if( isset( $_GET['cjwpbldr_action'] ) && $_GET['cjwpbldr_action'] == 'remove-all-addons' ) {
				if( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'You are not authorised to perform this action.', 'wp-builder-locale' ) );
				}
				$all_addons = $this->helpers->installedAddons();
				if( is_array( $all_addons ) ) {
					foreach( $all_addons as $key => $addon ) {
						if( ! $this->helpers->containString( 'modules', $addon['addon_path'] ) ) {
							$this->helpers->deleteDirectory( $addon['addon_path'] );
						}
					}
				}
				wp_redirect( $this->helpers->callbackUrl( 'core-uninstall' ) );
				die();
			}

			if( isset( $_GET['cjwpbldr_action'] ) && $_GET['cjwpbldr_action'] == 'remove-all-blocks' ) {
				if( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'You are not authorised to perform this action.', 'wp-builder-locale' ) );
				}
				$all_blocks = $this->helpers->installedUiBlocks();
				foreach( $all_blocks as $key => $block ) {
					if( $this->helpers->containString( $this->helpers->cjwpbldr_content_path, $block['path'] ) ) {
						$this->helpers->deleteDirectory( $block['path'] );
					}
				}
				wp_redirect( $this->helpers->callbackUrl( 'core-uninstall' ) );
				die();
			}

			if( isset( $_GET['cjwpbldr_action'] ) && $_GET['cjwpbldr_action'] == 'remove-all-templates' ) {
				if( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'You are not authorised to perform this action.', 'wp-builder-locale' ) );
				}
				$all_templates = $this->helpers->dbSelect( 'posts', '*', [
					'post_type' => 'cjwpbldr-templates'
				] );
				if( ! empty( $all_templates ) ) {
					foreach( $all_templates as $key => $template ) {
						wp_delete_post( $template['ID'], true );
					}
				}

				wp_redirect( $this->helpers->callbackUrl( 'core-uninstall' ) );
				die();
			}
		}
	}

	cjwpbldr_uninstall::getInstance();
}