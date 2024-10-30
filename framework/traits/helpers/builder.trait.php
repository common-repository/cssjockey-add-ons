<?php

/**
 * Trait cjwpbldr_helpers_builder
 */
trait cjwpbldr_helpers_builder
{

	/**
	 * @param        $key
	 * @param        $array
	 * @param string $default
	 *
	 * @return mixed|string
	 */
	public function getBlockValue($key, $array, $default = '')
	{
		$prefix = '_block_settings_';
		$key = $prefix . $key;

		if (is_array($array) && array_key_exists($key, $array)) {
			if ($array[$key] != '') {
				return $array[$key];
			} else {
				return $default;
			}
		} else {
			return $default;
		}
	}

	/**
	 * @param $options
	 * @param $block_info
	 *
	 * @return bool
	 */
	public function cmb2SaveDefaults($options, $block_info)
	{
		return false;
		$prefix = '_block_settings_';
		$group_fields_defaults = [];
		$defaults_to_save = [];
		if (is_array($options) && !empty($options)) {
			foreach ($options as $key => $option) {
				if (!$this->containString('_block_settings_', $option['id'])) {
					$option['id'] = $prefix . $option['id'];
				}
				$option['default'] = (isset($option['default'])) ? $option['default'] : '';
				if ($option['type'] !== 'group' && !isset($block_info[$option['id']])) {
					update_post_meta($block_info['ID'], $option['id'], $option['default']);
				}
				if ($option['type'] == 'group' && array_key_exists('group_fields_defaults', $option) && !empty($option['group_fields_defaults']) && isset($block_info[$option['id']])) {
					$this->updateUiBlockDefaults($block_info);
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	public function createUiBlocks()
	{
		$installed_ui_blocks = $this->installedUiBlocks();
		$available_ui_blocks_count = $this->dbCount('postmeta', 'meta_value', [
			'meta_key' => '_core_ui_block'
		]);

		if (count($installed_ui_blocks) == $available_ui_blocks_count) {
			return false;
		}

		$ui_block_posts = $this->dbSelect('postmeta', 'meta_value', [
			'meta_key' => '_ui_block_class_name'
		]);
		$new_ui_blocks = $installed_ui_blocks;
		$installed_ui_blocks_keys = array_keys($installed_ui_blocks);
		if (!empty($ui_block_posts)) {
			foreach ($ui_block_posts as $key => $ui_block_post) {
				if (in_array($ui_block_post['meta_value'], $installed_ui_blocks_keys)) {
					unset($new_ui_blocks[$ui_block_post['meta_value']]);
				}
			}
		}
		if (is_array($new_ui_blocks) && !empty($new_ui_blocks)) {
			foreach ($new_ui_blocks as $class_name => $install_new_ui_block) {
				$check_existing = $this->getPostsByMetaKey('_ui_block_class_name', $class_name);
				if (is_null($check_existing)) {
					$block_post_data = [
						'post_title' => $install_new_ui_block['name'],
						'post_content' => $install_new_ui_block['description'],
						'post_type' => 'cjwpbldr-ui-blocks',
						'post_status' => 'publish',
						'post_name' => sanitize_title($install_new_ui_block['id']),
						'comment_status' => 0,
						'ping_status' => 0,
						'_ui_block_class_name' => $install_new_ui_block['id'],
						'_core_ui_block' => 1,
					];
					$post_info = $this->createPost($block_post_data);
					if (is_array($post_info)) {
						do_action('cjwpbldr_ui_block_created', $post_info);
						if (is_array($post_info) && !empty($post_info) && isset($post_info['ID'])) {
							$groups = (is_array($install_new_ui_block['group']) && !empty($install_new_ui_block['group'])) ? $install_new_ui_block['group'] : ['MISC'];
							wp_set_object_terms($post_info['ID'], $groups, 'cjwpbldr-blocks-group');
						}
						$this->updateUiBlockDefaults($post_info);
						if (isset($post_info['ID'])) {
							$this->compileBlocksScss($post_info['ID']);
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param $template_id
	 *
	 * @return mixed
	 */
	public function syncTemplateBlocks($template_id)
	{

		$installed_blocks = $this->installedUiBlocks();

		$post_info = $this->postInfo($template_id, [], 'all');

		if (isset($post_info['_cjwpbldr_ui_blocks'])) {
			$ui_blocks = $post_info['_cjwpbldr_ui_blocks'];
			if (is_array($ui_blocks) && !empty($ui_blocks)) {
				foreach ($ui_blocks as $key => $block_data) {
					$block_post_info = $this->postInfo($block_data['ID']);
					if (empty($block_post_info)) {
						unset($ui_blocks[$key]);
					}
					if (!in_array($block_data['info']['class_name'], array_keys($installed_blocks))) {
						wp_delete_post($block_data["ID"], true);
						unset($ui_blocks[$key]);
					}
				}
			}
			update_post_meta($post_info['ID'], '_cjwpbldr_ui_blocks', $ui_blocks);
		}

		if (isset($post_info['_cjwpbldr_ui_blocks_preview'])) {
			$ui_blocks = $post_info['_cjwpbldr_ui_blocks_preview'];
			if (is_array($ui_blocks) && !empty($ui_blocks)) {
				foreach ($ui_blocks as $key => $block_data) {
					$block_post_info = $this->postInfo($block_data['ID']);
					if (empty($block_post_info)) {
						unset($ui_blocks[$key]);
					}
					if (!in_array($block_data['info']['class_name'], array_keys($installed_blocks))) {
						wp_delete_post($block_data["ID"], true);
						unset($ui_blocks[$key]);
					}
				}
			}
			update_post_meta($post_info['ID'], '_cjwpbldr_ui_blocks_preview', $ui_blocks);
		}
		$post_info = $this->postInfo($template_id, [], 'all');

		return $post_info;
	}

	/**
	 * @param array $post_info
	 *
	 * @return bool
	 */
	public function updateUiBlockDefaults($post_info = [])
	{
		$prefix = '_block_settings_';
		$class_name = (isset($post_info['_ui_block_class_name']) && class_exists($post_info['_ui_block_class_name'])) ? $post_info['_ui_block_class_name'] : '';
		if ($class_name == '') {
			return false;
		}
		$class_instance = $class_name::getInstance();
		$class_info = $class_instance->info;
		$block_groups = implode(' | ', $class_info['group']);
		$block_content = " {$class_info['name']} - {$class_info['description']} - {$block_groups}";

		$this->updatePostInfo($post_info['ID'], [
			'post_content' => $block_content
		]);

		$options = $class_info['options'];
		$core_block_settings = [
			'_block_settings_block_visibility' => ['everyone'],
			'_block_settings_block_visibility_message' => 'This block is only visible to certain roles.',
			'_block_settings_wrap_in_container' => 'cj-container',
			'_block_settings_custom_css' => '',
			'_block_settings_custom_js' => '',
		];

		$core_block_preview_settings = [
			'_block_settings_block_visibility_preview' => ['everyone'],
			'_block_settings_block_visibility_message_preview' => 'This block is only visible to certain roles.',
			'_block_settings_wrap_in_container_preview' => 'cj-container',
			'_block_settings_custom_css_preview' => '',
			'_block_settings_custom_js_preview' => '',
		];

		foreach ($core_block_settings as $key => $block_settings) {
			update_post_meta($post_info['ID'], $key, $block_settings);
		}

		foreach ($core_block_preview_settings as $key => $block_preview_setting) {
			if (!get_post_meta($post_info['ID'], $key, true)) {
				update_post_meta($post_info['ID'], $key, $block_preview_setting);
			}
		}

		if (is_array($options) && !empty($options)) {
			foreach ($options as $key => $option) {
				if ($option['type'] != 'group') {

					// Original Block Settings
					if (!$this->containString('_block_settings_', $option['id'])) {
						$option['id'] = $prefix . $option['id'];
					}
					$option['default'] = (isset($option['default'])) ? $option['default'] : '';
					if (isset($option['repeatable']) && $option['default'] == '') {
						$option['default'] = [];
					}
					update_post_meta($post_info['ID'], $option['id'], $option['default']);

					// Preview Block Settings
					if (!$this->containString('_preview', $option['id']) && $this->containString('_block_settings_', $option['id'])) {
						$preview_option_id = $option['id'] . '_preview';
					} else if (!$this->containString('_preview', $option['id']) && !$this->containString('_block_settings_', $option['id'])) {
						$preview_option_id = $prefix . $option['id'] . '_preview';
					}
					$preview_option_default = (isset($option['default'])) ? $option['default'] : '';
					if (isset($option['repeatable']) && $option['default'] == '') {
						$preview_option_default = [];
					}
					update_post_meta($post_info['ID'], $preview_option_id, $preview_option_default);
				}
				if ($option['type'] == 'group') {
					$group_field_defaults = ($this->isValidatedArray($option, true, 'group_fields_defaults')) ? $option['group_fields_defaults'] : [];
					if (!empty($group_field_defaults)) {
						$group_defaults = [];
						foreach ($group_field_defaults as $group_key => $group_values) {
							foreach ($group_values as $id_key => $values) {
								if (!is_array($values)) {
									$group_defaults[$group_key][$id_key] = $values;
								} else {
									foreach ($values as $item_key => $item_value) {
										$group_defaults[$group_key][$id_key][$item_key] = $item_value;
									}
								}
							}
						}
						$meta_key = '_block_settings_' . $option['id'];
						$preview_meta_key = '_block_settings_' . $option['id'] . '_preview';
						if (!get_post_meta($post_info['ID'], $preview_meta_key, true)) {
							update_post_meta($post_info['ID'], $preview_meta_key, $group_defaults);
						}
						update_post_meta($post_info['ID'], $meta_key, $group_defaults);
					}
				}
			}
			//die;
		}
		wp_set_object_terms($post_info['ID'], $class_instance->info['group'], 'cjwpbldr-blocks-group');
	}

	/**
	 * @param string $group
	 * @param array  $query_vars
	 *
	 * @return array
	 */
	public function savedUiBlocks($group = '', $query_vars = [])
	{
		$return = [];
		$args['post_type'] = 'cjwpbldr-ui-blocks';
		$args['numberposts'] = -1;
		if ($group != '') {
			$args['tax_query'] = [
				[
					'taxonomy' => 'cjwpbldr-blocks-group',
					'field' => 'slug',
					'terms' => [$group],
				]
			];
		}
		if (!empty($query_vars)) {
			foreach ($query_vars as $key => $value) {
				$args[$key] = $value;
			}
		}
		$blocks = $this->getPosts($args);
		if (is_array($blocks) && !empty($blocks)) {
			foreach ($blocks as $key => $block) {
				$return[] = $block;
			}
		}

		return $return;
	}

	/**
	 * @param $class_name
	 *
	 * @return array
	 */
	public function getBlockInfo($class_name)
	{
		$return = [];
		if (class_exists($class_name)) {
			$class_instance = $class_name::getInstance();
			$return = $class_instance->info();
		}

		return $return;
	}

	/**
	 * @return array
	 */
	public function uiBlockGroups()
	{
		$blocks = $this->installedUiBlocks();
		$groups_array = [];
		if (is_array($blocks) && !empty($blocks)) {
			foreach ($blocks as $key => $block) {
				$block_info = $block;
				unset($block_info['options']);
				unset($block_info['path']);
				unset($block_info['class_name']);
				foreach ($block['group'] as $group_key => $group) {
					$groups_array[$group]['group_name'] = $group;
					$groups_array[$group]['blocks'][] = $block_info;
				}
			}
		}

		return $groups_array;
	}

	/**
	 * @param $block_info
	 *
	 * @return array
	 */
	public function getBlockFormOptions($block_info)
	{
		$block_class = $block_info['_ui_block_class_name'];
		$block_class = $block_class::getInstance();
		$block_class_info = $block_class->info;
		$block_form_options = $block_class->info['options'];

		$block_core_form_options[] = [
			'name' => __('Visibility', 'wp-builder-locale'),
			'id' => 'block_visibility',
			'type' => 'roles',
			'description' => __('NOTE: If <strong>Visitors Only</strong> option is selected, it would ignore other options and show block only to non-logged in users only.', 'wp-builder-locale'),
			'default' => ['everyone'],
			'options' => [],
			'form_tab' => 'core-settings',
		];

		$block_core_form_options[] = [
			'name' => __('Flexible or Boxed?', 'wp-builder-locale'),
			'id' => 'wrap_in_container',
			'type' => 'select',
			'description' => __('Boxed: If boxed, the content of this block will be wrapped in a container otherwise it will be flexible to fit in the parent element.', 'wp-builder-locale'),
			'default' => 'cj-container',
			'options' => [
				'cj-container' => __('Boxed', 'lang-text-domain'),
				'cj-container cj-is-fluid' => __('Flexible', 'lang-text-domain'),
				'cj-container-none' => __('None', 'lang-text-domain')
			],
			'form_tab' => 'core-settings',
		];

		$block_core_form_options[] = [
			'name' => __('Custom CSS', 'wp-builder-locale'),
			'id' => 'custom_css',
			'type' => 'code-css',
			'default' => '',
			'description' => __('Specify custom CSS code for this UI Block. e.g.<br><code>' . sprintf(".%s{...}" . '</code>', $block_info['_ui_block_class_name']) . '', 'wp-builder-locale'),
			'form_tab' => 'custom-settings',
		];

		$block_core_form_options[] = [
			'name' => __('Custom Javascript', 'wp-builder-locale'),
			'id' => 'custom_js',
			'type' => 'code-js',
			'default' => '',
			'description' => __('Specify custom Javascript code for this UI Block. e.g.<br>', 'wp-builder-locale') . sprintf('<code>$(".%s .element").callback();</code>', $block_info['_ui_block_class_name']),
			'form_tab' => 'custom-settings',
		];

		foreach ($block_form_options as $key => $value) {
			if (!$this->containString('_block_settings_', $value['id'])) {
				$block_form_options[$key]['id'] = '_block_settings_' . $block_form_options[$key]['id'];
			}
		}

		foreach ($block_core_form_options as $key => $value) {
			if (!$this->containString('_block_settings_', $value['id'])) {
				$block_core_form_options[$key]['id'] = '_block_settings_' . $block_core_form_options[$key]['id'];
			}
		}

		$return = array_merge($block_form_options, $block_core_form_options);

		return $return;
	}

	/**
	 * @param $block_info
	 * @param $class_info
	 *
	 * @return string
	 */
	public function renderUiBlock($block_info, $class_info)
	{
		$block_info['block_url'] = str_replace(ABSPATH, site_url('/'), $class_info['path']);
		$content_file_path = sprintf('%s/content.php', $class_info['path']);

		if (file_exists($content_file_path)) {
			$unique_id = $this->uniqueString('ui-block-');
			if (isset($block_info['_block_settings_custom_css']) && $block_info['_block_settings_custom_css'] != '') {
				$custom_css = '<style type="text/css">';
				$custom_css .= strip_tags($block_info['_block_settings_custom_css']);
				$custom_css .= '</style>';
				add_action('wp_footer', function () use ($custom_css) {
					echo $custom_css;
				}, 999999);
			}
			if (isset($block_info['_block_settings_custom_js']) && $block_info['_block_settings_custom_js'] != '') {
				$custom_js = '<script type="text/javascript">';
				$custom_js .= strip_tags($block_info['_block_settings_custom_js']);
				$custom_js .= '</script>';
				add_action('wp_footer', function () use ($custom_js) {
					echo $custom_js;
				}, 999999);
			}

			$edit_link = '';
			$preview_div_start = '';
			$preview_div_end = '';
			$preview_iframe = '';

			$block_css_js = [];

			/**
			 * @todo admin@cssjockey.com
			 * Below piece of code was rendering the JS and CSS code blocks inside <body> twice
			 */
			if (isset($block_info['inline-editing']) && $block_info['inline-editing'] == true) {
				//$preview_div_start = '<div id="cjwpbldr-ui-block-preview-' . $block_info['ID'] . '">';
				//$preview_div_end = '</div>';
				if (isset($block_info['_block_js']) && $block_info['_block_js'] != '') {
					$block_css_js[] = '<script type="text/javascript">';
					$block_css_js[] = $block_info['_block_js'];
					$block_css_js[] = '</script>';
				}
				if (isset($block_info['_block_css']) && $block_info['_block_css'] != '') {
					$block_css_js[] = '<style type="text/css">';
					$block_css_js[] = $block_info['_block_css'];
					$block_css_js[] = '</style>';
				}
			}
			$screenshot_link = '';
			add_filter('cjwpbldr_block_used', function ($array) use ($block_info) {
				if (is_array($block_info) && !empty($block_info) && isset($block_info['ID'])) {
					$array[] = $block_info['ID'];
				}

				return $array;
			});
			ob_start();
			require $content_file_path;

			$classes = 'cssjockey-ui cjwpbldr-ui-block cjwpbldr-ui-block-' . $block_info['ID'] . '  cjwpbldr-ui-block-outer  ' . $class_info['class_name'] . ' ' . implode(' cjwpbldr-group-', $class_info['group']);
			$classes = strtolower(str_replace('_', '-', $classes));

			$block_content[] = $preview_div_start;
			$block_content[] = '<!-- ' . $class_info['class_name'] . ' -->';
			$block_content[] = '<div id="' . $unique_id . '" data-block-id="' . $block_info['ID'] . '" class=" cj-relative ' . $classes . '">';
			$block_content[] = $edit_link;
			$block_content[] = $screenshot_link;
			$block_content[] = ob_get_clean();
			$block_content[] = '</div>';
			$block_content[] = '<!-- /' . $class_info['class_name'] . ' -->';
			$block_content[] = $preview_iframe;
			$block_content[] = $preview_div_end;
			$block_content[] = implode('', $block_css_js);

			return implode('', $block_content);
		}

		return '';
	}

	/**
	 * @param      $block_id
	 * @param bool $inline_editing
	 *
	 * @return false|string
	 */
	public function renderUiBlockPreview($block_id, $inline_editing = true)
	{
		$block_info = $this->postInfo($block_id);
		$block_class = $block_info['_ui_block_class_name'];
		$block_class = $block_class::getInstance();
		$block_class_info = $block_class->info;
		$new_block_info = $block_info;
		foreach ($block_info as $key => $value) {
			if ($this->containString('_block_settings_', $key) && !$this->containString('_preview', $key)) {
				$new_block_info[$key] = (isset($block_info[$key . '_preview'])) ? $block_info[$key . '_preview'] : $block_info[$key];
			}
		}

		ob_start();
		// enable block inline editing
		$new_block_info['inline-editing'] = $inline_editing;
		echo $this->renderUiBlock($new_block_info, $block_class_info);
		$return = ob_get_clean();

		return $return;
	}

	/**
	 * @param        $dir_path
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function blockPreviewImage($dir_path, $type = 'screenshot')
	{
		$file_path = $dir_path . '/' . $type . '.jpg';
		if (file_exists($file_path)) {
			return str_replace(ABSPATH, site_url('/'), $file_path);
		}
		$file_path = str_replace('.jpg', '.png', $file_path);

		return str_replace(ABSPATH, site_url('/'), $file_path);
	}

	/**
	 * @param string $type
	 * @param array  $block_info
	 * @param string $container_custom_class
	 * @param string $container_style
	 *
	 * @return string
	 */
	public function blockContainer($type = 'open', $block_info = [], $container_custom_class = '', $container_style = '')
	{
		if ($container_custom_class != '') {
			$container_custom_class = ' ' . $container_custom_class;
		}
		if ($container_style != '') {
			$container_style = ' ' . $container_style;
		}

		$key = (isset($block_info['_block_has_preview']) && $block_info['_block_has_preview']) ? '_block_settings_wrap_in_container_preview' : '_block_settings_wrap_in_container';
		$container_class = $block_info[$key];
		if ($container_class == 'cj-container-none') {
			return '';
		}

		if ($type == 'open') {
			return '<div class="' . $container_class . $container_custom_class . '"' . $container_style . '>';
		}
		if ($type != 'open') {
			return '</div><!-- /container -->';
		}
	}

	/**
	 * @param $original_block_id
	 *
	 * @return mixed
	 */
	public function resetUiBlock($original_block_id)
	{
		$post_info = $this->postInfo($original_block_id);
		foreach ($post_info as $key => $value) {
			if ($this->containString('_block_settings_', $key)) {
				delete_post_meta($post_info['ID'], $key);
			}
			$preview_key = '_block_settings_' . $key . '_preview';
			if ($this->containString('_preview', $key)) {
				delete_post_meta($post_info['ID'], $preview_key);
			}
		}
		$this->updateUiBlockDefaults($post_info);
		if (isset($post_info['ID'])) {
			$this->compileBlocksScss($post_info['ID']);
		}

		$post_info = $this->postInfo($original_block_id);

		return $post_info;
	}

	/**
	 * @param $original_block_id
	 *
	 * @return mixed
	 */
	public function cloneUiBlock($original_block_id)
	{
		$post_info = $this->postInfo($original_block_id);
		unset($post_info['ID']);
		unset($post_info['post_name']);
		unset($post_info['post_date']);
		unset($post_info['post_date_gmt']);
		unset($post_info['post_modified']);
		unset($post_info['post_modified_gmt']);
		unset($post_info['_core_ui_block']);
		$post_info['post_title'] = $post_info['post_title'] . ' - ' . __('Copy', 'wp-builder-locale');
		$new_block_id = wp_insert_post($post_info);
		$this->updatePostInfo($new_block_id, $post_info);
		$taxonomies = [];
		if (isset($post_info['taxonomies']['cjwpbldr-blocks-group']) && is_array($post_info['taxonomies']['cjwpbldr-blocks-group'])) {
			$groups = $post_info['taxonomies']['cjwpbldr-blocks-group'];
			foreach ($groups as $group_key => $group) {
				$taxonomies[] = $group['term_id'];
			}
		}
		wp_set_object_terms($new_block_id, $taxonomies, 'cjwpbldr-blocks-group');
		$new_block_info = $this->postInfo($new_block_id);

		return $new_block_info;
	}

	/**
	 * @return int|mixed
	 */
	public function getTemplateId()
	{
		$queried_object = get_queried_object();

		if (is_feed()) {
			return 0;
		}

		if (is_front_page() && is_home()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_blog_page');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		} elseif (is_front_page()) {
			if (isset($queried_object->post_type) && $queried_object->post_type == 'page') {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_homepage');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		} elseif (is_home()) {
			if (isset($queried_object->post_type) && $queried_object->post_type == 'page') {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_blog_page');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_singular('attachment')) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_attachment');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_singular('post')) {
			if (isset($queried_object->post_type) && $queried_object->post_type == 'post') {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}

			$categories = wp_get_post_categories($queried_object->ID);
			if (!empty($categories)) {
				foreach ($categories as $cat_key => $cat_term) {
					$term_meta = get_term_meta($cat_term);
					if (isset($term_meta['_cjwpbldr_use_template']) && isset($term_meta['_cjwpbldr_use_template'][0])) {
						return $term_meta['_cjwpbldr_use_template'][0];
					}
				}
			}

			$tags = wp_get_post_tags($queried_object->ID);
			if (!empty($tags)) {
				foreach ($tags as $tag_key => $tag_term) {
					$term_meta = get_term_meta($tag_term);
					if (isset($term_meta['_cjwpbldr_use_template']) && isset($term_meta['_cjwpbldr_use_template'][0])) {
						return $term_meta['_cjwpbldr_use_template'][0];
					}
				}
			}

			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_all_posts');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_singular('page')) {
			if (isset($queried_object->post_type) && $queried_object->post_type == 'page') {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_all_pages');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_singular()) {
			if (isset($queried_object->post_type) && !in_array($queried_object->post_type, ['page', 'post', 'attachment'])) {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_cpt_' . $queried_object->post_type . '_singular');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_post_type_archive()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_cpt_' . $queried_object->name . '_archive');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_author()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_author');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_archive()) {
			if (isset($queried_object->term_id)) {
				$saved_template = get_term_meta($queried_object->term_id, '_cjwpbldr_use_template', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			if (isset($queried_object->taxonomy)) {
				$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_tax_' . $queried_object->taxonomy);
				if (!empty($saved_template)) {
					return $saved_template['ID'];
				}
			}
		}

		if (is_date() || is_year() || is_month() || is_day() || is_time() || is_new_day() || is_time()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_archives');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_search()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_search_page');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_404()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_404_page');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		$entire_website_template = $this->getPostByMetaKey('_cjwpbldr_use_template_entire_website', 'on', 'data');
		if (!empty($entire_website_template)) {
			return $entire_website_template['ID'];
		}

		return 0;
	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	public function getTemplateInfoByPostId($post_id)
	{
		$post_info = $this->postInfo($post_id);
		$home_page_id = get_option('page_on_front');
		$blog_page_id = get_option('page_for_posts');
		if ($home_page_id == $post_info['ID']) {
			return $this->getPostByMetaKey('_cjwpbldr_use_template_homepage');
		} else if ($blog_page_id == $post_info['ID']) {
			return $this->getPostByMetaKey('_cjwpbldr_use_template_blog_page');
		} else {
			$single_template_id = get_post_meta($post_info['ID'], '_cjwpbldr_use_template_singular', true);
			if ($single_template_id != '') {
				$single_template = $this->postInfo($single_template_id);
				if (!empty($single_template)) {
					return $single_template;
				}
			}
		}

		return [];
	}

	/**
	 * @return bool|int|mixed|string
	 */
	public function getTemplateType()
	{
		return false;
		$queried_object = get_queried_object();

		if (is_feed()) {
			return 'feed';
		}

		if (is_front_page() && is_home()) {
			return '_cjwpbldr_use_template_blog_page';
		} elseif (is_front_page()) {
			return '_cjwpbldr_use_template_homepage';
		} elseif (is_home()) {
			return '_cjwpbldr_use_template_blog_page';
		}

		if (is_singular('attachment')) {
			return '_cjwpbldr_use_template_attachment';
		}

		if (is_singular('post')) {
			if (isset($queried_object->post_type) && $queried_object->post_type == 'post') {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}

			$categories = wp_get_post_categories($queried_object->ID);
			if (!empty($categories)) {
				foreach ($categories as $cat_key => $cat_term) {
					$term_meta = get_term_meta($cat_term);
					if (isset($term_meta['_cjwpbldr_use_template']) && isset($term_meta['_cjwpbldr_use_template'][0])) {
						return $term_meta['_cjwpbldr_use_template'][0];
					}
				}
			}

			$tags = wp_get_post_tags($queried_object->ID);
			if (!empty($tags)) {
				foreach ($tags as $tag_key => $tag_term) {
					$term_meta = get_term_meta($tag_term);
					if (isset($term_meta['_cjwpbldr_use_template']) && isset($term_meta['_cjwpbldr_use_template'][0])) {
						return $term_meta['_cjwpbldr_use_template'][0];
					}
				}
			}

			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_all_posts');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_singular('page')) {
			if (isset($queried_object->post_type) && $queried_object->post_type == 'page') {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_all_pages');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_singular()) {
			if (isset($queried_object->post_type) && !in_array($queried_object->post_type, ['page', 'post', 'attachment'])) {
				$saved_template = get_post_meta($queried_object->ID, '_cjwpbldr_use_template_singular', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_cpt_' . $queried_object->post_type . '_singular');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_post_type_archive()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_cpt_' . $queried_object->name . '_archive');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_author()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_author');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_archive()) {
			if (isset($queried_object->term_id)) {
				$saved_template = get_term_meta($queried_object->term_id, '_cjwpbldr_use_template', true);
				if ($saved_template != '') {
					return $saved_template;
				}
			}
			if (isset($queried_object->taxonomy)) {
				$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_tax_' . $queried_object->taxonomy);
				if (!empty($saved_template)) {
					return $saved_template['ID'];
				}
			}
		}

		if (is_date() || is_year() || is_month() || is_day() || is_time() || is_new_day() || is_time()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_archives');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_search()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_search_page');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		if (is_404()) {
			$saved_template = $this->getPostByMetaKey('_cjwpbldr_use_template_404_page');
			if (!empty($saved_template)) {
				return $saved_template['ID'];
			}
		}

		return 0;
	}

	/**
	 * @param null $var
	 *
	 * @return array|mixed
	 */
	public function customTemplatesArray($var = null)
	{
		$return = [
			'' => __('None', 'wp-builder-locale'),
		];
		$templates = get_posts([
			'posts_per_page' => -1,
			'post_type' => 'cjwpbldr-templates',
			'orderBy' => 'post_title',
			'order' => 'ASC',
		]);
		if (!empty($templates)) {
			foreach ($templates as $template) {
				$return[$template->ID] = $template->post_title;
			}
		}

		return (is_null($var)) ? $return : $return[$var];
	}

	/**
	 * @param $block_info
	 * @param $option_id
	 *
	 * @return array
	 */
	public function getBackground($block_info, $option_id)
	{
		$backgrounds = [];
		$prefix = '_block_settings_';
		$option_name = $prefix . $option_id;

		$option_value = $this->getArrayValue($option_name, $block_info, '');

		if (is_array($option_value)) {

			if (array_key_exists('overlay', $option_value) && $option_value['overlay'] != 'none') {

				$backgrounds['overlay_color'] = $option_value['overlay'];
				$backgrounds['overlay_opacity'] = $option_value['opacity'];
			}

			if (array_key_exists('text_color', $option_value) && $option_value['text_color'] != 'none') {

				$backgrounds['text_color'] = $option_value['text_color'];
			}

			if (array_key_exists('headings_color', $option_value) && $option_value['headings_color'] != 'none') {

				$backgrounds['headings_color'] = $option_value['headings_color'];
			}

			if (array_key_exists('links_color', $option_value) && $option_value['links_color'] != 'none') {

				$backgrounds['links_color'] = $option_value['links_color'];
			}

			if (array_key_exists('foreground_color', $option_value) && $option_value['foreground_color'] != 'none') {

				$backgrounds['foreground_color'] = $option_value['foreground_color'];
			}

			if (array_key_exists('image', $option_value) && $option_value['image'] != '') {
				$backgrounds['image'] = $option_value['image'];

				return $backgrounds;
			}

			//Return gradient only when all 3 values of gradient is present
			if (isset($option_value['gradient_start']) && $option_value['gradient_start'] != '' && $option_value['gradient_end'] != '' && $option_value['gradient_degree'] != '') {

				$start = $option_value['gradient_start'];
				$end = $option_value['gradient_end'];
				$degree = $option_value['gradient_degree'];

				$backgrounds['gradient'] = 'background-image: linear-gradient(' . $degree . 'deg, ' . $start . ', ' . $end . ');';

				return $backgrounds;
			}

			if (array_key_exists('background_color', $option_value) && $option_value['background_color'] != 'none') {

				$backgrounds['color'] = $option_value['background_color'];

				return $backgrounds;
			}

			return $backgrounds;
		}

		return [];
	}

	/**
	 * @return array
	 */
	public function assignTemplateArray()
	{
		$return = [];
		$return = [
			"_cjwpbldr_use_template_entire_website" => __('Entire Website', 'wp-builder-locale'),
			"_cjwpbldr_use_template_homepage" => __('Custom Homepage', 'wp-builder-locale'),
			"_cjwpbldr_use_template_blog_page" => __('Posts Page', 'wp-builder-locale'),
			"_cjwpbldr_use_template_archives" => __('Archives Pages', 'wp-builder-locale'),
			"_cjwpbldr_use_template_author" => __('Author Pages', 'wp-builder-locale'),
			"_cjwpbldr_use_template_all_pages" => __('Single Pages', 'wp-builder-locale'),
			"_cjwpbldr_use_template_all_posts" => __('Single Posts', 'wp-builder-locale'),
			"_cjwpbldr_use_template_attachment" => __('Media Page', 'wp-builder-locale'),
			"_cjwpbldr_use_template_search_page" => __('Search Page', 'wp-builder-locale'),
			"_cjwpbldr_use_template_404_page" => __('404 Page', 'wp-builder-locale'),
		];
		// taxonomies

		$taxonomies = get_taxonomies([
			'public' => true
		]);
		$exclude_taxonomies = ['post_format'];
		if (!empty($taxonomies)) {
			foreach ($taxonomies as $t_key => $taxonomy_name) {
				$taxonomy_object = get_taxonomy($taxonomy_name);
				if ($taxonomy_object->show_ui && $taxonomy_object->publicly_queryable && !in_array($taxonomy_name, $exclude_taxonomies)) {
					$return["_cjwpbldr_use_template_tax_{$taxonomy_object->name}"] = $taxonomy_object->label;
				}
			}
		}

		// post types
		$exclude_post_types = ['post', 'attachment'];
		$registered_post_types = get_post_types([
			'public' => true,
			'publicly_queryable' => true,
		]);
		if (!empty($registered_post_types)) {
			foreach ($registered_post_types as $key => $post_type_name) {
				if (!in_array($post_type_name, $exclude_post_types)) {
					$post_type_object = get_post_type_object($post_type_name);
					$return["_cjwpbldr_use_template_cpt_{$post_type_object->name}_archive"] = sprintf(__('%s Archives', 'wp-builder-locale'), $post_type_object->labels->name);
					$return["_cjwpbldr_use_template_cpt_{$post_type_object->name}_singular"] = sprintf(__('%s Single %ss', 'wp-builder-locale'), $post_type_object->labels->singular_name, ucwords($post_type_object->capability_type));
				}
			}
		}

		return $return;
	}

	/**
	 * @return string
	 */
	public function noTemplateAdminMessage()
	{
		global $wp_query;
		$msg[] = '<div class="cj-notification cj-is-warning cj-text-center cj-br-0">' . __('<b>Admin Notice:</b><br>Please select template for this section.', 'wp-builder-locale') . '</div>';
		$msg[] = '<div class="cj-columns cj-is-mobile cj-is-centered cj-is-gapless"><div class="cj-column cj-is-8-fullhd cj-is-8-widescreen cj-is-10-desktop cj-is-12-tablet cj-is-12-mobile">';
		$msg[] = $this->getTemplateType();
		$msg[] = '</div></div>';

		return implode('', $msg);
	}

	/**
	 * @param       $block_info
	 * @param array $option_keys
	 *
	 * @return string
	 */
	public function blockInlineEdit($block_info, $option_keys = [])
	{
		$inline_block_editor = false;
		if (current_user_can('manage_options')) {
			$inline_block_editor = true;
		}

		if (!isset($block_info['inline-editing'])) {
			return '';
		}

		if (isset($block_info['inline-editing']) && $block_info['inline-editing'] == false) {
			return '';
		}

		if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'preview-ui-block') {
			return '';
		}

		$option_keys = (is_array($option_keys)) ? implode('|&#43;|', $option_keys) : $option_keys;
		$option_keys = esc_html($option_keys);
		$option_keys_info = str_replace('_', '-', $option_keys);

		return " data-block-id='{$block_info['ID']}' data-edit-block-settings={$option_keys} data-settings-tooltip={$option_keys_info} ";
	}

	/**
	 * @return bool
	 */
	public function cmb2RenderFields()
	{
		return false;
	}

	public function publishTemplateBlocksPreview($template_id)
	{
		$template_info = $this->postInfo($template_id);
		$template_blocks = $template_info['_cjwpbldr_ui_blocks'];
		if (!empty($template_blocks)) {
			foreach ($template_blocks as $key => $block_data) {
				$block_post_info = $this->postInfo($block_data['ID']);
				foreach ($block_post_info as $key => $value) {
					if ($this->containString('_block_settings_', $key) && $this->containString('_preview', $key)) {
						$preview_key = $key;
						$real_key = str_replace('_preview', '', $key);
						$preview_value = $value;
						update_post_meta($block_post_info['ID'], $real_key, $value);
					}
				}
			}
		}

		return true;
	}
}
