<?php if( isset( $menus ) && is_array( $menus ) && ! empty( $menus ) ) { ?>
    <div id="cjwpbldr-wp-nav-menu-links" class="posttypediv" style="padding: 0; margin-top: -25px;">
        <div id="tabs-panel-cjwpbldr-wp-nav-menus" class="tabs-panel tabs-panel-active" style="border: none; padding: 0;">
            <ul id="cjwpbldr-wp-nav-menus-checklist" class="categorychecklist form-no-clear" style="margin-top: 0; margin-bottom: 0; padding-bottom: 10px;">
				<?php
				$count = 10000000;
				foreach( $menus as $key => $menu ) {
					if( $menu['type'] == 'cjwpbldr-metabox-heading' ) {
						echo '<p style="margin-bottom: 5px;"><strong>' . $menu['label'] . '</strong></p>';
					} else {
						$count --;
						?>
                        <li>
                            <label class="menu-item-title">
                                <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $count; ?>][menu-item-object-id]" value="<?php echo $count; ?>"> <?php echo $menu['label'] ?>
                            </label>
                            <input type="hidden" class="menu-item-type" name="menu-item[<?php echo $count; ?>][menu-item-type]" value="<?php echo $menu['type'] ?>">
                            <input type="hidden" class="menu-item-title" name="menu-item[<?php echo $count; ?>][menu-item-title]" value="<?php echo $menu['title'] ?>">
                            <input type="hidden" class="menu-item-url" name="menu-item[<?php echo $count; ?>][menu-item-url]" value="<?php echo $menu['url'] ?>">
                            <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo $count; ?>][menu-item-classes]" value="<?php echo $menu['classes'] ?>">
                        </li>
					<?php }
				} ?>
            </ul>
        </div>
        <p class="button-controls" style="margin-top: 0; padding-top: 15px; border-top: 1px solid #dddddd;">
            <span class="list-controls">
                <a href="/wordpress/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#cjwpbldr-wp-nav-menu-links" class="select-all">Select All</a>
            </span>
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-cjwpbldr-wp-nav-menu-links">
                <span class="spinner"></span>
            </span>
        </p>
    </div>
<?php } ?>