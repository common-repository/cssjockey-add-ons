<div id="cjwpbldr-admin-footer-hooks"><!-- DO NOT REMOVE THIS DIV TAG --></div>

<?php
$current_screen = get_current_screen();
// edit template button
if( $current_screen->base = 'post' && isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['post'] != '' ) {
	$template_info = $this->helpers->getTemplateInfoByPostId( $_GET['post'] );
	if( ! empty( $template_info ) ) {
		$edit_template_url = admin_url( 'post.php?post=' . $template_info['ID'] . '&action=edit' );
		$template_preview_url = $this->helpers->queryString( get_permalink( $_GET['post'] ) ) . 'disable-on-edit=1';
		$template_preview_url = $this->helpers->queryString( admin_url() ) . 'cjwpbldr-action=edit-template&hide-settings=1&template-id=' . $template_info['ID'];
		ob_start();
		echo '<input id="cjwpbldr-post-template-id" type="hidden" value="' . $edit_template_url . '" />';
		echo '<input id="cjwpbldr-post-template-preview-url" type="hidden" value="' . $template_preview_url . '" />';
		echo '<script src="' . sprintf( '%s/lib/js/post-edit-template-ui.js', $this->helpers->framework_url ) . '"></script>';
		echo '<style type="text/css">.edit-post-visual-editor.editor-styles-wrapper{padding: 0; margin: 0; line-height:0;}</style>';
		echo ob_get_clean();
	}

	if( isset( $_GET['highlight'] ) && $_GET['highlight'] == 'assign-template' ) {
		?>
        <script>
          jQuery(document).ready(function ($) {
            $(window).load(function () {
              setTimeout(function () {
                $('#cj_template_.cmb2-postbox').parent().addClass('cssjockey-ui')
                $('#cj_template_.cmb2-postbox').removeClass('closed')
                $('#cj_template_.cmb2-postbox').addClass('cj-bg-dark cj-has-bg-dark animated tada')
              }, 1000)
              setTimeout(function () {
                $('#cj_template_.cmb2-postbox').parent().removeClass('cssjockey-ui')
                $('#cj_template_.cmb2-postbox').removeClass('cj-bg-dark cj-has-bg-dark animated tada')
              }, 4000)
            })
          })
        </script>
		<?php
	}
}
?>