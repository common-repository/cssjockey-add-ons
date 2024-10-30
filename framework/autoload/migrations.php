<?php
// Exit if accessed directly.
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'cjwpbldr_migrations' ) ) {
	class cjwpbldr_migrations {
		private static $instance;
		public $helpers;
		protected $addons_in_dev;
		private $table_name, $previous_version_class;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->table_name = 'cjaddons_options';
			$this->previous_version_class = 'cjaddons_framework';
			$this->addons_in_dev = [
				'addon-social-login',
				'addon-content-placeholders',
			];
			add_action( 'admin_init', [$this, 'loadMigrations'] );
			add_action( 'cjwpbldr_after_options_sync', [$this, 'importPreviousSettings'] );
			add_action( 'admin_notices', [$this, 'showAdminNotice'] );
		}

		public function loadMigrations() {
			$active_plugins = $this->helpers->getActivePlugins();
			$plugins_path = str_replace( $this->helpers->site_url, ABSPATH, plugins_url() );
			$plugins_path = trailingslashit( str_replace( '//', '/', $plugins_path ) );
			$old_plugins_found = [];
			if( ! empty( $active_plugins ) ) {
				foreach( $active_plugins as $key => $active_plugin ) {
					// is addon
					$plugin_name = dirname( $active_plugin );
					if( ! in_array( $plugin_name, $this->addons_in_dev ) ) {
						$plugin_dir = trailingslashit( $plugins_path . dirname( $active_plugin ) );
						if( file_exists( $plugin_dir ) ) {
							$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $active_plugin );
							if( $this->helpers->containString( 'addon-', $active_plugin ) ) {
								$old_file_path = $plugin_dir . 'cssjockey-addon.php';
								if( file_exists( $old_file_path ) ) {
									$this->saveMigrationStatusInDB( $plugin_name, $plugin_info );
									$old_plugins_found[] = "<b>{$plugin_name}</b>";
								}
							}
						}
					}
				}
			}

			if( is_array( $old_plugins_found ) && ! empty( $old_plugins_found ) ) {
				add_action( 'in_admin_header', function () use ( $old_plugins_found ) {
					remove_all_actions( 'admin_notices' );
					remove_all_actions( 'all_admin_notices' );
					add_action( 'admin_notices', function () use ( $old_plugins_found ) {
						$title = $this->helpers->config( 'plugin-info', 'name' ) . ' <span class="cj-text-normal">(formerly CSSJockey Add-ons)</span>';
						$content[] = '<div class="cj-fs-20 cj-content">';
						$content[] = sprintf( __( '<p class="cj-mb-5">We have <b>rebuilt</b> CSSJockey Add-ons and all our add-ons with the latest coding standards and <b>re-branded</b> CSSJockey Add-ons to WP Builder.</p>', 'wp-builder-locale' ) );
						$content[] = sprintf( __( '<p class="cj-mb-5">You <b class="cj-color-danger">must upgrade</b> the following add-ons to their latest version:</p> <p>%s</p>', 'wp-builder-locale' ), implode( ', ', $old_plugins_found ) );
						$content[] = '</div>';
						$message = implode( '', $content );
						echo $this->helpers->adminNotice( 'cjwpbldr-old-addons-found', 'error', $message, $title, false );
					} );
				} );
			}
		}

		private function saveMigrationStatusInDB( $active_plugin, $plugin_info ) {
			$version = $plugin_info['Version'];
			$addon_migration_option_name = 'cjwpbldr_migrated_' . $active_plugin . '_v' . $version;

			if( get_option( $addon_migration_option_name ) && get_option( $addon_migration_option_name ) == 1 ) {
				return false;
			}

			add_option( $addon_migration_option_name, 0 );
			add_option( $addon_migration_option_name, 0 );
		}

		public function importPreviousSettings( $current_settings ) {

			$cjaddons_installed_versions = get_option( 'cjaddons_installed_versions' );

			if( class_exists( $this->previous_version_class ) ) {
				return false;
			}

			if( ! $this->helpers->tableExists( $this->helpers->wpTableName( $this->table_name ) ) ) {
				return false;
			}

			$imported = get_option( '_cjwpbldr_previous_imported', false );

			$previous_settings = $this->helpers->dbSelect( $this->table_name, '*' );

			$previous_settings_array = [];
			if( ! empty( $previous_settings ) ) {
				foreach( $previous_settings as $key => $value ) {
					$option_value = (is_serialized( $value['option_value'] )) ? unserialize( $value['option_value'] ) : $value['option_value'];
					$previous_settings_array[ $value['option_name'] ] = $option_value;
				}
			}

			if( $this->helpers->isValidatedArray( $cjaddons_installed_versions ) ) {
				foreach( $cjaddons_installed_versions as $add_on_name => $add_on_version ) {
					if( ! in_array( $add_on_name, $this->addons_in_dev ) ) {
						$path = $this->helpers->getAddonPath( $add_on_name );
						$wp_builder_file = $path . '/wp-builder.php';
						if( file_exists( $wp_builder_file ) ) {
							$add_on_migration_option_name = 'cjwpbldr_migrated_' . $add_on_name . '_v' . $add_on_version;
							$add_on_migration_option_value = get_option( $add_on_migration_option_name );
							if( $add_on_migration_option_value == 0 ) {
								if( ! empty( $previous_settings_array ) ) {
									foreach( $previous_settings_array as $key => $value ) {
										if( isset( $current_settings[ $key ] ) ) {
											$this->helpers->updateOption( $key, $value );
										}
									}
								}
								update_option( $add_on_migration_option_name, 1 );
							}
						}
					}
				}
			}

			if( $imported ) {
				return false;
			}

			if( ! empty( $previous_settings_array ) ) {
				foreach( $previous_settings_array as $key => $value ) {
					if( isset( $current_settings[ $key ] ) ) {
						$this->helpers->updateOption( $key, $value );
					}
				}
			}

			update_option( '_cjwpbldr_previous_imported', true );
		}

		public function showAdminNotice() {
			if( ! class_exists( $this->previous_version_class ) ) {
				return false;
			}
			$imported = get_option( '_cjwpbldr_previous_imported', false );
			if( $imported && class_exists( $this->previous_version_class ) ) {
				$title = $this->helpers->config( 'plugin-info', 'name' );
				$message = sprintf( __( '%s is upgraded version of %s. Please <a href="%s" class="cj-text-bold cj-color-danger">deactivate</a> %s and use %s add-ons to avoid any conflicts.', 'wp-builder-locale' ), $title, 'CSSJockey Add-ons', admin_url( 'plugins.php' ), 'CSSJockey Add-ons', $title );
				$this->helpers->adminNotice( '_cjwpbldr_previous_imported', 'error', $message, $title, false );
			}
		}
	}

	cjwpbldr_migrations::getInstance();
}