<?php
if( $this->helpers->isTheme() ) {
    $framework_name = $this->helpers->config( 'plugin-info', 'theme_name' );
} else {
    $framework_name = $this->helpers->config( 'plugin-info', 'name' );
}
$install_path = '<code>' . str_replace( ABSPATH, '/', $this->helpers->root_dir ) . '</code>';
$api_token_refresh_url = $this->helpers->queryString( $this->helpers->callbackUrl( 'core-welcome' ) ) . 'cjwpbldr-action=regenerate-token';
if( isset( $_GET['cjwpbldr-action'] ) && $_GET['cjwpbldr-action'] == 'regenerate-token' ) {
    delete_option( 'cjwpbldr_api_token' );
    wp_redirect($this->helpers->callbackUrl( 'core-welcome' ));
    die();
}
$api_token = get_option( 'cjwpbldr_api_token', false );
if( ! $api_token ) {
    $api_token = $this->helpers->generateToken();
    update_option( 'cjwpbldr_api_token', $api_token );
}

$api_token_html = '<div class="cj-field cj-has-addons">';
$api_token_html .= '<div class="cj-control">';
$api_token_html .= '<a class="cj-button cj-is-small" data-clipboard-text="' . $api_token . '"><i class="fa fa-copy"></i></a>';
$api_token_html .= '</div>';
$api_token_html .= '<div class="cj-control">';
$api_token_html .= '<input type="password" class="cj-input cj-is-small" value="' . $api_token . '" style="min-width: 250px">';
$api_token_html .= '</div>';
$api_token_html .= '<div class="cj-control">';
$api_token_html .= '<a href="'.$api_token_refresh_url.'" class="cj-button cj-is-small">' . __( 'Regenerate', 'lang-textdomain' ) . '</a>';
$api_token_html .= '</div>';
$api_token_html .= '</div>';

?>
<div class="cj-columns cj-is-multiline cj-is-mobile">
    <div class="cj-column cj-is-12-fullhd cj-is-12-widescreen cj-is-12-desktop cj-is-12-tablet cj-is-12-mobile">
        <?php
        $framework_links[] = '<a target="_blank" href="' . $this->helpers->config( 'plugin-info', 'docs_url' ) . '">' . __( 'Documentation', 'wp-builder-locale' ) . '</a>';
        $framework_links[] = '<a target="_blank" href="' . $this->helpers->config( 'plugin-info', 'faqs_url' ) . '">' . __( 'FAQs', 'wp-builder-locale' ) . '</a>';
        $framework_links[] = '<a target="_blank" href="' . $this->helpers->config( 'plugin-info', 'support_url' ) . '">' . __( 'Help & Support', 'wp-builder-locale' ) . '</a>';
        $framework_links[] = '<a target="_blank" href="' . $this->helpers->config( 'plugin-info', 'hire_us_url' ) . '">' . __( 'Hire Us', 'wp-builder-locale' ) . '</a>';
        $framework_links[] = '<a target="_blank" href="' . $this->helpers->config( 'plugin-info', 'twitter_url' ) . '">' . __( 'Twitter', 'wp-builder-locale' ) . '</a>';
        $framework_links[] = '<a target="_blank" href="' . $this->helpers->config( 'plugin-info', 'facebook_url' ) . '">' . __( 'Facebook', 'wp-builder-locale' ) . '</a>';
        $framework_form_fields = [
            [
                'id' => 'framework-title',
                'type' => 'sub-heading',
                'info' => '',
                'params' => [],
                'default' => $framework_name,
                'options' => '', // array in case of dropdown, checkbox and radio buttons
                'quick-searchable' => false
            ],
            [
                'id' => 'framework-version',
                'label' => __( 'Framework Version', 'wp-builder-locale' ),
                'type' => 'info',
                'info' => '',
                'default' => CJWPBLDR_VERSION,
                'options' => '', // array in case of dropdown, checkbox and radio buttons
                'quick-searchable' => false
            ],
            [
                'id' => 'framework-install-path',
                'label' => __( 'Framework Path', 'wp-builder-locale' ),
                'type' => 'info',
                'info' => '',
                'default' => $install_path,
                'options' => '', // array in case of dropdown, checkbox and radio buttons
                'quick-searchable' => false
            ],
            [
                'id' => 'framework-links',
                'label' => __( 'Useful Links', 'wp-builder-locale' ),
                'type' => 'info',
                'info' => '',
                'default' => implode( '<span class="cj-p-10 cj-opacity-50">|</span>', $framework_links ),
                'options' => '', // array in case of dropdown, checkbox and radio buttons
                'quick-searchable' => false
            ],
            [
                'id' => 'api-token',
                'label' => __( 'REST API Personal Token', 'wp-builder-locale' ),
                'type' => (current_user_can( 'manage_options' )) ? 'info' : 'hidden',
                'info' => __( 'This token has full access to all routes so be careful.', 'wp-builder-locale' ),
                'default' => $api_token_html,
                'options' => '', // array in case of dropdown, checkbox and radio buttons
                'quick-searchable' => false
            ],
        ];
        echo $this->helpers->renderAdminForm( $framework_form_fields );
        ?>
    </div>
</div>
<?php
$tab = (isset( $_GET['show'] ) && $_GET['show'] != '') ? $_GET['show'] : 'installed';
?>
<div id="cjwpbldr-cloud-app" v-cloak>
    <cjwpbldr-cloud show_tab="<?php echo $tab; ?>"></cjwpbldr-cloud>
</div>
