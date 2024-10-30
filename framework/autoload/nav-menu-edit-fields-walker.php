<?php

class cjwpbldr_bulma_navbar_edit_fields_walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = [] ) {
	}

	public function end_lvl( &$output, $depth = 0, $args = [] ) {
	}

	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
		global $_wp_nav_menu_max_depth, $wp_roles;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$helpers = cjwpbldr_helpers::getInstance();

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = [
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		];

		$original_title = false;
		if( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if( is_wp_error( $original_title ) ) {
				$original_title = false;
			}
		} elseif( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = get_the_title( $original_object->ID );
		} elseif( 'post_type_archive' == $item->type ) {
			$original_object = get_post_type_object( $item->object );
			if( $original_object ) {
				$original_title = $original_object->labels->archives;
			}
		}

		$classes = [
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ((isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item']) ? 'active' : 'inactive'),
		];

		$title = $item->title;

		if( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __( '%s (Pending)' ), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label) ? $title : $item->label;

		$submenu_text = '';
		if( 0 == $depth ) {
			$submenu_text = 'style="display: none;"';
		}

		$fa_icons = $helpers->fontAwesomeIconsArray();

		?>
    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
        <div class="menu-item-bar">
            <div class="menu-item-handle">
                <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span></span>
                <span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									[
										'action' => 'move-up-menu-item',
										'menu-item' => $item_id,
									],
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-up" aria-label="<?php esc_attr_e( 'Move up' ) ?>">&#8593;</a>
							|
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									[
										'action' => 'move-down-menu-item',
										'menu-item' => $item_id,
									],
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-down" aria-label="<?php esc_attr_e( 'Move down' ) ?>">&#8595;</a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" href="<?php echo (isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item']) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ); ?>" aria-label="<?php esc_attr_e( 'Edit menu item' ); ?>"></a>
					</span>
            </div>
        </div>

        <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
			<?php if( 'custom' == $item->type ) : ?>
                <p class="field-url description description-wide">
                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php _e( 'URL' ); ?><br/>
                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>"/>
                    </label>
                </p>
			<?php endif; ?>

            <p class="description description-wide">
                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
					<?php _e( 'Navigation Label' ); ?><br/>
                    <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>"/>
                </label>
            </p>

            <p class="field-title-attribute field-attr-title description description-wide">
                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
					<?php _e( 'Title Attribute' ); ?><br/>
                    <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
                </label>
            </p>

            <p class="field-link-target description">
                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php _e( 'Open link in a new tab' ); ?>
                </label>
            </p>
            <p class="field-css-classes description description-thin">
                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
					<?php _e( 'CSS Classes (optional)' ); ?><br/>
                    <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>"/>
                </label>
            </p>
            <p class="field-xfn description description-thin">
                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
					<?php _e( 'Link Relationship (XFN)' ); ?><br/>
                    <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>"/>
                </label>
            </p>
            <p class="field-description description description-wide">
                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
					<?php _e( 'Description' ); ?><br/>
                    <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                    <span class="description"><?php _e( 'The description will be displayed in the menu if the current theme supports it.' ); ?></span>
                </label>
            </p>

			<?php if( $item->type === 'cjwpbldr-navbar-blog-mega-menu' ) { ?>
                <p><strong><?php _e( 'Mega Menu Settings' ) ?></strong></p>
                <p class="field-cjwpbldr-navbar-blog-mega-menu-thumbs description description-wide">
					<?php _e( 'Post thumbnails?' ); ?><br/>
                    <span style="display: inline-block; margin-top: 4px;">
                    <label for="edit-menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs-show<?php echo $item_id; ?>">
                        <input <?php echo ($item->cjwpbldr_navbar_blog_mega_menu_thumbs == 'show' || $item->cjwpbldr_navbar_blog_mega_menu_thumbs == '') ? 'checked' : ''; ?> name="menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs-show<?php echo $item_id; ?>" type="radio" value="show"> <?php _e( 'Show' ) ?>
                    </label>
                    <label for="edit-menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs-hide<?php echo $item_id; ?>" style="margin-left: 15px;">
                        <input <?php echo ($item->cjwpbldr_navbar_blog_mega_menu_thumbs == 'hide') ? 'checked' : ''; ?> name="menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-navbar-blog-mega-menu-thumbs-hide<?php echo $item_id; ?>" type="radio" value="hide"> <?php _e( 'Hide' ) ?>
                    </label>
                    </span>
                </p>
			<?php } ?>

            <!-- blog settings -->
	        <?php if( in_array( 'cjwpbldr-navbar-blog', $item->classes ) ) { ?>
                <p class="field-cjwpbldr-navbar-blog-posts-number description description-wide">
                    <label for="edit-menu-item-cjwpbldr-blog-posts-number-<?php echo $item_id; ?>">
						<?php _e( 'Number of posts' ); ?><br/>
                        <input type="number" id="edit-menu-item-cjwpbldr-blog-posts-number-<?php echo $item_id; ?>" min="1" max="7" class="widefat code edit-menu-item-cjwpbldr-blog-posts-number" name="menu-item-cjwpbldr-blog-posts-number[<?php echo $item_id; ?>]" value="<?php echo (isset( $item->cjwpbldr_blog_posts_number ) && $item->cjwpbldr_blog_posts_number > 0) ? $item->cjwpbldr_blog_posts_number : 5; ?>"/>
                    </label>
                </p>
			<?php } ?>

            <!-- button settings -->
			<?php if( in_array( 'cjwpbldr-navbar-button', $item->classes ) ) { ?>
                <p><strong><?php _e( 'Button Settings' ) ?></strong></p>
                <p class="description description-thin">
                    <label for="edit-menu-item-cjwpbldr-button-style-<?php echo $item_id; ?>">
						<?php _e( 'Button Style' ); ?><br/>
                        <select name="menu-item-cjwpbldr-button-style[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-button-style-<?php echo $item_id; ?>" style="width: 100%">
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-primary') ? 'selected' : '' ?> value="cj-is-primary"><?php echo ucwords( __( 'primary', 'wp-builder-locale' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-success') ? 'selected' : '' ?> value="cj-is-success"><?php echo ucwords( __( 'success', 'wp-builder-locale' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-info') ? 'selected' : '' ?> value="cj-is-info"><?php echo ucwords( __( 'info', 'wp-builder-locale' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-danger') ? 'selected' : '' ?> value="cj-is-danger"><?php echo ucwords( __( 'danger', 'wp-builder-locale' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-warning') ? 'selected' : '' ?> value="cj-is-warning"><?php echo ucwords( __( 'warning', 'wp-builder-locale' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-dark') ? 'selected' : '' ?> value="cj-is-dark"><?php echo ucwords( __( 'dark', 'wp-builder-locale' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_style == 'cj-is-light') ? 'selected' : '' ?> value="cj-is-light"><?php echo ucwords( __( 'light', 'wp-builder-locale' ) ) ?></option>
                        </select>
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-cjwpbldr-button-size-<?php echo $item_id; ?>">
						<?php _e( 'Button Size' ); ?><br/>
                        <select name="menu-item-cjwpbldr-button-size[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-button-size-<?php echo $item_id; ?>" style="width: 100%">
                            <option <?php echo ($item->cjwpbldr_button_size == 'cj-is-default') ? 'selected' : ''; ?> value="cj-is-default"><?php echo ucwords( __( 'default' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_size == 'cj-is-small') ? 'selected' : ''; ?> value="cj-is-small"><?php echo ucwords( __( 'small' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_size == 'cj-is-medium') ? 'selected' : ''; ?> value="cj-is-medium"><?php echo ucwords( __( 'medium' ) ) ?></option>
                            <option <?php echo ($item->cjwpbldr_button_size == 'cj-is-large') ? 'selected' : ''; ?> value="cj-is-large"><?php echo ucwords( __( 'large' ) ) ?></option>
                        </select>
                    </label>
                </p>

                <p class="description description-thin">
                    <label for="edit-menu-item-cjwpbldr-button-normal-<?php echo $item_id; ?>">
                        <input <?php echo ($item->cjwpbldr_button_extra_style == 'cj-is-normal' || $item->cjwpbldr_button_extra_style == '') ? 'checked' : ''; ?> name="menu-item-cjwpbldr-button-extra-style[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-button-normal-<?php echo $item_id; ?>" type="radio" value="cj-is-normal"> <?php _e( 'Is Normal?' ) ?>
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-cjwpbldr-button-inverted-<?php echo $item_id; ?>">
                        <input <?php echo ($item->cjwpbldr_button_extra_style == 'cj-is-inverted') ? 'checked' : ''; ?> name="menu-item-cjwpbldr-button-extra-style[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-button-inverted-<?php echo $item_id; ?>" type="radio" value="cj-is-inverted"> <?php _e( 'Is Inverted?' ) ?>
                    </label>
                </p>
                <br>
                <p class="description description-thin">
                    <label for="edit-menu-item-cjwpbldr-button-outlined-<?php echo $item_id; ?>">
                        <input <?php echo ($item->cjwpbldr_button_extra_style == 'cj-is-outlined') ? 'checked' : ''; ?> name="menu-item-cjwpbldr-button-extra-style[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-button-outlined-<?php echo $item_id; ?>" type="radio" value="cj-is-outlined"> <?php _e( 'Is Outlined?' ) ?>
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-cjwpbldr-button-rounded-<?php echo $item_id; ?>">
                        <input <?php echo ($item->cjwpbldr_button_extra_style == 'cj-is-rounded') ? 'checked' : ''; ?> name="menu-item-cjwpbldr-button-extra-style[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-button-rounded-<?php echo $item_id; ?>" type="radio" value="cj-is-rounded"> <?php _e( 'Is Rounded?' ) ?>
                    </label>
                </p>

			<?php } ?>
            <!-- button settings -->


			<?php if( in_array( 'cjwpbldr-navbar-social-link', $item->classes ) ) {
				$social_icons = $helpers->arrays( 'social-icons' );
				?>
                <div class="wp-clearfix">
                    <p><strong><?php _e( 'Network settings' ) ?></strong></p>
                    <p class="field-cjwpbldr-navbar-social-icon description description-wide">
						<?php _e( 'Select network icon' ); ?><br/>
                        <select name="menu-item-cjwpbldr-social-icon[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-social-icon-<?php echo $item_id; ?>" style="width: 100%">
							<?php foreach( $social_icons as $key => $social_icon ) { ?>
                                <option <?php echo ($item->cjwpbldr_social_icon == $key) ? 'selected' : ''; ?> value="<?php echo $key ?>"><?php echo ucwords( str_replace( 'pe-so-', '', $key ) ) ?></option>
							<?php } ?>
                        </select>
                    </p>

                    <p class="field-cjwpbldr-navbar-social-icon-style description description-thin">
						<?php _e( 'Icon style' ); ?><br/>
                        <select name="menu-item-cjwpbldr-social-icon-style[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-social-icon-style-<?php echo $item_id; ?>" style="width: 100%">
                            <option <?php echo ($item->cjwpbldr_social_icon_style == 'default') ? 'selected' : ''; ?> value="default"><?php _e( 'Default', '' ) ?></option>
                            <option <?php echo ($item->cjwpbldr_social_icon_style == 'pe-bg') ? 'selected' : ''; ?> value="pe-bg"><?php _e( 'Brand background', '' ) ?></option>
                            <option <?php echo ($item->cjwpbldr_social_icon_style == 'pe-color') ? 'selected' : ''; ?> value="pe-color"><?php _e( 'Brand color', '' ) ?></option>
                        </select>
                    </p>

                    <p class="field-cjwpbldr-navbar-social-icon-gap description description-thin">
						<?php _e( 'Icon gap' ); ?><br/>
                        <select name="menu-item-cjwpbldr-social-icon-gap[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-social-icon-gap-<?php echo $item_id; ?>" style="width: 100%">
                            <option <?php echo ($item->cjwpbldr_social_icon_gap == 'default') ? 'selected' : ''; ?> value="default"><?php _e( 'Default' ) ?></option>
                            <option <?php echo ($item->cjwpbldr_social_icon_gap == 'small') ? 'selected' : ''; ?> value="small"><?php _e( 'Small' ) ?></option>
                            <option <?php echo ($item->cjwpbldr_social_icon_gap == 'medium') ? 'selected' : ''; ?> value="medium"><?php _e( 'Medium' ) ?></option>
                        </select>
                    </p>

                </div>
			<?php } ?>


			<?php if( in_array( 'cjwpbldr-navbar-icon-link', $item->classes ) ) { ?>
                <div class="wp-clearfix" style="display: block;">
                    <p class="field-cjwpbldr-navbar-link-icon description description-thin">
						<?php _e( 'Select icon' ); ?><br/>
                        <select name="menu-item-cjwpbldr-link-icon[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-link-icon-<?php echo $item_id; ?>" style="width: 100%">
							<?php foreach( $fa_icons as $key => $link_icon ) { ?>
                                <option <?php echo ($item->_menu_item_cjwpbldr_link_icon == $key) ? 'selected' : ''; ?> value="<?php echo $key ?>"><?php echo $link_icon; ?></option>
							<?php } ?>
                        </select>
                    </p>
                    <p class="field-cjwpbldr-navbar-link-icon-position description description-thin">
						<?php _e( 'Icon Position' ); ?><br/>
                        <select name="menu-item-cjwpbldr-link-icon-position[<?php echo $item_id; ?>]" id="edit-menu-item-cjwpbldr-link-icon-position-<?php echo $item_id; ?>" style="width: 100%">
                            <option <?php echo ($item->_menu_item_cjwpbldr_link_icon_position === 'left') ? 'selected' : ''; ?> value="left">Left</option>
                            <option <?php echo ($item->_menu_item_cjwpbldr_link_icon_position === 'right') ? 'selected' : ''; ?> value="right">Right</option>
                        </select>
                    </p>
                </div>
			<?php } ?>


            <div class="wp-clearfix">
                <p class="field-cjwpbldr-visibility-roles description description-thin">
					<?php _e( 'Hidden for (Roles):' ); ?><br/>
                    <select multiple name="menu-item-cjwpbldr-visibility-roles[<?php echo $item_id; ?>][]" id="edit-menu-item-cjwpbldr-visibility-roles-<?php echo $item_id; ?>" style="width: 100%">
                        <option value=""><?php _e( 'None', 'wp-builder-locale' ) ?></option>
						<?php
						$wp_roles->role_names['visitor'] = __( 'Visitor' );
						foreach( $wp_roles->role_names as $role_key => $role_name ) {
							$selected = (is_array( $item->cjwpbldr_visibility_roles ) && in_array( 'cj-is-hidden-' . $role_key, $item->cjwpbldr_visibility_roles )) ? 'selected' : '';
							echo '<option ' . $selected . ' value="cj-is-hidden-' . $role_key . '">' . $role_name . '</option>';
						}
						?>
                    </select>
                </p>
                <p class="field-cjwpbldr-visibility-devices description description-thin">
					<?php _e( 'Hidden on (Devices):' ); ?><br/>
                    <select multiple name="menu-item-cjwpbldr-visibility-devices[<?php echo $item_id; ?>][]" id="edit-menu-item-cjwpbldr-visibility-devices-<?php echo $item_id; ?>" style="width: 100%">
						<?php
						$display_devices = [
							'cj-is-hidden-none' => __( 'None' ),
							'cj-is-hidden-xl' => __( 'FullHD (1392px and above)' ),
							'cj-is-hidden-lg' => __( 'Wide Screens (1200px to 1391px)' ),
							'cj-is-hidden-md' => __( 'Desktops (1024px to 1199px)' ),
							'cj-is-hidden-sm' => __( 'Tablets (768px to 1023px)' ),
							'cj-is-hidden-xs' => __( 'Mobile (until 768px)' ),
						];
						foreach( $display_devices as $device_key => $device_name ) {
							$selected = (is_array( $item->cjwpbldr_visibility_devices ) && in_array( $device_key, $item->cjwpbldr_visibility_devices )) ? 'selected' : '';
							echo '<option ' . $selected . ' value="' . $device_key . '">' . $device_name . '</option>';
						}
						?>
                    </select>
                </p>
            </div>

            <fieldset class="field-move hide-if-no-js description description-wide">
                <span class="field-move-visual-label" aria-hidden="true"><?php _e( 'Move' ); ?></span>
                <button type="button" class="button-link menus-move menus-move-up" data-dir="up"><?php _e( 'Up one' ); ?></button>
                <button type="button" class="button-link menus-move menus-move-down" data-dir="down"><?php _e( 'Down one' ); ?></button>
                <button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
                <button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
                <button type="button" class="button-link menus-move menus-move-top" data-dir="top"><?php _e( 'To the top' ); ?></button>
            </fieldset>

            <div class="menu-item-actions description-wide submitbox">
				<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                    <p class="link-to-original">
						<?php printf( __( 'Original: %s' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                    </p>
				<?php endif; ?>
                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						[
							'action' => 'delete-menu-item',
							'menu-item' => $item_id,
						],
						admin_url( 'nav-menus.php' )
					),
					'delete-menu_item_' . $item_id
				); ?>"><?php _e( 'Remove' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( ['edit-menu-item' => $item_id, 'cancel' => time()], admin_url( 'nav-menus.php' ) ) );
				?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e( 'Cancel' ); ?></a>
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>"/>
            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>"/>
            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>"/>
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>"/>
            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>"/>
        </div><!-- .menu-item-settings-->
        <ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

} // Walker_Nav_Menu_Edit
