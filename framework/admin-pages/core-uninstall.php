<?php
$restore_settings_url = $this->helpers->queryString( $this->helpers->callbackUrl( 'core-uninstall' ) ) . 'cjwpbldr_action=restore-settings';
$remove_addons_url = $this->helpers->queryString( $this->helpers->callbackUrl( 'core-uninstall' ) ) . 'cjwpbldr_action=remove-all-addons';
$remove_blocks_url = $this->helpers->queryString( $this->helpers->callbackUrl( 'core-uninstall' ) ) . 'cjwpbldr_action=remove-all-blocks';
$remove_templates_url = $this->helpers->queryString( $this->helpers->callbackUrl( 'core-uninstall' ) ) . 'cjwpbldr_action=remove-all-templates';
$form_fields = array(
	array(
		'id' => 'uninstall-heading',
		'type' => 'sub-heading',
		'label' => __( 'Uninstall', 'wp-builder-locale' ),
		'info' => '',
		'params' => array(),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'id' => 'uninstall-settings',
		'type' => 'info-full',
		'label' => '<a href="' . $restore_settings_url . '" class="cj-confirm" data-title="' . __( 'Are you sure?', 'wp-builder-locale' ) . '" data-message="' . __( 'All settings will be restored to default values and you will have to configure all settings again.', 'wp-builder-locale' ) . '">' . __( 'Restore Settings', 'wp-builder-locale' ) . '</a>',
		'info' => '',
		'params' => array(),
		'default' => __( 'This will reset all settings saved by the framework and installed add-ons to the default values.', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'id' => 'uninstall-addons',
		'type' => 'info-full',
		'label' => '<a href="' . $remove_addons_url . '" class="cj-confirm" data-title="' . __( 'Are you sure?', 'wp-builder-locale' ) . '" data-message="' . __( 'All addons and their settings will be removed from this website.', 'wp-builder-locale' ) . '">' . __( 'Uninstall all Add-ons', 'wp-builder-locale' ) . '</a>',
		'info' => '',
		'params' => array(),
		'default' => __( 'This will remove all installed add-ons from this website.', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'id' => 'uninstall-blocks',
		'type' => 'info-full',
		'label' => '<a href="' . $remove_blocks_url . '" class="cj-confirm" data-title="' . __( 'Are you sure?', 'wp-builder-locale' ) . '" data-message="' . __( 'All ui blocks will be removed from this website.', 'wp-builder-locale' ) . '">' . __( 'Uninstall all UI Blocks', 'wp-builder-locale' ) . '</a>',
		'info' => '',
		'params' => array(),
		'default' => __( 'This will remove all installed ui blocks from this website.', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	),
	array(
		'id' => 'uninstall-templates',
		'type' => 'info-full',
		'label' => '<a href="' . $remove_templates_url . '" class="cj-confirm" data-title="' . __( 'Are you sure?', 'wp-builder-locale' ) . '" data-message="' . __( 'All templates will be removed from this website.', 'wp-builder-locale' ) . '">' . __( 'Uninstall all Templates', 'wp-builder-locale' ) . '</a>',
		'info' => '',
		'params' => array(),
		'default' => __( 'This will remove all installed ui blocks from this website.', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	)
);

echo $this->helpers->renderAdminForm( $form_fields );