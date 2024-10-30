<?php if( $depth == 0 ) { ?>
    <a title="&nbsp;"><i class="fa fa-search"></i></a>
    <ul class="cj-pv-10 cj-bg-white cj-has-bg-light">
        <li class="cjwpbldr-hover-transparent">
            <form action="<?php echo get_bloginfo('url') ?>" method="get">
                <div class="cj-field cj-has-addons" style="margin-bottom: 0 !important;">
                    <div class="cj-control">
                        <input name="s" type="text" class="cj-input" placeholder="<?php _e( 'Search..', 'wp-builder-locale' ) ?>">
                    </div>
                    <div class="cj-control">
                        <button type="submit" class="cj-button cj-is-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </li>
    </ul>
<?php } ?>


<!--<div class="cj-navbar-item <?php /*echo 'menu-item-' . $item->post_name; */ ?>">
    <div class="cj-navbar-content">
        <form action="<?php /*echo get_bloginfo('url'); */ ?>" method="GET" class="cj-m-0 cj-p-0">
            <div class="cj-field">
                <div class="cj-control cj-has-icons-left">
                    <input name="s" type="search" class="cj-input cj-br-50" placeholder="<?php /*_e( 'search' ) */ ?>"/>
                    <span class="cj-icon cj-is-small cj-is-left"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </form>
    </div>
</div>-->