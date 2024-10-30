<?php
if( ! class_exists( 'cjwpbldr_gutenberg_blocks' ) ) {
	class cjwpbldr_gutenberg_blocks {
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
			add_action( 'init', [$this, 'init'] );
		}

		public function init() {

			if( ! is_admin() ) {
				return false;
			}

			//$assets_path = site_url( 'wp-content/plugins/cjwpbldr-block/dist' );
			$assets_path = $this->helpers->framework_url . '/lib/gutenberg';

			wp_register_style( 'cjwpbldr_gutenberg_block_css', $assets_path . '/blocks.style.build.css', ['wp-editor'], null );
			wp_register_style( 'cjwpbldr_gutenberg_block_editor_css', $assets_path . '/blocks.editor.build.css', ['wp-edit-blocks'], null );
			$locale = [];
			if( is_admin() && current_user_can( 'publish_posts' ) ) {
				$gutenberg_blocks = $this->helpers->getPosts( [
					'post_type' => 'cjwpbldr-ui-blocks',
					'post_status' => 'publish',
					'posts_per_page' => 1000000,
				] );
				foreach( $gutenberg_blocks as $key => $block ) {
					if( isset( $block['_ui_block_class_name'] ) && class_exists( $block['_ui_block_class_name'] ) ) {
						$class = $block['_ui_block_class_name']::getInstance();
						$locale['blocks'][ $key ] = [
							'id' => $block['ID'],
							'uib_id' => str_replace( '_', '-', str_replace( 'cjwpbldr_', '', $class->info['class_name'] ) ),
							'screenshot' => $class->info['screenshot'],
							'title' => $block['ID'],
							'description' => $block['post_excerpt'],
						];
					}
				}
			}
			wp_register_script( 'cjwpbldr_gutenberg_block_js', $assets_path . '/blocks.build.js', ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'], null, true );
			wp_localize_script( 'cjwpbldr_gutenberg_block_js', 'cjwpbldrGutenberg', $locale );

			register_block_type(
				'cjwpbldr/gutenberg-ui-block', [
					// Enqueue blocks.style.build.css on both frontend & backend.
					'style' => 'cjwpbldr_gutenberg_block_css',
					// Enqueue blocks.build.js in the editor only.
					'editor_script' => 'cjwpbldr_gutenberg_block_js',
					// Enqueue blocks.editor.build.css in the editor only.
					'editor_style' => 'cjwpbldr_gutenberg_block_editor_css',
					'render_callback' => [$this, 'renderBlockCallback'],
				]
			);
		}

		public function renderBlockCallback( $attributes ) {
			ob_start();
			$post_id = $attributes['selected_block'];
			$post_info = $this->helpers->postInfo( $post_id );
			$class = $post_info['_ui_block_class_name']::getInstance();
			echo $this->helpers->renderUiBlock( $post_info, $class->info );

			return ob_get_clean();
		}

	}

	cjwpbldr_gutenberg_blocks::getInstance();
}