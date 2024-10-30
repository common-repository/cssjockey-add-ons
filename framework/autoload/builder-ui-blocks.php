<?php
if (!class_exists('cjwpbldr_builder_ui_blocks')) {
	class cjwpbldr_builder_ui_blocks
	{
		private static $instance;
		public $helpers;

		public static function getInstance()
		{
			if (!isset(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			$this->helpers = cjwpbldr_helpers::getInstance();
			$core_features = $this->helpers->getOption('core_features');
			if ($this->helpers->inArray(['blocks', 'builder'], $core_features)) {
				add_action('admin_init', [$this, 'initialSetup']);
				add_action('admin_init', [$this, 'createUiBlocks']);
				add_action('admin_init', [$this, 'clearUiBlocks']);
				add_action('admin_init', [$this, 'cloneUiBlock']);
				add_action('wp_enqueue_scripts', [$this, 'removeThemeAssets'], 99);
				add_action('save_post', [$this, 'updateUiBlockSettings'], 9999);
				add_filter('post_row_actions', [$this, 'editPostAdminLinks'], 10, 2);
				add_action('load-post.php', [$this, 'uiBlockPreviewMetaboxInit']);
				add_action('load-post-new.php', [$this, 'disableAddingNewBlocks']);
				add_shortcode('cjwpbldr_block', [$this, 'blockShortcode']);
				add_action('current_screen', [$this, 'disableAddNewBlockUi']);
				add_filter('manage_cjwpbldr-ui-blocks_posts_columns', [$this, 'customEditPostColumns'], 10, 1);
				add_action('manage_cjwpbldr-ui-blocks_posts_custom_column', [$this, 'customEditPostColumnsContent'], 10, 2);
				add_action('wp_enqueue_scripts', [$this, 'enqueueUiBlockCss'], 99998);
				add_action('wp_enqueue_scripts', [$this, 'enqueueUiBlockJs'], 99999);
				add_filter('query_vars', [$this, 'addTrigger']);
				add_action('init', [$this, 'addTaxonomyTemplateMetaBox']);
				add_action('admin_menu', [$this, 'hideAddNewFromSubMenu']);
				add_action('wp_before_admin_bar_render', [$this, 'removeAddNewBlockAdminBarLink']);
				add_action('init', [$this, 'loadUiBlocks']);
				add_action('wp_footer', [$this, 'editBlockVueApp']);

				add_action('template_redirect', [$this, 'showPreviews']);
				add_action('template_redirect', [$this, 'blockUrlCallbacks'], 1);
				add_action('template_redirect', [$this, 'parseTrigger']);

				add_action('current_screen', [$this, 'loadUiBlockEditor']);
			}
		}

		public function loadUiBlockEditor()
		{
			$current_screen = get_current_screen();
			if ($current_screen->post_type == 'cjwpbldr-ui-blocks' && $current_screen->base == 'post' && isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
				$template_editor_url = site_url("?cjwpbldr-action=edit-ui-block&id={$_GET['post']}&settings=show");
				wp_redirect($template_editor_url);
				exit;
			}
		}

		public function editBlockVueApp()
		{
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'edit-ui-block') {
				echo '<div class="cssjockey-ui">';
				echo '<div id="cj-edit-block-form"><edit-block-form></edit-block-form></div>';
				echo '</div>';
			}

			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'edit-template') {
				echo '<div class="cssjockey-ui">';
				echo '<div id="cj-edit-block-form"><edit-block-form></edit-block-form></div>';
				echo '</div>';
			}
		}

		public function initialSetup()
		{
			$preview_posts = $this->helpers->dbSelect('posts', 'ID', [
				'post_status' => 'template-preview'
			]);
			if (!empty($preview_posts)) {
				foreach ($preview_posts as $key => $post) {
					wp_delete_post($post['ID'], true);
				}
			}
		}

		public function loadUiBlocks()
		{
			/* in-built ui blocks */
			$directory_path = sprintf('%s/ui-blocks', $this->helpers->root_dir);
			if (is_dir($directory_path)) {
				$directories = preg_grep('/^([^.])/', scandir($directory_path));
				if (is_array($directories) && !empty($directories)) {
					foreach ($directories as $key => $dir_name) {
						$file_path = sprintf('%s/%s/init.php', $directory_path, $dir_name);
						if (file_exists($file_path)) {
							$this->enableLocalisation($file_path);
							require_once $file_path;
						}
					}
				}
			}

			/* modules ui blocks */
			$directory_path = sprintf('%s/modules/', $this->helpers->root_dir);
			if (is_dir($directory_path)) {
				$directories = preg_grep('/^([^.])/', scandir($directory_path));
				if (is_array($directories) && !empty($directories)) {
					foreach ($directories as $addon_dir_key => $addon_dir_name) {
						$addon_dir = $directory_path . $addon_dir_name;
						if (is_dir($addon_dir)) {
							$ui_blocks_directories_path = $addon_dir . '/ui-blocks/';
							$ui_blocks_directories = is_dir($ui_blocks_directories_path) ? preg_grep('/^([^.])/', scandir($ui_blocks_directories_path)) : [];
							if (is_array($ui_blocks_directories) && !empty($ui_blocks_directories)) {
								foreach ($ui_blocks_directories as $addon_ui_block_dir_key => $addon_ui_block_dir_name) {
									$ui_blocks_dir_path = $ui_blocks_directories_path . $addon_ui_block_dir_name;
									if (is_dir($ui_blocks_dir_path) && file_exists($ui_blocks_dir_path . '/init.php')) {
										$file_path = $ui_blocks_dir_path . '/init.php';
										$this->enableLocalisation($file_path);
										require_once $file_path;
									}
								}
							}
						}
					}
				}
			}

			/* wp-content ui blocks */
			$directory_path = sprintf('%s/blocks', $this->helpers->cjwpbldr_content_path);
			if (is_dir($directory_path)) {
				$directories = preg_grep('/^([^.])/', scandir($directory_path));
				if (is_array($directories) && !empty($directories)) {
					foreach ($directories as $key => $dir_name) {
						$file_path = sprintf('%s/%s/init.php', $directory_path, $dir_name);
						if (file_exists($file_path)) {
							$this->enableLocalisation($file_path);
							require_once $file_path;
						}
					}
				}
			}

			/* plugins/addons ui-blocks */
			$directory_path = sprintf('%s', dirname($this->helpers->root_dir));
			if (is_dir($directory_path)) {
				$directories = preg_grep('/^([^.])/', scandir($directory_path));
				if (is_array($directories) && !empty($directories)) {
					foreach ($directories as $key => $plugin_dir) {
						if ($this->helpers->containString('addon-', $plugin_dir)) {
							$ui_blocks_dir_path = sprintf('%s/%s/ui-blocks', $directory_path, $plugin_dir);
							if (is_dir($ui_blocks_dir_path)) {
								$block_dirs = preg_grep('/^([^.])/', scandir($ui_blocks_dir_path));
								if (is_array($block_dirs) && !empty($block_dirs)) {
									foreach ($block_dirs as $block_dir_key => $block_dir_name) {
										$file_path = sprintf('%s/%s/init.php', $ui_blocks_dir_path, $block_dir_name);
										if (file_exists($file_path)) {
											$this->enableLocalisation($file_path);
											require_once $file_path;
										}
									}
								}
							}
						}
					}
				}
			}

			/* wp-content/addons ui-blocks */
			$directory_path = sprintf('%s', $this->helpers->cjwpbldr_content_path . '/addons/');
			if (is_dir($directory_path)) {
				$directories = preg_grep('/^([^.])/', scandir($directory_path));
				if (is_array($directories) && !empty($directories)) {
					foreach ($directories as $key => $plugin_dir) {
						if ($this->helpers->containString('addon-', $plugin_dir)) {
							$ui_blocks_dir_path = sprintf('%s/%s/ui-blocks', $directory_path, $plugin_dir);
							if (is_dir($ui_blocks_dir_path)) {
								$block_dirs = preg_grep('/^([^.])/', scandir($ui_blocks_dir_path));
								if (is_array($block_dirs) && !empty($block_dirs)) {
									foreach ($block_dirs as $block_dir_key => $block_dir_name) {
										$file_path = sprintf('%s/%s/init.php', $ui_blocks_dir_path, $block_dir_name);
										if (file_exists($file_path)) {
											$this->enableLocalisation($file_path);
											require_once $file_path;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		public function enableLocalisation($path)
		{
			$block_path = dirname($path);
			$locale = get_locale();
			$domain = basename($block_path) . '-locale';
			if (!$this->helpers->containString('204', $path)) {
				return false;
			}
			$mo_path = trailingslashit($block_path) . 'languages/' . $domain . '-' . $locale . '.mo';
			$lang_path = trailingslashit($block_path) . 'languages/';
			if (file_exists($mo_path)) {
				load_textdomain($domain, $mo_path);
				load_plugin_textdomain($domain, false, $lang_path);
			}
		}

		public function createUiBlocks()
		{
			$this->helpers->createUiBlocks();
		}

		public function clearUiBlocks()
		{
			$block_posts = $this->helpers->getPostsByMetaKey('_ui_block_class_name');
			$installed_ui_blocks = $this->helpers->installedUiBlocks();
			if (is_array($block_posts) && !empty($block_posts)) {
				foreach ($block_posts as $key => $meta) {
					$class_name = $meta['meta_value'];
					if (!in_array($class_name, array_keys($installed_ui_blocks))) {
						wp_delete_post($meta['post_id'], true);
					}
				}
			}
		}

		public function showPreviews()
		{
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'edit-ui-block') {
				if (isset($_GET['id']) && $_GET['id'] !== '') {
					ob_start();
					require_once sprintf('%s/html/ui-block-edit.php', $this->helpers->framework_dir);
					echo ob_get_clean();
					die();
				}
				if (isset($_GET['jira-id']) && $_GET['jira-id'] !== '') {
					ob_start();
					require_once sprintf('%s/html/ui-block-edit.php', $this->helpers->framework_dir);
					echo ob_get_clean();
					die();
				}
			}
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'preview-ui-block') {
				ob_start();
				require_once sprintf('%s/html/ui-block-preview.php', $this->helpers->framework_dir);
				echo ob_get_clean();
				die();
			}
		}

		public function removeThemeAssets()
		{
			if (isset($_GET['cjwpbldr-action']) && ($_GET['cjwpbldr-action'] == 'edit-ui-block' || $_GET['cjwpbldr-action'] == 'preview-ui-block') && isset($_GET['id']) && $_GET['id'] !== '') {
				global $wp_styles, $wp_scripts;
				if (isset($wp_styles->registered) && !empty($wp_styles->registered)) {
					foreach ($wp_styles->registered as $key => $value) {

						if (preg_match('(wp-content/themes)', $value->src) && !preg_match('(cjwpbldr|cssjockey|wp-builder|wp-builder)', $value->handle)) {
							wp_deregister_style($value->handle);
							wp_dequeue_style($value->handle);
						}
					}
				}
				if (isset($wp_scripts->registered) && !empty($wp_scripts->registered)) {
					foreach ($wp_scripts->registered as $key => $value) {
						if (preg_match('(wp-content/themes)', $value->src) && !preg_match('(cjwpbldr|cssjockey|wp-builder|wp-builder)', $value->handle)) {
							wp_deregister_script($value->handle);
							wp_dequeue_script($value->handle);
						}
					}
				}
			}
		}

		public function editPostAdminLinks($actions, $post)
		{
			if ($post->post_type == 'cjwpbldr-ui-blocks') {
				$edit_link = admin_url('post.php?post=' . $post->ID . '&action=edit');
				$actions['edit'] = sprintf(__('<a href="%s">Edit Block</a>', 'wp-builder-locale'), $edit_link);
				unset($actions['inline hide-if-no-js']);
				$core = get_post_meta($post->ID, '_core_ui_block', true);
				if ($core == '1' && !$this->helpers->isLocal()) {
					unset($actions['trash']);
				}
				$clone_url = $this->helpers->queryString(admin_url('edit.php?post_type=cjwpbldr-ui-blocks')) . 'cjwpbldr-action=clone-ui-block&block_id=' . $post->ID;
				$frontend_edit_url = site_url('?cjwpbldr-action=edit-ui-block&id=' . $post->ID);
				if ($this->helpers->isLocal()) {
					$frontend_edit_url = site_url('?cjwpbldr-action=edit-ui-block&id=' . $post->ID . '&source=1');
				}
				$preview_url = site_url('?cjwpbldr-action=preview-ui-block&id=' . $post->ID);
				if ($this->helpers->isLocal()) {
					$preview_url = site_url('?cjwpbldr-action=preview-ui-block&id=' . $post->ID . '&source=1');
				}

				$reset_url = site_url('?cjwpbldr-action=reset-ui-block&id=' . $post->ID);
				$actions['cjwpbldr-clone-ui-block'] = '<a href="' . $clone_url . '" title="' . esc_attr__('Clone', 'wp-builder-locale') . '">' . esc_html__('Clone', 'wp-builder-locale') . '</a>';
				$actions['cjwpbldr-reset-ui-block'] = '<a href="' . $reset_url . '" title="' . esc_attr__('Reset', 'wp-builder-locale') . '">' . esc_html__('Reset', 'wp-builder-locale') . '</a>';
				//$actions['cjwpbldr-edit-ui-block'] = '<a href="' . $frontend_edit_url . '" target="_blank" title="' . esc_attr__( 'Edit on frontend', 'wp-builder-locale' ) . '">' . esc_html__( 'Frontend Edit', 'wp-builder-locale' ) . '</a>';
				$actions['cjwpbldr-preview-ui-block'] = '<a href="' . $preview_url . '" target="_blank" title="' . esc_attr__('Preview', 'wp-builder-locale') . '">' . esc_html__('Preview', 'wp-builder-locale') . '</a>';
			}

			return $actions;
		}

		public function blockUrlCallbacks()
		{
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'reset-ui-block' && isset($_GET['id'])) {
				$this->helpers->resetUiBlock($_GET['id']);
				$location = admin_url('post.php?post=' . $_GET['id'] . '&action=edit');
				if (isset($_GET['redirect'])) {
					$location = urldecode($_GET['redirect']);
				}
				wp_redirect($location);
				die();
			}

			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'hide-block-admin-preview' && isset($_GET['id'])) {
				update_option('cjwpbldr-block-admin-preview', 'hide');
				$location = admin_url('post.php?post=' . $_GET['id'] . '&action=edit');
				wp_redirect($location);
				die();
			}
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'show-block-admin-preview' && isset($_GET['id'])) {
				update_option('cjwpbldr-block-admin-preview', 'show');
				$location = admin_url('post.php?post=' . $_GET['id'] . '&action=edit');
				wp_redirect($location);
				die();
			}
		}

		public function cloneUiBlock()
		{
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'clone-ui-block' && isset($_GET['block_id'])) {
				$this->helpers->cloneUiBlock($_GET['block_id']);
				$location = admin_url('edit.php?post_type=cjwpbldr-ui-blocks');
				wp_redirect($location);
				die();
			}
		}

		public function updateUiBlockSettings($post_id)
		{
			$post_data = $_POST;
			$prefix = '_block_settings_';

			$post_info = $this->helpers->postInfo($post_id);

			if (!empty($post_info) && $post_info['post_type'] == 'cjwpbldr-ui-blocks' && strtotime($post_info['post_date']) !== strtotime($post_info['post_modified'])) {
				if (isset($post_info['_ui_block_class_name'])) {
					$class_name = $post_info['_ui_block_class_name'];

					if (isset($post_data['_block_settings_custom_css'])) {
						update_post_meta($post_info['ID'], '_block_settings_custom_css', $post_data['_block_settings_custom_css']);
					}
					if (isset($post_data['_block_settings_custom_js'])) {
						update_post_meta($post_info['ID'], '_block_settings_custom_js', $post_data['_block_settings_custom_js']);
					}

					if (isset($post_data['_block_settings_block_visibility'])) {
						$block_role = $post_data['_block_settings_block_visibility'];
						if (in_array('everyone', $post_data['_block_settings_block_visibility'])) {
							$block_role = ['everyone'];
						}
						update_post_meta($post_info['ID'], '_block_settings_block_visibility', $block_role);
					}
					if (isset($post_data['_block_settings_block_visibility_message'])) {
						update_post_meta($post_info['ID'], '_block_settings_block_visibility_message', $post_data['_block_settings_block_visibility_message']);
					}
					if (isset($post_data['_block_settings_wrap_in_container'])) {
						update_post_meta($post_info['ID'], '_block_settings_wrap_in_container', $post_data['_block_settings_wrap_in_container']);
					}

					if (class_exists($class_name)) {
						$class_instance = $class_name::getInstance();
						$class_info = $class_instance->info();
						$file = $class_info['path'] . '/style.scss';
						if (file_exists($file)) {
							$scss = '@import "global/mixins";';
							ob_start();
							require $file;
							$scss .= ob_get_clean();
							$block_css = $this->helpers->compileScss($scss);
							update_post_meta($post_info['ID'], '_block_css', $block_css);
						}
						$options = $class_info['options'];

						if (is_array($options) && !empty($options)) {
							foreach ($options as $key => $option) {
								if (!$this->helpers->containString('_block_settings_', $option['id'])) {
									$option['id'] = $prefix . $option['id'];
								}
								if (isset($post_data[$option['id']]) && $post_data[$option['id']] == '') {
									update_post_meta($post_id, $option['id'], '');
								}
								if (isset($post_data[$option['id']]) && $post_data[$option['id']] != '' && !is_array($post_data[$option['id']])) {
									update_post_meta($post_id, $option['id'], $post_data[$option['id']]);
								}
								if (array_key_exists($option['id'], $post_data) && is_array($post_data[$option['id']]) && !empty($post_data[$option['id']])) {
									update_post_meta($post_id, $option['id'], $post_data[$option['id']]);
								}
							}
						}
					}
				}
			}

			return $post_id;
		}

		public function uiBlockPreviewMetaboxInit()
		{
			add_action('add_meta_boxes', [$this, 'uiBlockPreviewMetaboxAdd']);
		}

		public function uiBlockPreviewMetaboxAdd($post_type)
		{

			$post_types = ['cjwpbldr-ui-blocks'];
			if (in_array($post_type, $post_types)) {
				add_meta_box(
					'_cjwpbldr_ui_block_admin_preview',
					__('UI Block Preview', 'wp-builder-locale'),
					[$this, 'uiBlockPreviewMetaboxContent'],
					$post_type,
					'normal',
					'high'
				);
			}
		}

		public function uiBlockPreviewMetaboxContent($post)
		{
			if (!isset($post->ID)) {
				echo $this->helpers->alert('danger', __('Something went wrong, please reset this block.', 'wp-builder-locale'));
			}
			ob_start();
			require_once sprintf('%s/html/ui-block-preview-admin.php', $this->helpers->framework_dir);
			echo ob_get_clean();
		}

		public function blockShortcode($atts, $content = null)
		{
			if (!isset($atts['id'])) {
				return $this->helpers->alert('danger', __('You must specify ui block ID.', 'wp-builder-locale'));
			}
			$block_info = $this->helpers->postInfo($atts['id']);
			if (empty($block_info)) {
				return $this->helpers->alert('danger', __('Ui block not found.', 'wp-builder-locale'));
			}

			$class_name = $block_info['_ui_block_class_name'];
			if (!class_exists($class_name)) {
				return $this->helpers->alert('danger', __('Ui block class missing.', 'wp-builder-locale'));
			}
			$class_instance = $class_name::getInstance();

			return $class_instance->render($block_info, $class_instance->info());
		}

		public function disableAddNewBlockUi()
		{
			$current_screen = get_current_screen();
			if ($current_screen->id == 'edit-cjwpbldr-ui-blocks' && $current_screen->post_type == 'cjwpbldr-ui-blocks') {
?>
				<style type="text/css">
					.wrap .page-title-action {
						display: none;
					}
				</style>
			<?php
			}
			if ($current_screen->id == 'cjwpbldr-ui-blocks' && $current_screen->post_type == 'cjwpbldr-ui-blocks' && isset($_GET['post'])) {
			?>
				<style type="text/css">
					.wrap .page-title-action {
						display: none;
					}

					.alignleft.actions.bulkactions {
						display: none;
					}

					#delete-action {
						display: none;
					}
				</style>

			<?php
			}
		}

		public function customEditPostColumns($columns)
		{
			$return = [];
			foreach ($columns as $key => $column) {
				$return[$key] = $column;
				if ($key == 'cb') {
					$return['cjwpbldr_ui_block_screenshot'] = __('Screenshot', 'wp-builder-locale');
				}
				if ($key == 'title') {
					$return['cjwpbldr_ui_block_shortcode'] = __('Shortcode', 'wp-builder-locale');
				}
			}

			return $return;
		}

		public function customEditPostColumnsContent($column, $post_id)
		{
			$post_info = $this->helpers->postInfo($post_id);
			$class_name = get_post_meta($post_id, '_ui_block_class_name', true);
			if (class_exists($class_name)) {
				$class_instance = $class_name::getInstance();
				switch ($column) {
					case 'cjwpbldr_ui_block_screenshot':
						$modal_id = 'cjwpbldr-ui-block-image-modal-' . $post_id;
						echo '<span class="cssjockey-ui">';
						echo '<div data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cjwpbldr-block-thumb">';
						if (isset($class_instance->info['thumbnail'])) {
							echo '<img src="' . $class_instance->info['thumbnail'] . '" />';
						} else {
							echo '<img src="' . $class_instance->info['screenshot'] . '" />';
						}
						echo '</div>';
						echo '<div id="' . $modal_id . '" class="cj-modal"><div data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cj-modal-background"></div><div class="cj-modal-content cj-width-50"><div class="cj-box cj-p-0"><img src="' . $class_instance->info['screenshot'] . '" /></div></div><button data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cj-modal-close cj-is-large" aria-label="close"></button></div>';
						echo '</span>';
						break;
					case 'cjwpbldr_ui_block_shortcode':
						$version = (isset($class_instance->info['version'])) ? $class_instance->info['version'] : 'NA';
						$shortcode = '[cjwpbldr_block id="' . $post_info['ID'] . '"]';
						$display[] = '<div class="cssjockey-ui" style="max-width: 210px;">';
						$display[] = '<div class="cj-field cj-has-addons cj-mt-5">';
						$display[] = '<div class="cj-control cj-is-expanded">';
						$display[] = '<a data-clipboard-text="[cjwpbldr_block id=\'' . $post_info['ID'] . '\']" class="cj-button cj-is-light cj-is-fullwidth">' . $shortcode . '</a>';
						$display[] = '</div>';
						$display[] = '<div class="cj-control">';
						$display[] = '<a data-clipboard-text="[cjwpbldr_block id=\'' . $post_info['ID'] . '\']" class="cj-button cj-is-dark"><i class="fa fa-copy"></i></a>';
						$display[] = '</div>';
						$display[] = '</div>';
						$display[] = '<div>' . sprintf('ID: %s | Version: %s', str_replace('cjwpbldr_uib_', 'uib-', $class_name), $version) . '</div>';
						$display[] = '</div>';

						echo implode('', $display);
						break;
				}
			}
		}

		public function enqueueUiBlockCss()
		{
			$block_ids = '';
			$template_id = $this->helpers->getTemplateId();
			if ($template_id != 0) {
				$ui_blocks = get_post_meta($template_id, '_cjwpbldr_ui_blocks', true);
				$template_block_ids = [];
				if (is_array($ui_blocks) && !empty($ui_blocks)) {
					foreach ($ui_blocks as $key => $block) {
						$template_block_ids[] = $block['ID'];
					}
				}
				$block_ids = implode(',', $template_block_ids);
			}

			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'edit-ui-block') {
				$block_ids = $_GET['id'];
			}
			if ($block_ids != '') {
				$path = $this->helpers->queryString($this->helpers->site_url) . 'cjwpbldr-blocks-css=1&blocks=' . $block_ids;
				wp_enqueue_style('cjwpbldr-template-blocks-css', $path, [], $template_id, 'all');
				//echo '<link rel="stylesheet" href="' . $path . '" />';
			}
		}

		public function enqueueUiBlockJs()
		{
			$block_ids = '';
			$template_id = $this->helpers->getTemplateId();
			if ($template_id != 0) {
				$ui_blocks = get_post_meta($template_id, '_cjwpbldr_ui_blocks', true);
				$template_block_ids = [];
				if (is_array($ui_blocks) && !empty($ui_blocks)) {
					foreach ($ui_blocks as $key => $block) {
						$template_block_ids[] = $block['ID'];
					}
				}
				$block_ids = implode(',', $template_block_ids);
			}
			if (isset($_GET['cjwpbldr-action']) && $_GET['cjwpbldr-action'] == 'edit-ui-block') {
				$block_ids = $_GET['id'];
			}
			if ($block_ids != '') {
				$path = $this->helpers->queryString($this->helpers->site_url) . 'cjwpbldr-blocks-js=1&blocks=' . $block_ids;
				wp_register_script('cjwpbldr-template-blocks-js', $path, ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $template_id, true);
				$localize_vars = $this->helpers->localizeScripts();
				wp_enqueue_script('cjwpbldr-template-blocks-js');
				wp_localize_script('cjwpbldr-template-blocks-js', 'locale', $localize_vars);
				//echo '<script type="text/javascript" src="' . $path . '"></script>';
			}
		}

		public function addTrigger($vars)
		{
			$vars[] = 'cjwpbldr-blocks-css';
			$vars[] = 'cjwpbldr-blocks-js';

			return $vars;
		}

		public function parseTrigger()
		{
			if (intval(get_query_var('cjwpbldr-blocks-css')) == 1) {
				ob_start();
				require_once sprintf('%s/ui-blocks.css.php', $this->helpers->framework_dir);
				echo ob_get_clean();
				exit;
			}
			if (intval(get_query_var('cjwpbldr-blocks-js')) == 1) {
				ob_start();
				require_once sprintf('%s/ui-blocks.js.php', $this->helpers->framework_dir);
				echo ob_get_clean();
				exit;
			}
		}

		/*  */

		public function addTaxonomyTemplateMetaBox()
		{
			$taxonomies = get_taxonomies();
			$cjwpbldr_taxonomies = get_option('cjwpbldr_taxonomies', []);
			$taxonomies = array_merge($taxonomies, $cjwpbldr_taxonomies);
			if (is_array($taxonomies) && !empty($taxonomies)) {
				foreach ($taxonomies as $key => $taxonomy_name) {
					add_action($taxonomy_name . '_add_form_fields', [$this, 'addTemplateHtmlField'], 99, 2);
					add_action($taxonomy_name . '_edit_form_fields', [$this, 'editTemplateHtmlField'], 99, 2);
					add_action('created_' . $taxonomy_name, [$this, 'updateTemplateId'], 99, 2);
					add_action('edited_' . $taxonomy_name, [$this, 'updateTemplateId'], 99, 2);
					add_filter('manage_edit-' . $taxonomy_name . '_columns', [$this, 'addTemplateColumn']);
					add_filter('manage_' . $taxonomy_name . '_custom_column', [$this, 'addTemplateColumnContent'], 10, 3);
				}
			}
		}

		public function addTemplateColumn($columns)
		{
			$columns['_cjwpbldr_use_template'] = __('WP Builder Template', 'wp-builder-locale');

			return $columns;
		}

		public function addTemplateColumnContent($content, $column_name, $term_id)
		{

			if ($column_name !== '_cjwpbldr_use_template') {
				return $content;
			}

			$term_id = absint($term_id);
			$term_meta = get_term_meta($term_id, '_cjwpbldr_use_template', true);

			if ($term_meta == '') {
				$content .= '&mdash;';
			}
			if (!empty($term_meta)) {
				$content .= esc_attr($this->helpers->customTemplatesArray($term_meta));
			}

			return $content;
		}

		public function addTemplateHtmlField($taxonomy)
		{
			?>
			<div class="form-field term-group">
				<label for="_cjwpbldr_use_template"><?php _e('WP Builder Template', 'wp-builder-locale'); ?></label>
				<select class="postform" id="_cjwpbldr_use_template" name="_cjwpbldr_use_template" style="min-width: 150px;">
					<?php foreach ($this->helpers->customTemplatesArray() as $id => $name) : ?>
						<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
					<?php endforeach; ?>
				</select>
			</div><?php
				}

				public function editTemplateHtmlField($term, $taxonomy)
				{
					$term_meta = get_term_meta($term->term_id, '_cjwpbldr_use_template', true);
					?>
			<tr class="form-field term-group-wrap">
				<th scope="row"><label for="_cjwpbldr_use_template"><?php _e('WP Builder Template', 'wp-builder-locale'); ?></label></th>
				<td>
					<select class="postform" id="_cjwpbldr_use_template" name="_cjwpbldr_use_template" style="min-width: 150px;">
						<?php foreach ($this->helpers->customTemplatesArray() as $id => $name) : ?>
							<option value="<?php echo $id; ?>" <?php selected($term_meta, $id); ?>><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr><?php
				}

				public function updateTemplateId($term_id, $tt_id)
				{
					if (isset($_POST['_cjwpbldr_use_template'])) {
						$template_id = $_POST['_cjwpbldr_use_template'];
						update_term_meta($term_id, '_cjwpbldr_use_template', $template_id);
					}
				}

				public function hideAddNewFromSubMenu()
				{
					global $submenu;
					unset($submenu['edit.php?post_type=cjwpbldr-ui-blocks'][10]);
				}

				public function disableAddingNewBlocks()
				{
					if (get_current_screen()->post_type == 'cjwpbldr-ui-blocks') {
						wp_die("You ain't allowed to do that!");
					}
				}

				public function removeAddNewBlockAdminBarLink()
				{
					global $wp_admin_bar;
					$wp_admin_bar->remove_menu('new-cjwpbldr-ui-blocks');
				}
			}

			cjwpbldr_builder_ui_blocks::getInstance();
		}
