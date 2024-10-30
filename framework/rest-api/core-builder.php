<?php
if( ! class_exists( 'cjwpbldr_api_core_templates_blocks' ) ) {
	/**
	 * Class cjwpbldr_api_core_templates_blocks
	 */
	class cjwpbldr_api_core_templates_blocks {

		/**
		 * @var cjwpbldr_helpers
		 * @var cjwpbldr_helpers
		 * @var array|cjwpbldr_helpers
		 * @var array|cjwpbldr_helpers|string
		 */
		public $helpers, $addon_dir, $routes, $api_url;

		/**
		 * @var
		 */
		private static $instance;

		/**
		 * @return cjwpbldr_api_core_templates_blocks
		 */
		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * cjwpbldr_api_core_templates_blocks constructor.
		 */
		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->api_url = rest_url( 'cjwpbldr' ) . '/';
			$this->routes = [
				'core-builder/products/install' => [
					'endpoint' => 'builder/products/install',
					'name' => __( 'Install Products', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'installProducts'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/groups' => [
					'endpoint' => 'builder/groups',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getGroups'],
						'post' => [$this, 'getGroups'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'publish_posts' );
					},
				],
				'core-builder/blocks' => [
					'endpoint' => 'builder/blocks',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getBlocks'],
						'post' => [$this, 'getBlocks'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->checkApiRoutePermissions( $request, 'publish_posts' );
					},
				],
				'core-builder/ui-blocks/get' => [
					'endpoint' => 'builder/ui-blocks/get',
					'name' => __( 'Get Blocks', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getBlocks'],
						'post' => [$this, 'getBlocks'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/ui-block/clone' => [
					'endpoint' => 'builder/ui-block/clone',
					'name' => __( 'Product Info', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'cloneBlock'],
						'post' => [$this, 'cloneBlock'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/ui-blocks/form/get' => [
					'endpoint' => 'builder/ui-blocks/form/get',
					'name' => __( 'Get block settings form.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'getEditBlockForm'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/ui-blocks/update' => [
					'endpoint' => 'builder/ui-blocks/update',
					'name' => __( 'Get block settings form.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'saveEditBlockForm'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/ui-blocks/reset' => [
					'endpoint' => 'builder/ui-blocks/reset',
					'name' => __( 'Reset block settings.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'resetBlockSettings'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/ui-blocks/preview/save' => [
					'endpoint' => 'builder/ui-blocks/preview/save',
					'name' => __( 'Get block html.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'saveBlockPreview'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/ui-blocks/preview/get' => [
					'endpoint' => 'builder/ui-blocks/preview/get',
					'name' => __( 'Get block html.', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getBlockPreview'],
						'post' => [$this, 'getBlockPreview'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/templates/preview/get' => [
					'endpoint' => 'builder/templates/preview/get',
					'name' => __( 'Get Template', 'wp-builder-locale' ),
					'methods' => [
						'get' => [$this, 'getTemplateEditorPreview'],
						'post' => [$this, 'getTemplateEditorPreview'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/templates/update' => [
					'endpoint' => 'builder/templates/update',
					'name' => __( 'Update Template', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'updateTemplate'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/templates/import' => [
					'endpoint' => 'builder/templates/import',
					'name' => __( 'Import template from json file.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'importTemplateFromJson'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
				'core-builder/templates/add-downloaded-block' => [
					'endpoint' => 'builder/templates/add-downloaded-block',
					'name' => __( 'Import template from json file.', 'wp-builder-locale' ),
					'methods' => [
						'post' => [$this, 'templateAddDownloadedUiBlock'],
					],
					'permissions' => function ( $request ) {
						return $this->helpers->apiUserCan( $request, 'manage_options' );
					},
				],
			];
			add_filter( 'cjwpbldr_register_api_route', [$this, 'registerRoute'] );
		}

		/**
		 * @param $routes
		 *
		 * @return array
		 */
		public function registerRoute( $routes ) {
			$routes = array_merge( $routes, $this->routes );

			return $routes;
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function getGroups( $request ) {
			$data = [];
			$response = $this->helpers->getTerms( 'cjwpbldr-blocks-group' );
			if( is_array( $response ) && ! empty( $response ) ) {
				$count = - 1;
				foreach( $response as $key => $term ) {
					$count ++;
					$data[ $count ]['group_name'] = $term['name'];
					$data[ $count ]['group_slug'] = $term['slug'];
					$blocks = $this->helpers->savedUiBlocks( $term['slug'] );
					foreach( $blocks as $block_key => $block ) {
						$block_data['ID'] = $block['ID'];
						$block_data['post_name'] = $block['post_name'];
						$block_data['post_title'] = $block['post_title'];
						$block_data['post_content'] = $block['post_content'];
						$block_data['_core_ui_block'] = (isset( $block['_core_ui_block'] )) ? true : false;
						$block_info = $this->helpers->getBlockInfo( $block['_ui_block_class_name'] );
						unset( $block_info['options'] );
						unset( $block_info['path'] );
						$block_data['info'] = $block_info;
						$data[ $count ]['blocks'][] = $block_data;
					}
				}
			}

			$return['success'] = $data;

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function cloneBlock( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$cloned_block = $this->helpers->cloneUiBlock( $post_data['block_id'] );
			$data['ID'] = $cloned_block['ID'];
			$data['post_name'] = $cloned_block['post_name'];
			$data['post_title'] = $cloned_block['post_title'];
			$data['post_content'] = $cloned_block['post_content'];
			$data['_core_ui_block'] = false;
			$block_info = $this->helpers->getBlockInfo( $cloned_block['_ui_block_class_name'] );
			unset( $block_info['options'] );
			unset( $block_info['path'] );
			$data['info'] = $block_info;
			$data['success'] = $data;

			return $this->helpers->apiResponse( $request, $data );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function getTemplateInfo( $request ) {
			$post_data = $request->get_params();
			$post_info = $this->helpers->syncTemplateBlocks( $post_data['template_id'] );
			$deleted_blocks_found = [];
			if( array_key_exists( '_cjwpbldr_ui_blocks', $post_info ) ) {
				foreach( $post_info['_cjwpbldr_ui_blocks'] as $key => $block ) {
					$block_info = $this->helpers->postInfo( $block['ID'] );
					if( empty( $block_info ) || ($block_info['post_status'] != 'publish' && $block_info['post_type'] == 'cjwpbldr-ui-blocks') ) {
						$deleted_blocks_found[] = $block;
						unset( $post_info['_cjwpbldr_ui_blocks'][ $key ] );
					}
				}
			}

			if( ! empty( $deleted_blocks_found ) ) {
				$this->helpers->updatePostInfo( $post_info['ID'], $post_info );
				$post_info = $this->helpers->postInfo( $post_info['ID'] );
			}

			$return = $post_info;

			// assignment array
			$return['_assign_options'] = $this->helpers->assignTemplateArray();

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_Error|WP_REST_Response
		 */
		public function installProducts( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$success_message = __( 'Installed, you can refresh this page to reflect changes.', 'wp-builder-locale' );
			$redirect_url = $this->helpers->callbackUrl( 'core-welcome' );

			if( isset( $post_data['success_message'] ) ) {
				$success_message = $post_data['success_message'];
			}
			if( isset( $post_data['redirect_url'] ) ) {
				$redirect_url = $post_data['redirect_url'];
			}

			if( ! in_array( $_FILES['file']['type'], ['application/zip'] ) ) {
				$message['error'] = __( 'You must upload a valid ZIP file.', 'wp-builder-locale' );

				return $this->helpers->apiError( $request, 406, $message, 406, $_FILES['file'] );
			}

			$file_name = sanitize_file_name( $_FILES['file']['name'] );
			$file_name = str_replace( '.zip', '', $file_name );
			// install blocks
			if( $_FILES['file']['type'] == 'application/zip' && $this->helpers->containString( 'wp-builder-installer', $_FILES['file']['name'] ) ) {
				$uploads_path = $this->helpers->cjwpbldr_content_path . '/imports/' . $file_name;
				$this->helpers->copyFile( $_FILES['file']['tmp_name'], $uploads_path . '.zip' );
				$this->helpers->upgradeFramework();
				sleep( 1 );
				$message['success']['redirect'] = $redirect_url;

				return $this->helpers->apiResponse( $request, $message );
			}

			// install blocks
			if( $_FILES['file']['type'] == 'application/zip' && $this->helpers->containString( 'uib-', $_FILES['file']['name'] ) ) {
				$uploads_path = $this->helpers->cjwpbldr_content_path . '/blocks/' . $file_name;
				$this->helpers->unzipFile( $_FILES['file']['tmp_name'], $uploads_path );
				//$message['success'] = __( 'UI Block installed, please refresh this page.', 'wp-builder-locale' );
				$this->helpers->checkProductUpgrades();
				//$message['success']['redirect'] = $redirect_url;
				$message['success']['message'] = $success_message;

				return $this->helpers->apiResponse( $request, $message );
			}

			// install addons
			if( $_FILES['file']['type'] == 'application/zip' && $this->helpers->containString( 'addon-', $_FILES['file']['name'] ) ) {
				$uploads_path = $this->helpers->cjwpbldr_content_path . '/addons/';
				$addon_name = str_replace( '.zip', '', $_FILES['file']['name'] );
				$addon_info = $this->helpers->addonInfo( null, $addon_name );
				if( is_array( $addon_info ) && isset( $addon_info['addon_path'] ) ) {
					$uploads_path = $addon_info['addon_path'];
					if( $this->helpers->containString( $addon_info['addon_id'], $addon_info['addon_path'] ) ) {
						$uploads_path = dirname( $addon_info['addon_path'] );
					}
				}
				$this->helpers->unzipFile( $_FILES['file']['tmp_name'], $uploads_path );
				$this->helpers->checkProductUpgrades();
				//$message['success']['redirect'] = $redirect_url;
				$message['success']['message'] = $success_message;

				return $this->helpers->apiResponse( $request, $message );
			}

			// install templates
			if( $_FILES['file']['type'] == 'application/zip' ) {
				$template_folder_name = str_replace( '.zip', '', $_FILES['file']['name'] );
				$template_uploads_path = $this->helpers->cjwpbldr_content_path . '/templates/' . $template_folder_name;
				$this->helpers->deleteDirectory( $template_uploads_path );
				$this->helpers->unzipFile( $_FILES['file']['tmp_name'], $template_uploads_path );
				if( file_exists( $template_uploads_path . '/wpb-template.json' ) ) {
					sleep( 1 );
					$this->helpers->installTemplates();
					$this->helpers->checkProductUpgrades();
					//$message['success']['redirect'] = admin_url( 'edit.php?post_type=cjwpbldr-templates' );
					$message['success']['message'] = $success_message;

					return $this->helpers->apiResponse( $request, $message );
				} else {
					$this->helpers->deleteDirectory( $template_uploads_path );
				}
			}

			// install themes
			if( $_FILES['file']['type'] == 'application/zip' && $this->helpers->containString( '-theme', $_FILES['file']['name'] ) ) {
				$theme_folder_name = str_replace( '.zip', '', $_FILES['file']['name'] );
				$theme_folder_name_path = $this->helpers->cjwpbldr_content_path . '/imports/' . $theme_folder_name;
				$this->helpers->deleteDirectory( $theme_folder_name_path );
				$this->helpers->copyFile( $_FILES['file']['tmp_name'], $theme_folder_name_path . '.zip' );
				$this->helpers->unzipFile( $theme_folder_name_path . '.zip', $theme_folder_name_path );
				ob_start();
				require_once sprintf( '%s/cjwpbldr-theme.json', $theme_folder_name_path );
				$json_data = ob_get_clean();
				delete_transient( 'cjwpbldr_install_theme' );
				set_transient( 'cjwpbldr_install_theme', json_decode( $json_data, true ), 84600 );
				$this->helpers->deleteDirectory( $theme_folder_name_path );
				$this->helpers->deleteFile( $theme_folder_name_path . '.zip' );

				$message['success']['redirect'] = $this->helpers->callbackUrl( 'core-install-themes' );

				return $this->helpers->apiResponse( $request, $message );
			}

			$message['error'] = __( 'You must upload a valid file. Make sure you have not renamed the file.', 'wp-builder-locale' );

			return $this->helpers->apiError( $request, 406, $message, 406, $_FILES['file'] );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function saveEditBlockForm( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$block_id = $post_data['block_id'];
			$block_info = $this->helpers->postInfo( $block_id );
			$options_data = $post_data['form_data'];
			$updated = [];
			foreach( $options_data as $meta_key => $meta_value ) {
				if( ! $this->helpers->containString( '_block_settings_', $meta_key ) ) {
					$meta_key = '_block_settings_' . $meta_key;
				}
				$updated[ $meta_key ] = $meta_value;
				update_post_meta( $block_info['ID'], $meta_key, $meta_value );
			}
			if( isset( $block_info['_block_has_preview'] ) && $block_info['_block_has_preview'] ) {
				foreach( $block_info as $key => $value ) {
					if( $this->helpers->containString( '_block_settings', $key ) && $this->helpers->containString( '_preview', $key ) ) {
						$preview_meta_key = $key;
						$actual_meta_key = str_replace( '_preview', '', $key );
						update_post_meta( $block_info['ID'], $actual_meta_key, $value );
						update_post_meta( $block_info['ID'], $preview_meta_key, $value );
					}
				}
				update_post_meta( $block_info['ID'], '_block_has_preview', false );
			}
			$block_info = $this->helpers->postInfo( $block_id );
			$return['success'] = $block_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function getEditBlockForm( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$block_id = $post_data['block_id'];
			$block_info = $this->helpers->postInfo( $block_id );
			$filters = (isset( $post_data['filters'] )) ? $post_data['filters'] : '';
			if( $filters != '' ) {
				$filters = explode( '|+|', $filters );
			}
			$saved_values = [];
			foreach( $block_info as $key => $value ) {
				if( $this->helpers->containString( '_block_settings', $key ) ) {
					$saved_values[ $key ] = $value;
				}
			}
			$block_form_options = $this->helpers->getBlockFormOptions( $block_info );
			foreach( $block_form_options as $key => $option ) {
				if( ! isset( $block_info[ $option['id'] . '_preview' ] ) ) {
					if( isset( $option['default'] ) ) {
						update_post_meta( $block_id, $option['id'] . '_preview', $option['default'] );
					}
				}
				$option_id = (isset( $block_info['_block_has_preview'] ) && $block_info['_block_has_preview']) ? $option['id'] . '_preview' : $option['id'];
				$saved_value = (isset( $saved_values[ $option_id ] )) ? $saved_values[ $option_id ] : $option['default'];
				$block_form_options[ $key ] = $this->helpers->sanitizeFormOption( $option, $saved_value );
				$block_form_options[ $key ]['form_tab'] = ( ! isset( $option['form_tab'] )) ? 'block-settings' : $option['form_tab'];
			}
			$return['success']['labels'] = [
				'tab_block_settings' => __( 'Content', 'wp-builder-locale' ),
				'tab_core_settings' => __( 'Layout', 'wp-builder-locale' ),
				'tab_custom_settings' => __( 'CSS/JS', 'wp-builder-locale' ),
				'save_block_settings' => __( 'Save & Publish', 'wp-builder-locale' ),
				'reset_block_settings' => __( 'Reset', 'wp-builder-locale' ),
				'continue_editing' => __( 'Continue Editing', 'wp-builder-locale' ),
				'double_click' => __( '<span class="cj-icon cj-is-small cj-mr-8"><i class="fa fa-lightbulb"></i></span> <span>You can <b>double click</b> on any edit box to open all settings.</span>', 'wp-builder-locale' ),
			];

			if( is_array( $filters ) && ! empty( $filters ) ) {
				$filtered_option_ids = [];
				foreach( $filters as $key => $option_id ) {
					$option_id = ($this->helpers->containString( '_block_settings_', $option_id )) ? $option_id : '_block_settings_' . $option_id;
					$filtered_option_ids[] = $option_id;
				}

				$filtered_options = [];
				foreach( $block_form_options as $key => $option ) {
					if( in_array( $option['id'], $filtered_option_ids ) ) {
						$filtered_options[] = $option;
					}
				}

				$return['success']['block_class_name'] = $block_info['_ui_block_class_name'];
				$return['success']['block_info'] = $block_info;
				$return['success']['block_settings_form'] = $filtered_options;

				return $this->helpers->apiResponse( $request, $return );
			}

			$return['success']['block_class_name'] = $block_info['_ui_block_class_name'];
			$return['success']['block_info'] = $block_info;
			$return['success']['block_settings_form'] = $block_form_options;

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function saveBlockPreview( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$block_info = $this->helpers->postInfo( $post_data['block_id'] );
			$all_options = $post_data['all_options'];
			$merged_options = [];
			update_post_meta( $block_info['ID'], '_block_has_preview', true );
			foreach( $all_options as $key => $value ) {
				foreach( $value as $key_2 => $option ) {
					update_post_meta( $block_info['ID'], $option['id'] . '_preview', $option['default'] );
				}
			}
			$return['success'] = $this->helpers->renderUiBlockPreview( $block_info['ID'] );

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function getBlockPreview( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			$return['success'] = $this->helpers->renderUiBlockPreview( $post_data['block_id'], true );

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function resetBlockSettings( $request ) {
			$post_data = $this->helpers->apiRequestParams( $request );
			if( ! isset( $post_data['block_id'] ) ) {
				$return['errors'] = __( 'The field block_id is required.', 'wp-builder-locale' );

				return $this->helpers->apiResponse( $request, $return );
			}
			$block_id = $post_data['block_id'];
			$block_info = $this->helpers->postInfo( $block_id );
			if( empty( $block_info ) ) {
				$return['errors'] = __( 'No ui block found with the provided block id.', 'wp-builder-locale' );

				return $this->helpers->apiResponse( $request, $return );
			}
			$block_id = $post_data['block_id'];
			$block_info = $this->helpers->resetUiBlock( $block_id );
			$return['success'] = $block_info;

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function getBlocks( $request ) {
			$post_data = $request->get_params();
			$group = (isset( $post_data['group'] )) ? $post_data['group'] : '';
			$query_vars = (isset( $post_data['query_vars'] )) ? $post_data['query_vars'] : [];
			$blocks = $this->helpers->savedUiBlocks( $group, $query_vars );
			$data = [];
			if( ! empty( $blocks ) ) {
				foreach( $blocks as $key => $block ) {
					$block_data['ID'] = $block['ID'];
					$block_data['post_name'] = $block['post_name'];
					$block_data['post_title'] = $block['post_title'];
					$block_data['post_content'] = $block['post_content'];
					$block_data['_core_ui_block'] = (isset( $block['_core_ui_block'] )) ? true : false;
					$block_info = $this->helpers->getBlockInfo( $block['_ui_block_class_name'] );
					unset( $block_info['options'] );
					unset( $block_info['path'] );
					$block_data['info'] = $block_info;
					$data[] = $block_data;
				}
			}

			$return['success'] = $data;

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function getTemplateEditorPreview( $request ) {
			$post_data = $request->get_params();
			$template_id = $post_data['template_id'];
			$template_info = $this->helpers->postInfo( $template_id );
			$template_info = $this->helpers->syncTemplateBlocks( $template_info['ID'] );
			$preview_ui_blocks = (isset( $template_info['_cjwpbldr_ui_blocks_preview'] )) ? $template_info['_cjwpbldr_ui_blocks_preview'] : $template_info['_cjwpbldr_ui_blocks'];
			$html = [];
			if( is_array( $preview_ui_blocks ) && ! empty( $preview_ui_blocks ) ) {
				foreach( $preview_ui_blocks as $key => $block ) {
					$block_info = $this->helpers->postInfo( $block['ID'] );
					if( class_exists( $block_info['_ui_block_class_name'] ) ) {
						ob_start();
						$inline_editing = true;
						echo '<div id="cjwpbldr-ui-block-preview-' . $block_info['ID'] . '" class="cjwpbldr-ui-block-preview-container">';
						echo $this->helpers->renderUiBlockPreview( $block_info['ID'], $inline_editing );
						echo '</div>';
						$html[] = ob_get_clean();
					}
				}
			}
			// settings form
			$form_fields = [];
			$form_fields_options['post_title'] = [
				'id' => 'post_title',
				'type' => 'text',
				'label' => __( 'Template Name', 'wp-builder-locale' ),
				'info' => '',
				'default' => $template_info['post_title'],
				'options' => '', // array in case of dropdown, checkbox and radio buttons
			];

			$assign_array = $this->helpers->assignTemplateArray();
			$assign_default = [];
			foreach( $assign_array as $key => $value ) {
				if( isset( $template_info[ $key ] ) ) {
					$assign_default[] = $key;
				}
			}
			$template_info['assign_template'] = $assign_default;

			$form_fields_options['assign_template'] = [
				'id' => 'assign_template',
				'type' => 'checkbox',
				'label' => __( 'Assign Template', 'wp-builder-locale' ),
				'info' => '',
				'default' => $assign_default,
				'options' => $assign_array, // array in case of dropdown, checkbox and radio buttons
			];

			$assign_template_pages = [];
			$assign_template_posts = [];
			$assigned_posts_pages = $this->helpers->getPostsByMetaKey( '_cjwpbldr_use_template_singular', $template_info['ID'], 'data' );
			if( ! empty( $assigned_posts_pages ) ) {
				foreach( $assigned_posts_pages as $info ) {
					if( $info['post_type'] == 'page' ) {
						$assign_template_pages[] = $info['ID'];
					}
					if( $info['post_type'] == 'post' ) {
						$assign_template_posts[] = $info['ID'];
					}
				}
			}

			$saved_template_pages = (isset( $template_info['assign_template_pages'] ) && ! empty( $template_info['assign_template_pages'] )) ? $template_info['assign_template_pages'] : [];
			$assign_template_pages = array_merge( $assign_template_pages, $saved_template_pages );

			$saved_template_posts = (isset( $template_info['assign_template_posts'] ) && ! empty( $template_info['assign_template_posts'] )) ? $template_info['assign_template_posts'] : [];
			$assign_template_posts = array_merge( $assign_template_posts, $saved_template_posts );

			$assign_template_terms = [];
			$assigned_term_templates = $this->helpers->dbSelect( 'termmeta', '*', [
				'meta_key' => '_cjwpbldr_use_template',
				'meta_value' => $template_info['ID']
			] );
			if( ! empty( $assigned_term_templates ) ) {
				$template_info['assign_template_terms'] = [];
				foreach( $assigned_term_templates as $key => $info ) {
					$term = get_term( $info['term_id'] );
					$term_key = "{$term->slug}@{$term->taxonomy}";
					$term_value = "{$term->slug} ({$term->taxonomy})";
					$assign_template_terms[ $key ] = $term_key;
				}
			}

			$saved_template_terms = (isset( $template_info['assign_template_terms'] ) && ! empty( $template_info['assign_template_terms'] )) ? $template_info['assign_template_terms'] : [];
			$assign_template_terms = array_merge( $assign_template_terms, $saved_template_terms );

			$form_fields_options['assign_template_pages'] = [
				'id' => 'assign_template_pages',
				'type' => 'pages',
				'label' => 'Specific Pages',
				'info' => 'This template will be applied to selected pages.',
				'default' => $assign_template_pages,
				'name' => 'assign_template_pages',
				'options' => [],
			];
			$form_fields_options['assign_template_posts'] = [
				'id' => 'assign_template_posts',
				'type' => 'posts',
				'label' => 'Specific Posts',
				'info' => 'This template will be applied to selected posts.',
				'default' => $assign_template_posts,
				'name' => 'assign_template_posts',
				'options' => [],
			];
			$form_fields_options['assign_template_terms'] = [
				'id' => 'assign_template_terms',
				'type' => 'taxonomy-terms',
				'label' => 'Specific Terms',
				'info' => 'This template will be applied to selected terms.',
				'default' => $assign_template_terms,
				'name' => 'assign_template_terms',
				'options' => [],
			];
			foreach( $form_fields_options as $key => $option ) {
				$form_fields[] = $this->helpers->sanitizeFormOption( $option, $option['default'] );
			}

			if( sha1( json_encode( $template_info['_cjwpbldr_ui_blocks'] ) ) != sha1( json_encode( $template_info['_cjwpbldr_ui_blocks_preview'] ) ) ) {
				$template_info['_cjwpbldr_is_draft'] = 1;
			}

			$html = implode( '', $html );
			if( $html == '' ) {
				$html = '<div class="cj-notification cj-is-light cj-m-20 cj-br-0 cj-text-center">' . __( '<b>There are no ui blocks assigned to this template.</b><br>Browse the UI Blocks section in the sidebar and click the plus button to start designing this template.', 'wp-builder-locale' ) . '</div>';
			}

			// set ui_blocks to array if not
			$template_info['_cjwpbldr_ui_blocks'] = ( ! is_array( $template_info['_cjwpbldr_ui_blocks'] )) ? [] : $template_info['_cjwpbldr_ui_blocks'];
			$template_info['_cjwpbldr_ui_blocks_preview'] = ( ! is_array( $template_info['_cjwpbldr_ui_blocks_preview'] )) ? [] : $template_info['_cjwpbldr_ui_blocks_preview'];

			$return['success']['template_info'] = $template_info;
			$return['success']['settings_form'] = $form_fields;
			$return['success']['html'] = $html;

			return $this->helpers->apiResponse( $request, $return );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function updateTemplate( $request ) {
			global $wpdb;
			$post_data = $this->helpers->apiRequestParams( $request );

			$template_info = $this->helpers->syncTemplateBlocks( $post_data['template_id'] );

			$template_post_data['post_title'] = $post_data['post_title'];
			$this->helpers->updatePostInfo( $post_data['template_id'], $template_post_data );

			$post_data['assign_template_pages'] = (isset( $post_data['assign_template_pages'] ) && ! empty( $post_data['assign_template_pages'] )) ? $post_data['assign_template_pages'] : [];
			$post_data['assign_template_posts'] = (isset( $post_data['assign_template_posts'] ) && ! empty( $post_data['assign_template_posts'] )) ? $post_data['assign_template_posts'] : [];
			$post_data['assign_template_terms'] = (isset( $post_data['assign_template_terms'] ) && ! empty( $post_data['assign_template_terms'] )) ? $post_data['assign_template_terms'] : [];

			update_post_meta( $post_data['template_id'], '_cjwpbldr_is_draft', $post_data['_cjwpbldr_is_draft'] );
			update_post_meta( $post_data['template_id'], '_cjwpbldr_ui_blocks', $post_data['_cjwpbldr_ui_blocks_preview'] );
			update_post_meta( $post_data['template_id'], '_cjwpbldr_ui_blocks_preview', $post_data['_cjwpbldr_ui_blocks_preview'] );
			update_post_meta( $post_data['template_id'], 'assign_template_pages', array_filter( $post_data['assign_template_pages'] ) );
			update_post_meta( $post_data['template_id'], 'assign_template_posts', array_filter( $post_data['assign_template_posts'] ) );
			update_post_meta( $post_data['template_id'], 'assign_template_terms', array_filter( $post_data['assign_template_terms'] ) );

			$template_types = $this->helpers->assignTemplateArray();
			$assign_template = $post_data['assign_template'];
			// assign global
			foreach( $template_types as $key => $value ) {
				update_post_meta( $post_data['template_id'], $key, 'off' );
			}
			foreach( $assign_template as $key => $value ) {
				update_post_meta( $post_data['template_id'], $value, 'on' );
			}
			$this->helpers->dbDelete( 'postmeta', " WHERE post_id='{$post_data['template_id']}' AND meta_key LIKE '%_use_template%' AND meta_value = 'off' " );

			$this->helpers->dbDelete( 'postmeta', " WHERE meta_key = '_cjwpbldr_use_template_singular' AND meta_value = '{$post_data['template_id']}' " );

			$this->helpers->dbDelete( 'termmeta', " WHERE meta_key = '_cjwpbldr_use_template' AND meta_value = '{$post_data['template_id']}' " );
			// assign pages
			if( is_array( $post_data['assign_template_pages'] ) && ! empty( $post_data['assign_template_pages'] ) ) {
				$post_data['assign_template_pages'] = array_filter( $post_data['assign_template_pages'] );
				foreach( $post_data['assign_template_pages'] as $key => $post_id ) {
					update_post_meta( $post_id, '_cjwpbldr_use_template_singular', $post_data['template_id'] );
				}
			}
			// assign posts
			if( is_array( $post_data['assign_template_posts'] ) && ! empty( $post_data['assign_template_posts'] ) ) {
				$post_data['assign_template_posts'] = array_filter( $post_data['assign_template_posts'] );
				foreach( $post_data['assign_template_posts'] as $key => $post_id ) {
					update_post_meta( $post_id, '_cjwpbldr_use_template_singular', $post_data['template_id'] );
				}
			}

			if( is_array( $post_data['assign_template_terms'] ) && ! empty( $post_data['assign_template_terms'] ) ) {
				$post_data['assign_template_terms'] = array_filter( $post_data['assign_template_terms'] );
				foreach( $post_data['assign_template_terms'] as $key => $term_string ) {
					$arr = explode( '@', $term_string );
					$term_slug = $arr[0];
					$taxonomy = $arr[1];
					$term = get_term_by( 'slug', $term_slug, $taxonomy );
					update_term_meta( $term->term_id, '_cjwpbldr_use_template', $post_data['template_id'] );
				}
			}

			$this->helpers->publishTemplateBlocksPreview( $post_data['template_id'] );

			$data['success'] = $this->helpers->postInfo( $post_data['template_id'] );

			return $this->helpers->apiResponse( $request, $data );
		}

		/**
		 * @param $request
		 *
		 * @return mixed|WP_REST_Response
		 */
		public function templateAddDownloadedUiBlock( $request ) {
			$post_data = $request->get_params();
			$slug = $post_data['slug'];
			$class_name = str_replace( 'uib-', 'cjwpbldr_uib_', $slug );
			$return = [];
			$new_block_info = [];
			if( class_exists( $class_name ) ) {
				$block_post_info = $this->helpers->getPostByMetaKey( '_ui_block_class_name', $class_name );
				$block_class_info = $this->helpers->getBlockInfo( $class_name );
				unset( $block_class_info['options'] );
				unset( $block_class_info['path'] );
				$new_block_info['ID'] = $block_post_info['ID'];
				$new_block_info['post_name'] = $block_post_info['post_name'];
				$new_block_info['post_title'] = $block_post_info['post_title'];
				$new_block_info['post_content'] = $block_post_info['post_content'];
				if( isset( $block_post_info['_core_ui_block'] ) ) {
					$new_block_info['_core_ui_block'] = $block_post_info['_core_ui_block'];
				}
				$new_block_info['info'] = $block_class_info;
				$return['success'] = $new_block_info;
			} else {
				$return['errors'] = __( 'UI Block not found.', 'wp-builder-locale' );
			}

			return $this->helpers->apiResponse( $request, $return );
		}

	}

	cjwpbldr_api_core_templates_blocks::getInstance();
}