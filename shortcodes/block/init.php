<?php
/*
 * Class name should be cjwpbldr_NAME_shortcode
 * Shortcode Tag will be cjwpbldr_NAME
 * */
if( ! class_exists( 'cjwpbldr_block_shortcode' ) ) {
	class cjwpbldr_block_shortcode {

		public $defaults, $helpers, $render_forms, $textdomain, $shortcode_tag;

		private static $instance;

		public static function getInstance() {

			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->shortcode_tag = str_replace( '_shortcode', '', get_class( $this ) );
			add_shortcode( $this->shortcode_tag, array($this, 'run') );
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->textdomain = $this->helpers->config( 'plugin-info', 'textdomain' );
			$this->defaults = $this->defaults();
		}

		public function defaults() {

			$blocks_array = $this->helpers->getPosts( [
				'post_type' => 'cjwpbldr-ui-blocks',
				'post_status' => 'publish',
				'posts_per_page' => - 1,
			], 'ID', 'post_title' );

			$defaults['info'] = array(
				'single' => true,
				'tag' => $this->shortcode_tag,
				'name' => esc_attr__( 'UI Block', 'wp-builder-locale' ),
				'description' => esc_attr__( 'This shortcode will render the UI Block.', 'wp-builder-locale' ),
				'screenshot' => '',
				'default_content' => '',
				'group' => 'Core',
				'item_name' => $this->helpers->config( 'plugin-info', 'name' ),
				'preview' => true,
			);
			$defaults['options'] = array(
				array(
					'id' => 'id',
					'type' => 'select',
					'label' => 'id',
					'info' => __( 'Specify UI Block ID', 'wp-builder-locale' ),
					'default' => '',
					'options' => $blocks_array
				)
			);

			return $defaults;
		}

		public function run( $atts, $content = null ) {
			$defaults = array();
			$shortcode_params = $this->defaults();
			if( is_array( $shortcode_params['options'] ) && ! empty( $shortcode_params['options'] ) ) {
				foreach( $shortcode_params['options'] as $key => $param ) {
					$default_key = str_replace( '-', '_', $param['id'] );
					$default_value = (isset( $param['default'] ) && is_array( $param['default'] )) ? implode( '|', $param['default'] ) : $param['default'];
					$defaults[ $default_key ] = $default_value;
				}
			}
			$instance = ( ! is_array( $atts )) ? array() : $atts;
			$instance['content'] = $content;
			$output = '';
			$content_file_path = dirname( __FILE__ ) . '/content.php';
			if( file_exists( $content_file_path ) ) {
				ob_start();
				require($content_file_path);
				$output .= ob_get_clean();
				$output = apply_filters( 'cjwpbldr_shortcode_before_output', $output, $shortcode_params );
				$output = apply_filters( 'cjwpbldr_shortcode_after_output', $output, $shortcode_params );
			} else {
				$output .= '<div class="cj-notification cj-is-info">';
				$output .= sprintf( __( 'Shortcode content file not found.<br>%s', 'cjwpbldr' ), str_replace( dirname( dirname( __FILE__ ) ), '', $content_file_path ) );
				$output .= '</div>';
			}

			return $output;
		}

	}

	cjwpbldr_block_shortcode::getInstance();
}