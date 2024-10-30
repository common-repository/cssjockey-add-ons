<?php
// Exit if accessed directly.
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$traits_dir_path = sprintf( '%s/traits/helpers/', dirname( __FILE__ ) );
$dirs = preg_grep( '/^([^.])/', scandir( $traits_dir_path ) );
if( is_array( $dirs ) && ! empty( $dirs ) ) {
	foreach( $dirs as $key => $file_name ) {
		require_once sprintf( '%s/%s', $traits_dir_path, $file_name );
	}
}

if( ! class_exists( 'cjwpbldr_helpers' ) ) {
	/**
	 * Class cjwpbldr_helpers
	 */
	class cjwpbldr_helpers {

		use cjwpbldr_helpers_arrays,
			cjwpbldr_helpers_builder,
			cjwpbldr_helpers_database,
			cjwpbldr_helpers_filesystem,
			cjwpbldr_helpers_forms,
			cjwpbldr_helpers_framework,
			cjwpbldr_helpers_images,
			cjwpbldr_helpers_options,
			cjwpbldr_helpers_posts,
			cjwpbldr_helpers_rest,
			cjwpbldr_helpers_strings,
			cjwpbldr_helpers_utils,
			cjwpbldr_helpers_users,
			cjwpbldr_helpers_vendors,
			cjwpbldr_helpers_wordpress;

		/**
		 * @var
		 */
		private static $instance;

		public $root_dir,
			$root_url,
			$site_url,
			$framework_dir,
			$framework_url,
			$assets_dir,
			$assets_url,
			$config_dir,
			$config_url,
			$ui_block_url,
			$api_url,
			$textdomain,
			$upload_dir,
			$upload_path,
			$default_avatar_url,
			$default_post_featured_image_url,
			$saved_options,
			$table_options,
			$table_custom_data,
			$options_key,
			$cjwpbldr_content_path,
			$cjwpbldr_content_url,
			$user_info_fields_public,
			$user_info_fields,
			$post_info_fields_public,
			$post_info_fields,
			$upload_url,
			$cjwpbldr_title,
			$post_max_size,
			$upload_max_file_size,
			$form_field_types,
			$is_premium,
			$comments_template_path = '',
			$is_theme;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * cjwpbldr_helpers constructor.
		 */
		public function __construct() {
			global $current_user;
			wp_get_current_user();
			$this->site_url = get_bloginfo( 'url' );
			$this->root_dir = wp_normalize_path( CJWPBLDR_ROOT_DIR );
			$this->root_url = wp_normalize_path( CJWPBLDR_ROOT_URL );

			$this->framework_dir = $this->root_dir . '/framework';
			$this->framework_url = $this->root_url . '/framework';
			$this->assets_dir = $this->root_dir . '/assets';
			$this->assets_url = $this->root_url . '/assets';
			$this->config_dir = $this->root_dir . '/config';
			$this->config_url = $this->root_url . '/config';
			$this->ui_block_url = $this->root_url . '/ui-blocks';
			$this->api_url = '';
			if( function_exists( 'rest_url' ) && function_exists( 'using_index_permalinks' ) ) {
				$this->api_url = rest_url( 'cjwpbldr' );
			}
			$this->textdomain = CJWPBLDR_TEXT_DOMAIN;
			$this->upload_dir = wp_upload_dir();
			$this->upload_path = $this->upload_dir['basedir'];
			$this->upload_url = $this->upload_dir['baseurl'];
			$this->default_avatar_url = 'https://getwpbuilder.com/media/samples/default-avatar.png';
			$this->default_post_featured_image_url = 'https://getwpbuilder.com/media/samples/default-thumb.png';
			$this->table_options = $this->wpTableName( $this->config( 'plugin_info', 'options_table' ) );
			$this->options_key = '_cjwpbldr_options_';
			$this->saved_options = get_option( $this->options_key );
			$this->cjwpbldr_content_path = $this->upload_dir['basedir'] . '/wp-builder';
			$this->cjwpbldr_content_url = str_replace( ABSPATH, site_url( '/' ), $this->cjwpbldr_content_path );
			$this->user_info_fields_public = ['ID', 'first_name', 'last_name', 'display_name', 'default_avatar', 'gravatar', 'nickname', 'description', 'user_url', 'posts_link_url'];
			$this->user_info_fields = ['ID', 'user_login', 'user_email', 'first_name', 'last_name', 'display_name', 'default_avatar', 'gravatar', 'nickname', 'description', 'user_registered', 'user_url', 'posts_link_url'];
			$this->post_info_fields_public = ['ID', 'post_title', 'post_excerpt', 'permalink'];
			$this->post_info_fields = ['ID', 'post_title', 'post_excerpt', 'permalink'];
			$this->is_theme = $this->isTheme();
			$this->cjwpbldr_title = ($this->isTheme()) ? $this->config( 'plugin-info', 'theme_name' ) : $this->config( 'plugin-info', 'name' );
			$this->table_custom_data = 'cjwpbldr_data';
			$this->post_max_size = @ini_get( 'post_max_size' );
			$this->upload_max_file_size = @ini_get( 'upload_max_filesize' );
			$this->is_premium = get_transient( '_wp_builder_pro_key' );
			$this->form_field_types = [
				'text' => [],
				'select' => [
					'select',
					'select-raw',
					'dropdown',
					'country',
					'nav_menu',
					'nav-menu',
					'nav_menus',
					'nav-menus',
					'countries',
					'user',
					'role',
					'page',
					'post',
					'custom-post',
					'multi-dropdown',
					'multi-select',
					'users',
					'roles',
					'pages',
					'posts',
					'custom-posts',
					'post-type',
					'post-types',
					'tag',
					'tags',
					'sidebar',
					'sidebars',
					'category',
					'categories',
					'taxonomy-term',
					'taxonomy-terms',
					'taxonomy',
					'taxonomies',
					'font-family',
				],
				'select_multiple' => ['multi-dropdown', 'multi-select', 'users', 'roles', 'pages', 'posts', 'custom-posts', 'post-types', 'tags', 'categories', 'taxonomies', 'countries', 'nav_menus', 'nav-menus', 'sidebars', 'taxonomy-terms'],
			];

			if( ! function_exists( 'request_filesystem_credentials' ) ) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}
			add_filter( 'cmb2_sanitize_conditional', [$this, 'cmb2_sanitize_conditional_callback'], 10, 5 );

			add_filter( 'comments_template', [$this, 'wpCommentsTemplateOverride'], 10 );
			/*add_action( 'admin_init', function () {} );*/
		}
	}
}
