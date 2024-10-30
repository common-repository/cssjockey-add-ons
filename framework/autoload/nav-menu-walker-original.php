<?php

if( ! class_exists( 'cjwpbldr_navbar_walker' ) ) {
	class cjwpbldr_navbar_walker extends Walker_Nav_Menu {
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
		}

		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			if( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			// Default class.
			$classes = array('sub-menu');
			$class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
			$class_names = $class_names ? ' class="cj-navbar-item cj-has-dropdown cj-is-hoverable ' . esc_attr( $class_names ) . '"' : '';

			$boxed = (isset( $args->is_boxed ) && $args->is_boxed) ? 'cj-is-boxed' : '';

			$output .= "{$n}{$indent}<div class=\"cj-navbar-dropdown {$boxed}\">{$n}";
		}

		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			if( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = "";
				$n = "";
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			$output .= "$indent</div><!-- /cj-navbar-dropdown -->\n</div><!-- /cj-navbar-item -->{$n}";
		}

		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ($depth) ? str_repeat( "\t", $depth ) : '';
			$helpers = cjwpbldr_helpers::getInstance();
			$menu_id = $item->ID;
			$object = $item->object;
			$type = $item->type;
			$title = esc_attr( $item->title );
			$title_tag = ($title !== '') ? 'title="' . $title . '"' : '';
			$target = ($item->target !== '') ? 'target="_blank"' : '';
			$classes = array();
			$item_classes = (is_array( $item->classes )) ? $item->classes : $classes;
			foreach( $item_classes as $key => $class ) {
				if( $class == 'current_page_item' || $class == 'current-menu-item' ) {
					$classes['cj-is-active'] = 'cj-is-active';
				}
				$classes[ $class ] = $class;
			}
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = explode( ' ', $class_names );
			foreach( $class_names as $c_key => $c_value ) {
				$classes[ $c_value ] = $c_value;
			}
			$description = $item->description;
			$permalink = $item->url;
			$rel = ($item->xfn !== '') ? 'rel="' . $item->xfn . '"' : '';

			// divider
			if( in_array( 'cjwpbldr-navbar-divider', $item->classes ) ) {
				if( $depth > 0 ) {
					$display = '<hr class="cj-navbar-divider">';
				} else {
					$display = '<div class="cj-navbar-item cj-navbar-item-divider"></div>';
				}
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// button
			if( in_array( 'cjwpbldr-navbar-logo', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/logo.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// button
			if( in_array( 'cjwpbldr-navbar-button', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/button.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// search form
			if( in_array( 'cjwpbldr-navbar-search-form', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/search-form.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();

				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// blog with latest posts
			if( in_array( 'cjwpbldr-navbar-blog', $item->classes ) ) {
				$classes[] = 'cj-navbar-link';
				ob_start();
				include sprintf( '%s/html/nav-menu-items/blog-with-posts.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// Social Link
			if( in_array( 'cjwpbldr-navbar-social-link', $item->classes ) ) {
				$icon_gap = '';
				$icon_gap = $item->cjwpbldr_social_icon_gap;
				if( isset( $item->cjwpbldr_social_icon_gap ) && $item->cjwpbldr_social_icon_gap == 'small' ) {
					$icon_gap = 'cj-pl-6 cj-pr-6';
				}
				if( isset( $item->cjwpbldr_social_icon_gap ) && $item->cjwpbldr_social_icon_gap == 'medium' ) {
					$icon_gap = 'cj-pl-9 cj-pr-9';
				}
				$classes[] = 'cj-navbar-item cj-has-social-link ' . $icon_gap;
				ob_start();
				include sprintf( '%s/html/nav-menu-items/social-link.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			if( in_array( 'cjwpbldr-navbar-icon-link', $item->classes ) ) {
				$classes[] = 'cj-navbar-item cj-has-icon-link ';
				ob_start();
				include sprintf( '%s/html/nav-menu-items/icon-link.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// blog (mega menu)
			if( in_array( 'cjwpbldr-navbar-blog-mega-menu', $item->classes ) ) {
				$classes[] = 'cj-navbar-link';
				ob_start();
				include sprintf( '%s/html/nav-menu-items/blog-mega-menu.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// woocommerce (cart link)
			if( in_array( 'cjwpbldr-navbar-wc-cart-link', $item->classes ) ) {
				$classes[] = 'cj-navbar-item';
				ob_start();
				include sprintf( '%s/html/nav-menu-items/woocommerce-shop-link.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			// woocommerce (mega menu)
			if( in_array( 'cjwpbldr-navbar-wc-mega-menu', $item->classes ) ) {
				$classes[] = 'cj-navbar-link';
				ob_start();
				include sprintf( '%s/html/nav-menu-items/woocommerce-mega-menu.php', $this->helpers->framework_dir );
				$item_output[] = ob_get_clean();
				$display = implode( "\n", $item_output );
				$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

				return false; // custom nav menu item
			}

			$classes[] = '';
			$item_output = array();
			if( $args->has_children && $depth >= 0 ) {
				$item_output[] = '<div class="cj-navbar-item cj-has-dropdown cj-is-hoverable">' . "\n";
				$classes[] = 'cj-navbar-link';
			} else {
				$classes[] = 'cj-navbar-item';
			}

			ob_start();
			include sprintf( '%s/html/nav-menu-items/normal-link.php', $this->helpers->framework_dir );
			$item_output[] = ob_get_clean();

			$display = implode( '', $item_output );

			$output .= apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );
		}

		public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
			if( ! $element ) {
				return false;
			}
			$id_field = $this->db_fields['id'];
			// Display this element.
			if( is_object( $args[0] ) ) {
				$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
			}
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
		}

		public function end_el( &$output, $category, $depth = 0, $args = array() ) {
			$output .= "\n";
		}
	}
	//cjwpbldr_navbar_walker::getInstance();
}