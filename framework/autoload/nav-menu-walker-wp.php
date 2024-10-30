<?php

if( ! class_exists( 'cjwpbldr_menu_walker_wp' ) ) {
	class cjwpbldr_menu_walker_wp extends Walker_Nav_Menu {
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

		function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
			if( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );

			$object = $item->object;
			$type = $item->type;
			$title = esc_attr( $item->title );
			$description = esc_attr( $item->description );
			$permalink = $item->url;
			$title_tag = ($title !== '') ? ' title="' . $title . '"' : '';
			$target = ($item->target !== '') ? ' target="_blank"' : '';
			$rel = ($item->xfn !== '') ? ' rel="' . $item->xfn . '"' : '';
			$classes = [];
			$item_classes = (is_array( $item->classes )) ? $item->classes : $classes;
			$item_classes[] = 'menu-item-depth-' . $depth;
			$item_classes = apply_filters( 'nav_menu_css_class', array_filter( $item_classes ), $item, $args, $depth );
			$item_classes = implode( ' ', $item_classes );
			$arrow_down = '';
			if( isset( $args->has_children ) && $args->has_children && $depth == 0 ) {
				$arrow_down = '<i class="menu-arrow fa fa-chevron-down"></i>';
			}
			if( isset( $args->has_children ) && $args->has_children && $depth > 0 ) {
				$arrow_down = '<i class="menu-arrow fa fa-chevron-right"></i>';
			}

			// li hover overrides
			if(
				in_array( 'cjwpbldr-navbar-divider', $item->classes ) ||
				in_array( 'cjwpbldr-navbar-button', $item->classes )
			) {
				$item_classes .= ' cjwpbldr-hover-transparent';
			}

			// social icons
			if( in_array( 'cjwpbldr-navbar-social-link', $item->classes ) ) {
				if( $item->cjwpbldr_social_icon_style == 'pe-bg' ) {
					$item_classes .= ' cj-color-white cj-bg-' . str_replace( 'pe-so-', '', $item->cjwpbldr_social_icon );
				}
				if( $item->cjwpbldr_social_icon_gap == 'small' ) {
					$item_classes .= ' cj-ph-5 ';
				}
				if( $item->cjwpbldr_social_icon_gap == 'medium' ) {
					$item_classes .= ' cj-ph-10 ';
				}
			}

			$display[] = "{$n}";
			$display[] = '<li class="' . $item_classes . '">';
			$display[] = "{$n}";

			if( in_array( 'cjwpbldr-navbar-button', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/button.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else if( in_array( 'cjwpbldr-navbar-divider', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/divider.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else if( in_array( 'cjwpbldr-navbar-social-link', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/social-link.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else if( in_array( 'cjwpbldr-navbar-icon-link', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/icon-link.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else if( in_array( 'cjwpbldr-navbar-search-form', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/search-form.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else if( in_array( 'cjwpbldr-navbar-blog', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/blog.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else if( in_array( 'cjwpbldr-navbar-avatar', $item->classes ) ) {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/avatar.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			} else {
				ob_start();
				include sprintf( '%s/html/nav-menu-items/default.php', $this->helpers->framework_dir );
				$display[] = ob_get_clean();
			}

			$display = implode( '', $display );

			$display = apply_filters( 'walker_nav_menu_start_el', $display, $item, $depth, $args );

			$output .= "{$indent}{$display}{$n}";
		}

		public function start_lvl( &$output, $depth = 0, $args = [] ) {
			if( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			$output .= "{$indent}<ul>{$n}";
		}

		public function end_lvl( &$output, $depth = 0, $args = [] ) {
			if( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			$output .= "{$indent}</ul>{$n}";
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

		public function end_el( &$output, $category, $depth = 0, $args = [] ) {
			if( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			$output .= "{$indent}</li>{$n}";
		}

	}
	//cjwpbldr_menu_walker_wp::getInstance();
}