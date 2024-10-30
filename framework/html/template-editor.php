<?php require_once sprintf( '%s/previews/header.php', dirname( __FILE__ ) );
if( ! current_user_can( 'switch_themes' ) ) {
	wp_redirect( get_bloginfo( 'url' ) );
	die();
}
?>
    <div class="cssjockey-ui">
        <div class="cj-pageloader cjwpbldr-template-editor-pageloader cj-is-light cj-is-active"><span class="cj-title cj-opacity-50 cj-mt-10"><span class="cj-fs-16"><?php _e( '..loading..', 'wp-builder-locale' ) ?></span></span></div>
        <div id="cjwpbldr-template-editor" v-cloak>
            <div class="cjwpbldr-editor-template" :class="{'cj-is-active' : show_template_settings}">
                <a href="@" @click.prevent="show_template_settings = !show_template_settings" class="toggle-template-settings">
                    <span v-show="show_template_settings"><i class="fa fa-caret-left"></i></span>
                    <span v-show="!show_template_settings"><i class="fa fa-caret-right"></i></span>
                </a>
                <div class="cj-quickview cj-template-editor-quickview">
                    <header class="cj-quickview-header">
                        <div class="cj-title cj-is-block cj-width-100">

                            <div class="cj-level">
                                <div class="cj-level-left cj-text-normal">
                                    <a @click="tab = 'ui-blocks'" :class="{'cj-opacity-100 cj-text-bold' : tab === 'ui-blocks'}" class="cj-level-item cj-mr-15 cj-opacity-60 cj-color-dark cj-cursor-pointer">
										<?php _e( 'UI Blocks', 'wp-builder-locale' ) ?>
                                    </a>
                                    <a @click="tab = 'settings'" :class="{'cj-opacity-100 cj-text-bold' : tab === 'settings'}" class="cj-level-item cj-mr-15 cj-opacity-60 cj-color-dark cj-cursor-pointer">
										<?php _e( 'Settings', 'wp-builder-locale' ) ?>
                                    </a>
                                </div>
                                <div class="cj-level-right">
									<?php if( ! isset( $_GET['hide-settings'] ) ) { ?>
                                        <a href="<?php echo site_url() ?>" target="_blank" class="cj-level-item cj-ml-5"><i class="fa fa-globe-americas cj-color-dark"></i></a>
                                        <a @click="goBack" class="cj-level-item cj-ml-5"><i class="fa fa-times cj-color-dark"></i></a>
									<?php } ?>
									<?php if( isset( $_GET['hide-settings'] ) ) { ?>
                                        <a @click.prevent="show_template_settings = !show_template_settings" class="cj-level-item cj-ml-5"><i class="fa fa-times cj-color-dark"></i></a>
									<?php } ?>
                                </div>
                            </div>

                    </header>
                    <div class="cj-quickview-body cj-bg-light cj-has-bg-light">
                        <div class="cj-quickview-block">
                            <div v-show="tab === 'ui-blocks'">
                                <!--template blocks-->
                                <div class="cj-box cj-pv-10 cj-ph-15 cj-br-0 cj-mb-0 cj-bg-light">
                                    <b><?php _e( 'Template Blocks', 'wp-builder-locale' ) ?></b>
                                </div>
                                <div v-if="template_info['_cjwpbldr_ui_blocks_preview'] !== undefined && template_info['_cjwpbldr_ui_blocks_preview'].length === 0" class="cj-notification cj-is-warning cj-color-warning-invert cj-br-0 cj-m-0 cj-p-15">
									<?php _e( 'No ui blocks assigned to this template.', 'wp-builder-locale' ) ?>
                                </div>
                                <div v-if="template_info['_cjwpbldr_ui_blocks_preview'] !== undefined && template_info['_cjwpbldr_ui_blocks_preview'].length > 0">
                                    <draggable v-model="template_info['_cjwpbldr_ui_blocks_preview']" @change="blocksSortOrderChanged">
                                        <div class="cj-box cj-pv-10 cj-ph-15 cj-br-0 cj-mb-0"
                                             v-for="(block, index) in template_info['_cjwpbldr_ui_blocks_preview']"
                                             :key="index"
                                        >
                                            <div class="cj-is-pulled-right cj-fs-14">
                                                <a @click="cloneTemplateBlock(block)" class="cj-tooltip cj-is-tooltip-left" data-tooltip="<?php _e( 'Clone & Add', 'wp-builder-locale' ) ?>">
                                                    <i v-if="loading === 'cloning-'+block.ID" class="fa fa-sync fa-sync cj-ml-10 cj-color-dark"></i>
                                                    <i v-if="loading !== 'cloning-'+block.ID" class="far fa-clone cj-ml-10 cj-color-dark"></i>
                                                </a>
                                                <a @click="removeTemplateBlock(index)"><i class="fa fa-trash-alt cj-ml-10 cj-color-danger"></i></a>
                                            </div>
                                            <span style="max-height: 20px; display: inline-block; position: relative; top: -4px;" class="cj-icon cj-is-small cj-fs-14 cj-opacity-90 cj-mr-5"><i class="fa fa-arrows-alt"></i></span>
                                            <span style="overflow: hidden; max-width: 190px; max-height: 20px; display: inline-block;">{{block.post_title}}</span>
                                        </div>
                                    </draggable>
                                </div>
                                <!--/template blocks-->
                                <!-- ui blocks heading-->
                                <div class="cj-box cj-pv-10 cj-ph-15 cj-br-0 cj-mb-0 cj-bg-light">
                                    <div class="cj-is-pulled-right">
                                        <a @click="switchUiBlocksTab('saved')" :class="{'cj-opacity-100': ui_blocks_tab === 'saved'}" class="cj-color-dark cj-opacity-50 cj-cursor-pointer">Installed</a>
                                        <span class="cj-mh-5 cj-opacity-50">|</span>
                                        <a @click="switchUiBlocksTab('cloud')" :class="{'cj-opacity-100': ui_blocks_tab === 'cloud'}" class="cj-color-dark cj-opacity-50 cj-cursor-pointer">Download</a>
                                    </div>
                                    <b>UI Blocks</b>
                                </div>
                                <!-- ui blocks heading-->
                                <!--saved blocks-->
                                <div v-if="ui_blocks_tab === 'saved'">

                                    <div class="cj-box cj-p-10 cj-br-0 cj-mb-0">
                                        <div class="cj-field">
                                            <div class="cj-control" :class="{'cj-is-loading' : loading === 'searching'}">
                                                <input v-model="saved_search_keyword" type="text" class="cj-input" placeholder="<?php _e( 'Search installed ui blocks..', 'wp-builder-locale' ) ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="saved_search_keyword !== '' && searched_local_blocks && searched_local_blocks.length === 0" class="cj-p-15 cj-br-0 cj-mb-0 cj-opacity-50">
										<?php _e( 'Nothing found based on your query, try different keywords.', 'wp-builder-locale' ) ?>
                                    </div>

                                    <div v-if="searched_local_blocks && searched_local_blocks.length > 0">
                                        <div v-for="(block, index) in searched_local_blocks" :key="index">
                                            <div class="cj-p-0 cj-br-0 cj-mb-0 cj-relative">
                                                <div class="cj-absolute-tr cj-mt-20 cj-mr-20">
                                                    <div class="cj-field cj-has-addons">
                                                        <!--<div class="cj-control">
                                                            <a @click="addUiBlock(block)" class="cj-button cj-is-white cj-is-small">{{block.info.name}}</a>
                                                        </div>-->
                                                        <div class="cj-control">
                                                            <a @click="addUiBlock(block)" class="cj-button cj-is-success cj-is-small"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div>
                                                    <div class="cj-lh-0"><img :src="block.info.screenshot" :alt="block.info.name"/></div>
                                                    <div class="cj-text-center cj-p-10 cj-bg-light cj-fs-14">{{block.post_title}}</div>
                                                </div>
                                            </div>
                                            <hr class="cj-mv-15 cj-opacity-50">
                                        </div>
                                    </div>

                                    <div v-if="!searched_local_blocks && saved_blocks.length > 0" v-for="(group, index) in saved_blocks" :key="group.group_slug">
                                        <div @click="active_group = (active_group === group.group_slug ? '' : group.group_slug)" class="cj-box cj-pv-10 cj-ph-15 cj-br-0 cj-mb-0 cj-cursor-pointer">
                                            <span class="cj-is-pulled-right cj-fs-14 cj-opacity-50">{{group.blocks.length}}</span>
                                            <b class="cj-text-upper cj-fs-14">
                                                <span class="cj-icon cj-is-small cj-opacity-90 cj-fs-14 cj-mr-5">
                                                    <i v-if="active_group !== group.group_slug" class="fa fa-folder"></i>
                                                    <i v-if="active_group === group.group_slug" class="fa fa-folder-open"></i>
                                                </span>
                                                <span>{{group.group_name}}</span>
                                            </b>
                                        </div>
                                        <div :id="'group-blocks-'+group.group_slug" v-for="(block, index) in group.blocks" :key="index" v-show="active_group === group.group_slug">
                                            <div class="cj-p-0 cj-br-0 cj-mb-0 cj-relative">
                                                <div class="cj-absolute-tr cj-mt-20 cj-mr-20">
                                                    <div class="cj-field cj-has-addons">
                                                        <!--<div class="cj-control">
                                                            <a @click="addUiBlock(block)" class="cj-button cj-is-white cj-is-small">{{block.info.name}}</a>
                                                        </div>-->
                                                        <div class="cj-control">
                                                            <a @click="addUiBlock(block)" class="cj-button cj-is-success cj-is-small"><i class="fa fa-plus"></i></a>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div>
                                                    <div class="cj-lh-0"><img :src="block.info.screenshot" :alt="block.info.name"/></div>
                                                    <div class="cj-text-center cj-p-10 cj-bg-light cj-fs-14">{{block.post_title}}</div>
                                                </div>
                                            </div>
                                            <hr class="cj-mv-15 cj-opacity-50">
                                        </div>
                                    </div>
                                </div>
                                <!--/saved blocks-->
                                <!--saved blocks-->
                                <div v-if="ui_blocks_tab === 'cloud'">
                                    <div class="cj-box cj-p-10 cj-br-0 cj-mb-0">
                                        <div class="cj-field">
                                            <div class="cj-control" :class="{'cj-is-loading' : loading === 'searching'}">
                                                <input v-model="search_keyword" type="text" class="cj-input" placeholder="<?php _e( 'Search & download ui blocks..', 'wp-builder-locale' ) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="search_keyword === ''" class="cj-p-15 cj-br-0 cj-mb-0 cj-opacity-50">
										<?php _e( 'Type keywords to find and download ui blocks.', 'wp-builder-locale' ) ?>
                                    </div>
                                    <div v-if="search_keyword !== '' && cloud_blocks.total === 0" class="cj-p-15 cj-br-0 cj-mb-0 cj-opacity-50">
										<?php _e( 'Nothing found based on your query, try different keywords.', 'wp-builder-locale' ) ?>
                                    </div>
                                    <hr class="cj-m-0">
                                    <div v-if="cloud_blocks.posts !== undefined && cloud_blocks.posts.length > 0">
                                        <div class="cj-box cj-p-15 cj-br-0 cj-mb-0 cj-bg-light"
                                             v-for="(block, index) in cloud_blocks.posts"
                                             :key="index"
                                             v-if="installed_products[block._product_slug] === undefined"
                                        >
                                            <div class="cj-box cj-mb-0 cj-br-0 cj-p-0">
                                                <div class="thumb cj-relative">
                                                    <div class="cj-absolute-tr cj-m-10">
                                                        <!-- free product -->
                                                        <div class="cj-field cj-has-addons" v-if="block['_product_price'] === '0'">
                                                            <div class="cj-control" v-if="loading === 'downloading-'+block['_product_slug']">
                                                                <span class="cj-button cj-br-0 cj-is-dark cj-is-small">{{download_status}}</span>
                                                            </div>
                                                            <div class="cj-control">
                                                                <a @click="downloadUiBlock(block)" class="cj-button cj-br-0 cj-is-success cj-is-small" :class="{'cj-is-loading' : loading === 'downloading-'+block['_product_slug']}">
                                                                    <i class="fa fa-cloud-download-alt"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <!-- prermium product -->
                                                        <div class="cj-field cj-has-addons" v-if="block['_product_price'] !== '0' && premium_license_key.length > 10">
                                                            <div class="cj-control" v-if="loading === 'downloading-'+block['_product_slug']">
                                                                <span class="cj-button cj-br-0 cj-is-dark cj-is-small">{{download_status}}</span>
                                                            </div>
                                                            <div class="cj-control">
                                                                <a @click="downloadUiBlock(block)" class="cj-button cj-br-0 cj-is-success cj-is-small" :class="{'cj-is-loading' : loading === 'downloading-'+block['_product_slug']}">
                                                                    <i class="fa fa-cloud-download-alt"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <!-- get pro -->
                                                        <div class="cj-field cj-has-addons" v-if="block['_product_price'] !== '0' && premium_license_key.length < 10">
                                                            <div class="cj-control">
                                                                <a href="https://gum.co/wp-builder-pro" target="_blank" class="cj-button cj-br-0 cj-is-dark cj-is-small">
																	<?php _e( 'Get Pro', 'wp-builder-locale' ) ?>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <img :src="block.featured_image_url" :alt="block.post_title"/>
                                                </div>
                                            </div>
                                            <div class="cj-box cj-mb-0 cj-br-0 cj-p-0 cj-fs-14">
                                                <div class="cj-p-15 cj-bg-white cj-has-bg-white">
                                                    <div class="cj-mb-5"><b>{{block.post_title}}</b></div>
                                                    <div class="cj-mb-0 cj-lh-18">
                                                        <div class="cj-opacity-60">
                                                            {{block.post_excerpt}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/saved blocks-->
                            </div>
                            <div v-show="tab === 'settings'" class="editor-settings-form">
                                <template-settings-form @option_updated="settingsFieldChanged" :options="settings_form">
                                    <template v-slot:before_field="props">
                                        <label class="cj-label" v-html="props.item.label"></label>
                                    </template>
                                    <template v-slot:after_field="props">
                                        <div class="cj-help" v-html="props.item.info"></div>
                                    </template>
                                </template-settings-form>
                            </div>
                        </div>
                    </div>
                    <footer class="cj-quickview-footer cj-ph-10">
                        <div>
                            <a @click="publishTemplate" class="cj-button cj-is-small cj-is-primary" :class="{'cj-is-loading' : loading === 'publishing'}">
								<?php _e( 'Save & Publish', 'wp-builder-locale' ) ?>
                            </a>
                        </div>
                        <div>
                            <span v-if="template_info._cjwpbldr_is_draft === 1" class="cj-color-danger">
                                <?php _e( 'Draft', 'wp-builder-locale' ) ?>
                            </span>
                        </div>
                    </footer>
                </div>
                <div id="cj-template-editor-blocks" class="cj-template-editor-blocks">
                </div>
            </div>
        </div><!--vue app-->
    </div>
    <div id="cjwpbldr-template-block-added"></div>
    <input id="cjwpbldr-edit-template-screen-url" type="hidden" value="<?php echo admin_url( 'edit.php?post_type=cjwpbldr-templates' ) ?>">
    <input id="cjwpbldr-edit-template-id" type="hidden" value="<?php echo $_GET['template-id'] ?>">
    <input id="cjwpbldr-pro-license-key" type="hidden" value="<?php echo get_transient( '_wp_builder_pro_key' ) ?>">
<?php require_once sprintf( '%s/previews/footer.php', dirname( __FILE__ ) ); ?>