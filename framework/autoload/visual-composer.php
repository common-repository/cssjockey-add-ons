<?php
if ( ! class_exists('cjwpbldr_vc_integration')) {
	class cjwpbldr_vc_integration
	{
		private static $instance;
		public $helpers;

		public static function getInstance()
		{
			if ( ! isset(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->generateBlockShortCodes();
			add_action('vc_after_init', array($this, 'visualComposerUiBlockMap'));
			add_action( 'vc_after_init', array($this, 'visualComposerShortcodesMap') );
		}

		public function generateBlockShortCodes()
		{
			$ui_blocks = get_posts(array(
				'post_type' => 'cjwpbldr-ui-blocks',
				'posts_per_page' => -1,
				'orderby' => 'post_date',
				'order' => 'DESC',
			));
			foreach ($ui_blocks as $key => $post) {
				$shortcode_name = 'cjvcuib_' . $post->ID;

				$callback = function ($atts, $content, $tag) {
					$block_id = str_replace('cjvcuib_', '', $tag);

					return do_shortcode("[cjwpbldr_block id='{$block_id}']");
				};

				add_shortcode($shortcode_name, $callback);
			}
		}

		public function visualComposerUiBlockMap()
		{
			$blocks_array = array('Select UI Block' => '');

			$ui_blocks = $this->helpers->getPosts(array(
				'post_type' => 'cjwpbldr-ui-blocks',
				'posts_per_page' => -1,
				'orderby' => 'post_date',
				'order' => 'DESC',
			));

			foreach ($ui_blocks as $key => $value) {
				$blocks_array[$value['post_title']] = $value['ID'];
				$class_name = $value['_ui_block_class_name'];

				if (class_exists($class_name)) {
					$shortcode_name = $shortcode_name = 'cjvcuib_' . $value['ID'];
					$class_instance = $class_name::getInstance();
					$screenshot = $class_instance->info['screenshot'];
					$description = array();
					$description[] = '<style>input[name="id"]{display:none;}</style>';
					$description[] = '<p>';
					$description[] = '<b>' . $value['post_title'] . '</b><br>';
					$description[] = $class_instance->info['description'] . '<br>';
					$description[] = '<a href="http://addons.wp/wp-admin/post.php?post=' . $value['ID'] . '&action=edit" target="_blank">' . __('Edit Block Settings', 'cssjockey-add-ons') . '</a>';
					$description[] = '</p>';
					$description[] = '<p>';
					$description[] = '<img src="' . $screenshot . '" style="width: 100%;" />';
					$description[] = '</p>';
					$settings = array(
						'icon' => $screenshot,
						'name' => $value['post_title'],
						'base' => $shortcode_name,
						'category' => __('WP Builder UI Blocks', 'cssjockey-add-ons'),
						'description' => $class_instance->info['description'],
						'show_settings_on_create' => true,
						'weight' => 0,
						'js_view' => 'cjwpbldr_block_js_view_' . $class_name,
						'params' => array(
							array(
								'type' => 'textfield',
								'heading' => '',
								'param_name' => 'id', //param_name for textarea_html must be named "content"
								'value' => $value['ID'],
								'description' => implode('', $description)
							),
						)
					);
					vc_map($settings);
				}
			}
		}

		public function visualComposerShortcodesMap()
		{

			global $shortcode_tags;
			$cjwpbldr_shortcodes = array();
			if (is_array($shortcode_tags) && ! empty($shortcode_tags)) {
				foreach ($shortcode_tags as $shortcode_key => $shortcode) {
					if (strpos($shortcode_key, 'cjwpbldr') === 0) {
						$shortcode_info = (array)$shortcode[0];
						$cjwpbldr_shortcodes[$shortcode_key . '_shortcode'] = $shortcode_info['defaults'];
					}
				}
			}
			if (isset($cjwpbldr_shortcodes['cjwpbldr_block_shortcode'])) {
				unset($cjwpbldr_shortcodes['cjwpbldr_block_shortcode']);
			}

			if (is_array($cjwpbldr_shortcodes)) {
				foreach ($cjwpbldr_shortcodes as $key => $shortcode) {

					$params = array();
					$hidden_default_content = (isset($shortcode['info']['default_content_type']) && $shortcode['info']['default_content_type'] == 'hidden') ? true : false;

					if ( ! $shortcode['info']['single'] && ! $hidden_default_content) {
						$params[] = array(
							"type" => "textarea_html",
							"holder" => "div",
							"class" => "",
							"heading" => __("Content", "cssjockey-add-ons"),
							"param_name" => "content", // Important: Only one textarea_html param per content element allowed and it should have "content" as a "param_name"
							"value" => $shortcode['info']['default_content'],
							"description" => ''
						);
					}

					foreach ($shortcode['options'] as $o_key => $option) {
						$field_type = $option['type'];
						$field_value = (is_array($option['options']) && ! empty($option['options'])) ? array_keys($option['options']) : $option['default'];
						if (in_array($option['type'], $this->helpers->inputTextFieldTypes())) {
							$field_type = 'textfield';
						}
						if ($option['type'] == 'textarea') {
							$field_type = 'textarea';
						}
						if ($option['type'] == 'file') {
							$field_type = 'attach_image';
						}
						if ($option['type'] == 'files') {
							$field_type = 'attach_images';
						}
						if ($option['type'] == 'color') {
							$field_type = 'colorpicker';
						}
						if (in_array($option['type'], array('checkbox', 'checkbox-inline', 'radio', 'radio-inline'))) {
							$field_type = 'dropdown';
						}
						$params[] = array(
							'type' => $field_type,
							'heading' => $option['label'],
							'param_name' => $option['id'],
							'description' => $option['info'],
							'value' => $field_value,
						);
					}

					$settings = array(

						'icon' => $this->helpers->root_url . '/assets/cssjockey/images/icon.svg',
						// icon

						'name' => $shortcode['info']['name'],
						// shortcode name

						'base' => $shortcode['info']['tag'],
						// shortcode base [cjwpbldr_block]

						'category' => __('WP Builder UI Blocks', 'cssjockey-add-ons'),
						// param category tab in add elements view

						'description' => $shortcode['info']['description'],
						// element description in add elements view

						'show_settings_on_create' => true,
						// don't show params window after adding

						'weight' => -5,
						// Depends on ordering in list, Higher weight first

						'js_view' => $shortcode['info']['tag'] . '_js_view',
						// JS View name for backend. Can be used to override or add some logic for shortcodes in backend (cloning/rendering/deleting/editing).

						'params' => $params
					);

					vc_map($settings);
				}
			}
		}

	}

	cjwpbldr_vc_integration::getInstance();
}