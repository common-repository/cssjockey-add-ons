<?php
if( ! class_exists( 'cjwpbldr_assistant' ) ) {
	class cjwpbldr_assistant {
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
			// add_action( 'wp_dashboard_setup', [$this, 'wpDashboardSetup'] );
			add_action( 'admin_footer', [$this, 'welcomeSetup'] );
			add_action( 'admin_bar_menu', [$this, 'adminBarWelcomeText'], 10 );

			add_action( 'init', [$this, 'handleAssistantActions'] );
		}

		public function welcomeSetup() {
			$list_items = [
				'Install Add-ons',
				'Install UI Blocks',
				'Install Pre-built Templates',
				'Install Pre-built Websites',
				'Activate WP Builder Pro',
				'Show UI SCSS Variables',
			];
			ob_start();
			require_once sprintf( '%s/html/assistant.php', $this->helpers->framework_dir );
			echo ob_get_clean();
		}

		public function wpDashboardSetup() {
			$logo = $this->helpers->assets_url . '/svg/logo-on-light.svg';
			$title = '<span class="cssjockey-ui"><img class="cj-inline-block cj-mr-5 cj-relative" style="top: 3px;" src="' . $logo . '" width="16" height="16"><span>' . $this->helpers->config( 'plugin-info', 'name' ) . '</span></span>';
			wp_add_dashboard_widget( 'cjwpbldr-welcome', $title, [$this, 'dashboardWelcomeWidget'] );
		}

		public function dashboardWelcomeWidget() {
			echo '<div class="cssjockey-ui">';
			echo '<p class="cj-mt-0">Work in progress..</p>';
			echo '</div>';
		}

		public function adminBarWelcomeText( $wp_admin_bar ) {
			$wp_admin_bar->add_menu( [
				'id' => 'cjwpbldr-assistant',
				'parent' => 'top-secondary',
				'title' => $this->helpers->logo( '16', 'logo-on-dark', 'image', 'width: 16px !important; height: 16px !important; margin-top:8px; margin-right:3px;' ),
				'href' => '#',
				'meta' => [
					'class' => 'hello',
				],
			] );
		}

		public function handleAssistantActions() {
			if( isset( $_GET['cjwpbldr-assistant'] ) && $_GET['cjwpbldr-assistant'] != '' ) {
				$action_type = $_GET['cjwpbldr-assistant'];

				switch( $action_type ) {
					case "Install Add-ons":
						$url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=add-ons';
						wp_redirect( $url );
						die();
						break;
					case "Install UI Blocks":
						$url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=blocks';
						wp_redirect( $url );
						die();
						break;

					case "Install Pre-built Templates":
						$url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=templates';
						wp_redirect( $url );
						die();
						break;

					case "Install Pre-built Websites":
						$url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=websites';
						wp_redirect( $url );
						die();
						break;

					case "Activate WP Builder Pro":
						$url = $this->helpers->callbackUrl( 'core-cloud', true ) . 'show=activate-pro';
						wp_redirect( $url );
						die();
						break;

					case "Show UI SCSS Variables":
						$url = $this->helpers->callbackUrl( 'core-sass' );
						wp_redirect( $url );
						die();
						break;
					default:
						//echo "Your favorite color is neither red, blue, nor green!";
				}
			}
		}

	}

	cjwpbldr_assistant::getInstance();
}