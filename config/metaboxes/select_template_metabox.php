<?php
if( ! class_exists( 'cjwpbldr_select_template_metabox' ) ) {
	class cjwpbldr_select_template_metabox {

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
			add_action( 'cmb2_admin_init', [$this, 'init'] );
		}

		public function init() {
			global $pagenow;
			if( ! isset( $_GET['post'] ) ) {
				return false;
			}
			$default_template_id = '0';
			$template_info = $this->helpers->getTemplateInfoByPostId( $_GET['post'] );
			if( ! empty( $template_info ) ) {
				$default_template_id = $template_info['ID'];
			}
			$exclude = [
				/*'cjwpbldr-templates',
				'cjwpbldr-ui-blocks',
				'cjwpbldr-custom-forms',
				'attachment',
				'revision',
				'nav_menu_item',
				'attachment',
				'cjwpbldr-sidebars',
				'cjwpbldr-post-types',
				'cjwpbldr-taxonomies',*/
			];

			$post_types_array = [];
			$post_types = get_post_types( [
				'public' => true
			] );
			foreach( $post_types as $post_type ) {
				$post_type_object = get_post_type_object( $post_type );
				if( ! in_array( $post_type, $exclude ) && $post_type_object->public ) {
					$post_types_array[] = $post_type;
				}
			}

			/**
			 * Initiate the metabox
			 */
			$cmb = new_cmb2_box( [
				'id' => 'cj_template_',
				'title' => '<span class="cssjockey-ui">' . esc_attr__( 'WP Builder Template', 'wp-builder-locale' ) . '</span>',
				'object_types' => $post_types_array,
				'context' => 'side',
				'priority' => 'high',
				'show_names' => true,
			] );

			$cmb->add_field( [
				'name' => '',
				'desc' => '',
				'id' => '_cjwpbldr_use_template_singular',
				'type' => 'select',
				'default' => $default_template_id,
				'options' => $this->helpers->customTemplatesArray(),
			] );
		}

	}

	cjwpbldr_select_template_metabox::getInstance();
}