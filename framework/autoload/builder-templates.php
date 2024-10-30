<?php
if( ! class_exists( 'cjwpbldr_builder_templates' ) ) {
	class cjwpbldr_builder_templates {
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
			$core_features = $this->helpers->getOption( 'core_features' );

			if( is_array( $core_features ) && $this->helpers->inArray( 'builder', $core_features ) ) {
				$core_features = array_merge( $core_features, ['blocks', 'builder'] );
				//$core_features = array_merge( $core_features, ['blocks', 'builder', 'portfolio', 'team', 'faqs', 'testimonials'] );
				$this->helpers->updateOption( 'core_features', $core_features );
			}

			if( $this->helpers->inArray( 'builder', $core_features ) ) {

				require_once sprintf( '%s/config/metaboxes/select_template_metabox.php', $this->helpers->root_dir );

				$post_types = get_post_types( [
					'public' => true
				] );
				foreach( $post_types as $post_type_key => $post_type_value ) {
					add_filter( 'manage_' . $post_type_key . 's_columns', [$this, 'postTemplateColumn'] );
					add_action( 'manage_' . $post_type_key . 's_custom_column', [$this, 'postTemplateColumnContent'], 10, 2 );
				}

				add_action( 'current_screen', [$this, 'loadTemplateEditor'] );
				add_filter( 'post_row_actions', [$this, 'editPostAdminLinks'], 10, 2 );
				add_action( 'wp_enqueue_scripts', [$this, 'removeThemeStyles'], 9999 );
				add_action( 'init', [$this, 'processThemeSupports'] );
				add_action( 'admin_init', [$this, 'removeTemplateColumn'], 10, 2 );

				add_filter( 'manage_cjwpbldr-templates_posts_columns', [$this, 'customEditPostColumns'], 10, 1 );
				add_action( 'manage_cjwpbldr-templates_posts_custom_column', [$this, 'customEditPostColumnsContent'], 10, 2 );
				add_filter( 'manage_wpb-portfolio_posts_columns', [$this, 'customEditPostPortfolioColumns'], 10, 1 );
				add_action( 'manage_wpb-portfolio_posts_custom_column', [$this, 'customEditPostColumnsPortfolioContent'], 10, 2 );
				add_filter( 'manage_wpb-team_posts_columns', [$this, 'customEditPostTeamColumns'], 10, 1 );
				add_action( 'manage_wpb-team_posts_custom_column', [$this, 'customEditPostColumnsTeamContent'], 10, 2 );
				add_filter( 'manage_wpb-testimonials_posts_columns', [$this, 'customEditPostTeamColumns'], 10, 1 );
				add_action( 'manage_wpb-testimonials_posts_custom_column', [$this, 'customEditPostColumnsTeamContent'], 10, 2 );

				add_action( 'admin_notices', [$this, 'showThemeInstallerMessage'] );
				add_action( 'admin_init', [$this, 'showTemplateEditor'], 1 );

				add_action( 'save_post', [$this, 'savePostTemplateId'], 9999 );

				add_filter( 'template_redirect', [$this, 'cloneTemplate'] );
				add_filter( 'template_redirect', [$this, 'exportTemplate'] );
				add_action( 'template_redirect', [$this, 'cleanTemplate'], 2 );
				add_action( 'template_redirect', [$this, 'renderTemplate'], 11 );
				add_action( 'admin_init', [$this, 'adminCallbacks'], 11 );
			}
		}

		public function showThemeInstallerMessage() {
			if( isset( $_GET['callback'] ) && $_GET['callback'] == 'core-install-themes' ) {
				return false;
			}
			$theme_data = get_transient( 'cjwpbldr_install_theme' );
			if( ! $theme_data ) {
				return false;
			}
			$continue_link = $this->helpers->callbackUrl( 'core-install-themes' );
			$cancel_link = $this->helpers->queryString( $this->helpers->callbackUrl( 'core-install-themes' ) ) . 'cjwpbldr-action=cancel-installer';
			echo '<div class="notice notice-warning">';
			echo '<p>';
			echo '<b>' . $this->helpers->config( 'plugin-info', 'name' ) . '</b><br>';
			echo __( 'Demo site installation is in progress.', 'wp-builder-locale' );
			echo '<br>';
			echo sprintf( __( '<a href="%s">Click here</a> to continue with the installation or <a href="%s">Click here</a> to cancel the installation.', 'wp-builder-locale' ), $continue_link, $cancel_link );
			echo '</p>';
			echo '</div>';
		}

		public function loadTemplateEditor() {
			$current_screen = get_current_screen();

			if( $current_screen->id == 'cjwpbldr-templates' && $current_screen->base == 'post' && $current_screen->action == 'add' ) {
				$post_data = [
					'post_type' => 'cjwpbldr-templates',
					'post_title' => $this->helpers->uniqueColumnValue( __( 'New Template', 'wp-builder-locale' ), 'posts', 'post_title', ' ' ),
					'post_status' => 'publish',
					'post_content' => 'custom template',
					'_cjwpbldr_ui_blocks' => [],
					'_cjwpbldr_ui_blocks_preview' => [],
				];
				$new_post_data = $this->helpers->createPost( $post_data );
				$template_editor_url = admin_url( '?cjwpbldr-action=edit-template&template-id=' . $new_post_data['ID'] );
				wp_redirect( $template_editor_url );
				exit;
			}

			if( $current_screen->post_type == 'cjwpbldr-templates' && $current_screen->base == 'post' && isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
				$template_editor_url = admin_url( '?cjwpbldr-action=edit-template&template-id=' . $_GET['post'] );
				wp_redirect( $template_editor_url );
				exit;
			}
		}

		public function showTemplateEditor() {
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'edit-template' && isset( $_GET['template-id'] ) && $_GET['template-id'] != '' ) {
				ob_start();
				$post_info = $this->helpers->postInfo( $_GET['template-id'] ); // required
				require_once sprintf( '%s/html/template-editor.php', $this->helpers->framework_dir );
				echo ob_get_clean();
				die();
			}
		}

		public function editPostAdminLinks( $actions, $post ) {
			if( $post->post_type == 'cjwpbldr-templates' ) {
				$edit_link = admin_url( 'post.php?post=' . $post->ID . '&action=edit' );
				$actions['edit'] = sprintf( __( '<a href="%s">Edit Template</a>', 'wp-builder-locale' ), $edit_link );
				unset( $actions['inline hide-if-no-js'] );
				$core = get_post_meta( $post->ID, '_core_ui_block', true );
				if( $core == '1' ) {
					unset( $actions['trash'] );
				}
				$clone_url = site_url( '?cjwpbldr-action=clone-template&template_id=' . $post->ID );
				$export_url = site_url( '?cjwpbldr-action=export-template&template_id=' . $post->ID );
				$actions['cjwpbldr-clone-template'] = '<a href="' . $clone_url . '" title="' . esc_attr__( 'Clone', 'wp-builder-locale' ) . '">' . esc_html__( 'Clone', 'wp-builder-locale' ) . '</a>';
				$actions['cjwpbldr-export-template'] = '<a href="' . $export_url . '" title="' . esc_attr__( 'Export', 'wp-builder-locale' ) . '">' . esc_html__( 'Export', 'wp-builder-locale' ) . '</a>';
			}

			return $actions;
		}

		public function cloneTemplate() {
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'clone-template' && isset( $_GET['template_id'] ) ) {
				$post_info = $this->helpers->postInfo( $_GET['template_id'] );
				unset( $post_info['ID'] );
				unset( $post_info['post_name'] );
				unset( $post_info['post_date'] );
				unset( $post_info['post_date_gmt'] );
				unset( $post_info['post_modified'] );
				unset( $post_info['post_modified_gmt'] );
				unset( $post_info['_core_template'] );
				$post_info['post_title'] = $post_info['post_title'] . ' - ' . __( 'Copy', 'wp-builder-locale' );
				$new_block_id = wp_insert_post( $post_info );
				$this->helpers->updatePostInfo( $new_block_id, $post_info );
				$location = admin_url( 'edit.php?post_type=cjwpbldr-templates' );
				wp_redirect( $location );
				die();
			}
		}

		public function exportTemplate() {
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'export-template' && isset( $_GET['template_id'] ) ) {
				$post_info = $this->helpers->postInfo( $_GET['template_id'] );
				$parsed_site_url = parse_url( $this->helpers->site_url );
				//$file_name = sanitize_title( $parsed_site_url['host'] . ' ' . $post_info['post_title'] ) . '-template.json';
				$file_name = strtolower( $this->helpers->uniqueString( 'wpb-template-' . $post_info['post_title'] . '-' ) ) . '.json';
				$zip_folder = sanitize_title( str_replace( '.json', '', $file_name ) );
				$json = [
					'install_type' => 'cjwpbldr-template',
					'template_name' => $post_info['post_title'],
					'file_name' => sanitize_title( $post_info['post_title'] . '-template' ),
				];
				if( ! isset( $post_info['_cjwpbldr_ui_blocks'] ) ) {
					$message = sprintf( __( '<p>This template has no UI blocks assigned.</p><p><a href="%s">Show Templates</a></p>', 'wp-builder-locale' ), admin_url( 'edit.php?post_type=cjwpbldr-templates' ) );
					wp_die( $message );
				}
				if( isset( $post_info['_cjwpbldr_ui_blocks'] ) && ! empty( $post_info['_cjwpbldr_ui_blocks'] ) ) {
					foreach( $post_info['_cjwpbldr_ui_blocks'] as $key => $block ) {
						$block_post_info = $this->helpers->postInfo( $block['ID'] );
						if( isset( $block['info']['class_name'] ) ) {
							$block_class = $block['info']['class_name']::getInstance();
							$block_class_info = $block_class->info();
							$block_slug = basename( str_replace( ['/screenshot.png', '/screenshot.jpg'], '', $block['info']['screenshot'] ) );
							$json['template_blocks'][ $key ]['class_name'] = $block['info']['class_name'];
							$json['template_blocks'][ $key ]['slug'] = $block_slug;
							$json['template_blocks'][ $key ]['version'] = $block['info']['version'];
							if( isset( $block_class_info['options'] ) ) {
								foreach( $block_class_info['options'] as $option_key => $option ) {
									if( isset( $block_post_info[ '_block_settings_' . $option['id'] ] ) ) {
										$json['template_blocks'][ $key ]['settings'][ $option['id'] ] = $block_post_info[ '_block_settings_' . $option['id'] ];
									}
								}
							}
							$block_path = str_replace( site_url( '/' ), ABSPATH, dirname( $block['info']['screenshot'] ) );
							$zip_source = $block_path;
							$this->helpers->createDirectory( $this->helpers->cjwpbldr_content_path . '/exports/' . $zip_folder );
							$zip_destination = $this->helpers->cjwpbldr_content_path . '/exports/' . $zip_folder . '/' . $block_slug . '.zip';
							$this->helpers->zipFolder( $zip_source, $zip_destination );
						}
					}
				}

				if( $this->helpers->isLocal() ) {
					$title = trim( str_replace( ['Template', ' - '], '', $post_info['post_title'] ) );
					$json_file_name = 'wpb-template-' . sanitize_title( $title );
					if( ! is_dir( ABSPATH . 'templates-json' ) ) {
						$this->helpers->createDirectory( ABSPATH . 'templates-json' );
					}
					$this->helpers->putFileContents( ABSPATH . 'templates-json/' . $json_file_name . '.json', json_encode( $json ) );
				}
				$this->helpers->putFileContents( $this->helpers->cjwpbldr_content_path . '/exports/' . $zip_folder . '/wpb-template.json', json_encode( $json ) );

				$compiled_zip_source = $this->helpers->cjwpbldr_content_path . '/exports/' . $zip_folder;
				$compiled_zip_destination = $this->helpers->cjwpbldr_content_path . '/exports/' . $zip_folder . '.zip';
				$this->helpers->zipFolder( $compiled_zip_source, $compiled_zip_destination );

				if( is_dir( $compiled_zip_source ) ) {
					$this->helpers->deleteDirectory( $compiled_zip_source );
				}

				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: application/octet-stream' );
				header( 'Content-Disposition: attachment; filename="' . basename( $compiled_zip_destination ) . '"' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . filesize( $compiled_zip_destination ) );
				readfile( $compiled_zip_destination );
				$this->helpers->deleteFile( $compiled_zip_destination );
				exit;
			}
		}

		public function renderTemplate() {
			// render assigned template
			$template_id = $this->helpers->getTemplateId();

			if( ! is_null( $template_id ) && $template_id > 0 && ! isset( $_GET['cjwpbldr-action'] ) ) {
				$template_info = $this->helpers->postInfo( $template_id );
				if( ! empty( $template_info ) && $template_info['post_status'] == 'publish' ) {
					require_once sprintf( '%s/html/render-template.php', $this->helpers->framework_dir );
					die();
				}
			}
		}

		public function cleanTemplate() {
			$template_id = $this->helpers->getTemplateId();
			$preview_blocks = get_post_meta( $template_id, '_cjwpbldr_ui_blocks_preview', true );
			if( is_array( $preview_blocks ) ) {
				$sorted_blocks = [];
				foreach( $preview_blocks as $key => $block ) {
					$check_post = get_post( $block['ID'] );
					if( ! is_null( $check_post ) ) {
						$sorted_blocks[] = $block;
					}
				}
				update_post_meta( $template_id, '_cjwpbldr_ui_blocks_preview', $sorted_blocks );
			}

			$live_blocks = get_post_meta( $template_id, '_cjwpbldr_ui_blocks', true );
			if( is_array( $live_blocks ) ) {
				$sorted_blocks = [];
				foreach( $live_blocks as $key => $block ) {
					$check_post = get_post( $block['ID'] );
					if( ! is_null( $check_post ) ) {
						$sorted_blocks[] = $block;
					}
				}
				update_post_meta( $template_id, '_cjwpbldr_ui_blocks', $sorted_blocks );
			}
		}

		public function removeThemeStyles() {
			$template_id = $this->helpers->getTemplateId();
			if( $template_id > 0 ) {
				$template_info = $this->helpers->postInfo( $template_id );
				if( ! empty( $template_info ) && $template_info['post_status'] == 'publish' ) {
					$this->removeThemeAssets();
				}
			}
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'edit-template' ) {
				$item_version = time();
				wp_register_style( 'cjwpbldr-template-builder', $this->helpers->assets_url . '/css/template-builder.min.css', [], $item_version, 'all' );
				wp_enqueue_style( 'cjwpbldr-template-builder' );

				wp_register_script( 'cjwpbldr-template-builder-js', $this->helpers->assets_url . '/js/template-builder.min.js', ['jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'], $item_version, false );
				$localize_vars = $this->helpers->localizeScripts();
				wp_enqueue_script( 'cjwpbldr-template-builder-js' );
				wp_localize_script( 'cjwpbldr-template-builder-js', 'locale', $localize_vars );
				$this->removeThemeAssets();
			}
		}

		public function removeThemeAssets() {
			global $wp_styles, $wp_scripts;
			foreach( $wp_styles->registered as $key => $value ) {
				if( preg_match( '(wp-content/themes)', $value->src ) && ! preg_match( '(cjwpbldr|cssjockey|addon)', $value->handle ) ) {
					wp_deregister_style( $value->handle );
					wp_dequeue_style( $value->handle );
				}
			}
			foreach( $wp_scripts->registered as $key => $value ) {
				if( preg_match( '(wp-content/themes)', $value->src ) && ! preg_match( '(cjwpbldr|cssjockey|addon)', $value->handle ) ) {
					wp_deregister_script( $value->handle );
					wp_dequeue_script( $value->handle );
				}
			}
		}

		public function processThemeSupports() {
			if( ! $this->helpers->is_theme ) {
				return false;
			}
			$theme_supports = [
				'post-thumbnails',
				'post-formats',
				'custom-header',
				'custom-background',
				'menus',
				'automatic-feed-links',
				'editor-style',
				'widgets',
				'html5',
				'title-tag',
			];

			if( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
			if( ! current_theme_supports( 'post-formats' ) ) {
				add_theme_support( 'post-formats', ['aside', 'image', 'video', 'quote', 'link', 'gallery', 'audio'] );
			}
			if( ! current_theme_supports( 'post-menus' ) ) {
				add_theme_support( 'post-menus' );
			}
			if( ! current_theme_supports( 'automatic-feed-links' ) ) {
				add_theme_support( 'automatic-feed-links' );
			}
			if( ! current_theme_supports( 'title-tag' ) ) {
				add_theme_support( 'title-tag' );
			}
			if( ! current_theme_supports( 'html5' ) ) {
				add_theme_support( 'html5', ['comment-form', 'comment-list', 'gallery', 'caption'] );
			}
			if( ! current_theme_supports( 'customize-selective-refresh-widgets' ) ) {
				add_theme_support( 'customize-selective-refresh-widgets' );
			}
		}

		public function postTemplateColumn( $columns ) {
			$return = [];
			foreach( $columns as $key => $column ) {
				$return[ $key ] = $column;
				if( $key == 'date' ) {
					$return['_cjwpbldr_template_id'] = __( 'WP Builder Template', 'wp-builder-locale' );
				}
			}

			return $return;
		}

		public function postTemplateColumnContent( $column, $post_id ) {
			if( $column == '_cjwpbldr_template_id' ) {
				$post_type = get_post_type( $post_id );
				$template_info = $this->helpers->getTemplateInfoByPostId( $post_id );
				echo '<div class="cssjockey-ui">';
				if( ! empty( $template_info ) ) {
					$remove_link = $this->helpers->queryString( admin_url() ) . 'cjwpbldr-action=remove-post-template&template-id=' . $template_info['ID'] . '&post-id=' . $post_id . '&redirect=' . $this->helpers->currentUrl();
					echo sprintf( '<a target="_blank" href="%s">%s</a>', admin_url( 'post.php?post=' . $template_info['ID'] . '&action=edit' ), $template_info['post_title'] );
					echo '<span class="cj-mh-8">|</span>';
					echo '<a href="' . $remove_link . '" class="cj-confirm" data-message="' . __( 'Are you sure?', 'lang-textdomain' ) . '"><i class="fa fa-trash-alt cj-color-danger"></i></a>';
				} else {
					/*echo '&mdash;';
					echo '<span class="cj-mh-8">|</span>';*/
					$create_new_template_link = $this->helpers->queryString( admin_url() ) . 'cjwpbldr-action=new-post-template&post-id=' . $post_id;
					echo '<a href="' . $create_new_template_link . '" class="cj-tooltip" data-tooltip="Create New Template">' . __( 'New', 'lang-textdomain' ) . '</a>';
					echo '<span class="cj-mh-8">|</span>';
					$assign_template_link = admin_url( 'post.php?post=' . $post_id . '&action=edit&highlight=assign-template' );
					echo '<a href="' . $assign_template_link . '" class="cj-tooltip" data-tooltip="Assign Existing Template">' . __( 'Assign', 'lang-textdomain' ) . '</a>';
				}
				echo '</div>';
				echo '<style>.column-_cjwpbldr_template_id{width: 230px;}</style>';
			}
		}

		public function removeTemplateColumn() {
			$non_public_post_types = get_post_types( ['public' => false] );
			foreach( $non_public_post_types as $key => $post_type ) {
				add_filter( 'manage_' . $key . '_posts_columns', [$this, 'unsetPostTemplateColumn'] );
			}
		}

		public function unsetPostTemplateColumn( $columns ) {
			unset( $columns['_cjwpbldr_template_id'] );

			return $columns;
		}

		public function customEditPostColumns( $columns ) {
			$return = [];
			foreach( $columns as $key => $column ) {
				$return[ $key ] = $column;
				/*if( $key == 'cb' ) {
					$return['cjwpbldr_ui_block_screenshot'] = __( 'Screenshot', 'wp-builder-locale' );
				}*/
				if( $key == 'title' ) {
					$return['cjwpbldr_template_blocks'] = __( 'UI Blocks', 'wp-builder-locale' );
				}
			}

			return $return;
		}

		public function customEditPostColumnsContent( $column, $post_id ) {
			$post_info = $this->helpers->postInfo( $post_id );

			$this->helpers->syncTemplateBlocks( $post_id );

			$edit_link = admin_url( 'post.php?post=' . $post_info['ID'] . '&action=edit' );
			switch( $column ) {
				case 'cjwpbldr_template_blocks':
					if( isset( $post_info['_cjwpbldr_ui_blocks'] ) ) {

						$ui_blocks_count = count( $this->helpers->getArrayValue( '_cjwpbldr_ui_blocks', $post_info, [] ) );
						$ui_blocks = $post_info['_cjwpbldr_ui_blocks'];
						$display[] = '<div class="cssjockey-ui">';
						if( $ui_blocks_count == 0 ) {
							$display[] = '<span class="cj-color-danger">' . __( 'No blocks assigned.<br>', 'wp-builder-locale' ) . '</span>';
							$display[] = '<a href="' . $edit_link . '">';
							$display[] = __( 'Add UI Blocks', 'wp-builder-locale' );
							$display[] = '</a>';
						}
						if( $ui_blocks_count > 0 ) {
							$screenshots = [];
							foreach( $ui_blocks as $key => $block ) {
								$screenshots[] = $block['info']['screenshot'];
							}

							$modal_id = $this->helpers->uniqueString( 'template-preview-modal-' );
							$display[] = '<div class="cssjockey-ui">';
							$display[] = sprintf( __( '%s block(s)', 'wp-builder-locale' ), $ui_blocks_count );
							$display[] = '<br>';
							$display[] = '<a target="_blank" href="#" data-toggle="class" data-classes="cj-is-active" data-target="#' . $modal_id . '">' . __( 'Quick Look', 'wp-builder-locale' ) . '</a>';
							$display[] = '<span class="cj-m-10">|</span>';
							$display[] = '<a target="_blank" href="' . $edit_link . '">' . __( 'Edit Template', 'wp-builder-locale' ) . '</a>';
							$display[] = '<div id="' . $modal_id . '" class="cj-modal">';
							$display[] = '<div class="cj-modal-background" data-toggle="class" data-classes="cj-is-active" data-target="#' . $modal_id . '"></div>';
							$display[] = '<div class="cj-modal-content cj-width-50">';
							$display[] = '<div class="cj-box">';
							foreach( $screenshots as $screenshot ) {
								$display[] = '<img src="' . $screenshot . '" />';
							}
							$display[] = '</div>';
							$display[] = '</div>';
							$display[] = '<button class="cj-modal-close cj-is-large" aria-label="close" data-toggle="class" data-classes="cj-is-active" data-target="#' . $modal_id . '"></button>';
							$display[] = '</div>';
							$display[] = '</div>';
						}
						$display[] = '</div>';

						echo implode( '', $display );
					} else {
						$display[] = '<div class="cssjockey-ui">';
						$display[] = '<span class="cj-color-danger">' . __( 'No blocks assigned.<br>', 'wp-builder-locale' ) . '</span>';
						$display[] = '<a href="' . $edit_link . '">';
						$display[] = __( 'Add UI Blocks', 'wp-builder-locale' );
						$display[] = '</a>';
						$display[] = '</div>';
						echo implode( '', $display );
					}
					break;
			}
		}

		public function customEditPostPortfolioColumns( $columns ) {
			$return = [];
			foreach( $columns as $key => $column ) {
				$return[ $key ] = $column;
				if( $key == 'cb' ) {
					$return['cjwpbldr_portfolio_screenshot'] = __( 'Screenshot', 'wp-builder-locale' );
				}
			}

			return $return;
		}

		public function customEditPostColumnsPortfolioContent( $column, $post_id ) {
			$post_info = $this->helpers->postInfo( $post_id );
			switch( $column ) {
				case 'cjwpbldr_portfolio_screenshot':
					$modal_id = 'cjwpbldr-portfolio-image-modal-' . $post_id;
					echo '<span class="cssjockey-ui">';
					echo '<div data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cjwpbldr-block-thumb">';
					echo '<img src="' . $post_info['featured_image_url'] . '" />';
					echo '</div>';
					echo '<div id="' . $modal_id . '" class="cj-modal"><div data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cj-modal-background"></div><div class="cj-modal-content cj-width-50"><div class="cj-box cj-p-10"><img width="100%" src="' . $post_info['featured_image_url'] . '" /></div></div><button data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cj-modal-close cj-is-large" aria-label="close"></button></div>';
					echo '</span>';
					break;
			}
		}

		public function customEditPostTeamColumns( $columns ) {
			$return = [];
			foreach( $columns as $key => $column ) {
				$return[ $key ] = $column;
				if( $key == 'cb' ) {
					$return['cjwpbldr_team_screenshot'] = __( 'Screenshot', 'wp-builder-locale' );
				}
			}

			return $return;
		}

		public function customEditPostColumnsTeamContent( $column, $post_id ) {
			$post_info = $this->helpers->postInfo( $post_id );
			switch( $column ) {
				case 'cjwpbldr_team_screenshot':
					$modal_id = 'cjwpbldr-portfolio-image-modal-' . $post_id;
					echo '<span class="cssjockey-ui">';
					echo '<div data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cjwpbldr-block-thumb">';
					echo '<img src="' . $post_info['featured_image_url'] . '" style="width: 64px; border-radius: 64px;  " />';
					echo '</div>';
					echo '<div id="' . $modal_id . '" class="cj-modal"><div data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cj-modal-background"></div><div class="cj-modal-content cj-width-50"><div class="cj-box cj-p-10"><img width="100%" src="' . $post_info['featured_image_url'] . '" /></div></div><button data-toggle="class" data-target="#' . $modal_id . '" data-classes="cj-is-active" class="cj-modal-close cj-is-large" aria-label="close"></button></div>';
					echo '</span>';
					break;
			}
		}

		public function adminCallbacks() {
			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'remove-post-template' ) {
				$post_info = $this->helpers->postInfo( $_GET['post-id'] );
				$home_page_id = get_option( 'page_on_front' );
				$blog_page_id = get_option( 'page_for_posts' );
				if( $home_page_id == $post_info['ID'] ) {
					$this->helpers->dbDelete( 'postmeta', [
						'meta_key' => '_cjwpbldr_use_template_homepage',
						'meta_value' => 'on',
					] );
				} else if( $blog_page_id == $post_info['ID'] ) {
					$this->helpers->dbDelete( 'postmeta', [
						'meta_key' => '_cjwpbldr_use_template_blog_page',
						'meta_value' => 'on',
					] );
				} else {
					delete_post_meta( $post_info['ID'], '_cjwpbldr_use_template_singular' );
				}
				wp_redirect( $_GET['redirect'] );
				die();
			}

			if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'new-post-template' ) {
				$post_info = $this->helpers->postInfo( $_GET['post-id'] );
				$template_post_title = sprintf( __( '%s Template', 'lang-textdomain' ), $post_info['post_title'] );
				$template_post_title = $this->helpers->uniqueColumnValue( $template_post_title, 'posts', 'post_title', ' - ' );
				$template_post_data = [
					'post_title' => $template_post_title,
					'post_content' => sprintf( __( '%s Template', 'lang-textdomain' ), $post_info['post_title'] ),
					'post_type' => 'cjwpbldr-templates',
					'post_status' => 'publish',
					'_cjwpbldr_ui_blocks' => [],
					'_cjwpbldr_ui_blocks_preview' => [],
				];
				$new_template_info = $this->helpers->createPost( $template_post_data );
				$home_page_id = get_option( 'page_on_front' );
				$blog_page_id = get_option( 'page_for_posts' );
				if( $home_page_id == $post_info['ID'] ) {
					$this->helpers->dbDelete( 'postmeta', [
						'meta_key' => '_cjwpbldr_use_template_homepage',
						'meta_value' => 'on',
					] );
					update_post_meta( $new_template_info['ID'], '_cjwpbldr_use_template_homepage', 'on' );
				} else if( $blog_page_id == $post_info['ID'] ) {
					$this->helpers->dbDelete( 'postmeta', [
						'meta_key' => '_cjwpbldr_use_template_blog_page',
						'meta_value' => 'on',
					] );
					update_post_meta( $new_template_info['ID'], '_cjwpbldr_use_template_blog_page', 'on' );
				} else {
					update_post_meta( $post_info['ID'], '_cjwpbldr_use_template_singular', $new_template_info['ID'] );
				}
				$edit_template_url = admin_url( 'post.php?post=' . $new_template_info['ID'] . '&action=edit' );
				wp_redirect( $edit_template_url );
				die();
			}
		}

		public function savePostTemplateId( $post_id ) {
			$post_info = $this->helpers->postInfo( $post_id );
			delete_post_meta( $post_id, '_cjwpbldr_use_template_singular' );
			$home_page_id = get_option( 'page_on_front' );
			$blog_page_id = get_option( 'page_for_posts' );

			/*if( isset( $_POST['_cjwpbldr_use_template_singular'] ) && $_POST['_cjwpbldr_use_template_singular'] == '' ) {
				update_post_meta( $post_id, '_cjwpbldr_original_post_content', urlencode( $post_info['post_content'] ) );
			}
			if( isset( $_POST['_cjwpbldr_use_template_singular'] ) && $_POST['_cjwpbldr_use_template_singular'] != '' ) {
				$template_info = $this->helpers->postInfo( $_POST['_cjwpbldr_use_template_singular'] );
				$template_post_content = $this->helpers->getTemplateContent( $post_info, $template_info );
				update_post_meta( $post_id, '_cjwpbldr_template_post_content', urlencode( $template_post_content ) );
			}*/

			if( $home_page_id == $post_info['ID'] ) {
				if( $_POST['_cjwpbldr_use_template_singular'] == '' ) {
					$this->helpers->dbDelete( 'postmeta', [
						'meta_key' => '_cjwpbldr_use_template_homepage'
					] );
				} else {
					update_post_meta( $_POST['_cjwpbldr_use_template_singular'], '_cjwpbldr_use_template_homepage', 'on' );
				}
			} else if( $blog_page_id == $post_info['ID'] ) {
				if( $_POST['_cjwpbldr_use_template_singular'] == '' ) {
					$this->helpers->dbDelete( 'postmeta', [
						'meta_key' => '_cjwpbldr_use_template_blog_page'
					] );
				} else {
					update_post_meta( $_POST['_cjwpbldr_use_template_singular'], '_cjwpbldr_use_template_blog_page', 'on' );
				}
			} else {
				if( isset( $_POST['_cjwpbldr_use_template_singular'] ) && $_POST['_cjwpbldr_use_template_singular'] != '' ) {
					update_post_meta( $post_id, '_cjwpbldr_use_template_singular', $_POST['_cjwpbldr_use_template_singular'] );
				}
			}

			// save template blocks data
			if( isset( $_POST['_cjwpbldr_use_template_singular'] ) && $_POST['_cjwpbldr_use_template_singular'] != '' ) {
				$template_id = $_POST['_cjwpbldr_use_template_singular'];
				$this->helpers->publishTemplateBlocksPreview( $template_id );
			}

			if( isset( $_POST['_cjwpbldr_use_template_singular'] ) ) {
				if( $_POST['_cjwpbldr_use_template_singular'] != '' ) {
					$template_info = $this->helpers->postInfo( $_POST['_cjwpbldr_use_template_singular'] );
					$template_content = $this->helpers->getTemplateContent( $post_info, $template_info );
					$this->helpers->dbUpdate( 'posts', [
						'post_content' => strip_tags( $template_content, 'p, a, img, h1,h2,h3,h4,h5,h6' )
					], 'ID', $post_id );
				}
			}
		}

	}

	cjwpbldr_builder_templates::getInstance();
}