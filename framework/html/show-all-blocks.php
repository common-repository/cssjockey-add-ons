<?php
if(!current_user_can('manage_options')){
	wp_redirect(get_bloginfo('url'));
	die();
}
?>
<div id="top" class="cssjockey-ui">
    <div class="cj-columns cj-is-multiline cj-is-mobile cj-is-gapless">
        <div class="cj-column cj-is-narrow cj-is-flex cj-bg-dark cj-has-bg-dark" style="width: 230px;">
            <div class="cj-p-15">
                <aside class="cj-menu">
                    <p class="cj-menu-label">
                        UI Block Groups
                    </p>
                    <ul class="cj-menu-list">
						<?php foreach( $blocks_to_show as $key => $block ) { ?>
                            <li><a href="#group-<?php echo $key; ?>"><?php echo $block['group_name'] . ' (' . count( $block['blocks'] ) . ')'; ?></a></li>
						<?php } ?>
                    </ul>
                </aside>
            </div>
        </div>
        <div class="cj-column">

			<?php foreach( $blocks_to_show as $key => $block ) { ?>
                <div id="group-<?php echo $key; ?>" class="cj-content">
                    <h2 class="cj-text-center cj-bg-dark cj-has-bg-dark cj-p-30">
                        <a href="#top" class="cj-is-pulled-right"><i class="fa fa-angle-up"></i></a>
						<?php echo $block['group_name']; ?>
                    </h2>
                    <div class="cj-p-30">
						<?php foreach( $block['blocks'] as $block_key => $block_info ) {
							$jira_id = str_replace( 'cjwpbldr_uib_', '', $block_info['class_name'] );
							?>
                            <div class="cj-box">
                                <div class="cj-columns cj-is-multiline cj-is-mobile">
                                    <div class="cj-column cj-is-6-fullhd cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile">
                                        <img src="<?php echo $block_info['screenshot']; ?>" width="100%">
                                    </div>
                                    <div class="cj-column cj-is-6-fullhd cj-is-6-widescreen cj-is-6-desktop cj-is-12-tablet cj-is-12-mobile">
										<?php $source_url = str_replace( ABSPATH, site_url( '/' ), $block_info['path'] ) . '/source.png'; ?>
                                        <img src="<?php echo $source_url; ?>" width="100%">
                                    </div>
                                    <div class="cj-column cj-is-12-fullhd cj-is-12-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile">
                                        <div class="cj-bg-info cj-has-bg-info cj-text-upper cj-p-10">
                                            <div class="cj-mr-15"><b>BLOCK ID:</b> <span data-clipboard-text="<?php echo $block_info['uib_id']; ?>"><?php echo $block_info['uib_id']; ?></span></div>
                                            <div class="cj-is-pulled-right cj-mr-5">
                                                <a target="_blank" href="<?php echo site_url( '?cjwpbldr-action=edit-ui-block&jira-id=' . $jira_id . '&source=1' ) ?>">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
						<?php } ?>
                    </div>
                </div>
			<?php } ?>

        </div>
    </div>
</div>