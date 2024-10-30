<?php if( ! empty( $list_items ) ) { ?>
    <div class="cssjockey-ui">
        <div id="cjwpbldr-assistant-modal" class="cj-modal">
            <div class="cj-modal-background"></div>
            <div class="cj-modal-content cj-fs-18 cj-width-40">
                <div class="cj-is-block cj-flex cj-is-full-height cj-ph-0">
                    <div class="cj-pt-50">
                        <div class="cj-text-right cj-mb-5 cj-fs-14 cj-color-white cj-pr-15">
                            <kbd>"ESC" to hide</kbd> <span class="cj-mh-5 cj-opacity-50">|</span> <kbd>"CTRL + /" to show</kbd>
                        </div>
                        <div class="cj-columns cj-is-multiline cj-is-mobile cj-is-gapless cj-bg-white">
                            <div class="cj-column cj-is-narrow" style="width: 63px;">
                                <div class="cj-p-10 cj-bd-right cj-lh-0">
									<?php echo $this->helpers->logo( 43, 'logo-on-light' ); ?>
                                </div>
                            </div>
                            <div class="cj-column">
                                <input id="cjwpbldr-assistant-input" class="cj-input cj-is-large" placeholder="What would you like to do? e.g install add-on"/>
                                <ul class="cjwpbldr-assistant-tasks cj-is-hidden">
									<?php foreach( $list_items as $item_key => $item_name ) { ?>
                                        <li><?php echo $item_name; ?></li>
									<?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="cjwpbldr-assistant-action-url" value="<?php echo $this->helpers->queryString( admin_url() ) ?>cjwpbldr-assistant=">
<?php } ?>