<?php
$framework_features = [
	'blocks' => 'UI Blocks',
	'builder' => 'Site Builder',
	'portfolio' => 'Portfolio',
	'team' => 'Team',
	'faqs' => 'FAQs',
	'testimonials' => 'Testimonials',
];

return [
	[
		'type' => 'heading',
		'id' => 'core_global_config_heading',
		'label' => __( 'Global Settings', 'wp-builder-locale' ),
		'info' => '',
		'default' => __( 'Global Settings', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'multi-dropdown',
		'id' => 'core_features',
		'label' => __( 'Framework Features', 'wp-builder-locale' ),
		'info' => __( 'Select features you would like to enable.', 'wp-builder-locale' ),
		'default' => ($this->helpers->isTheme()) ? ['blocks', 'builder', 'portfolio', 'team', 'faqs', 'testimonials'] : ['blocks', 'builder'],
		'options' => $framework_features, // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'roles',
		'id' => 'core_admin_bar',
		'label' => __( 'Disable Admin Bar for', 'wp-builder-locale' ),
		'info' => __( 'You can choose to disable admin bar for specified roles.', 'wp-builder-locale' ),
		'default' => 'everyone',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'dropdown',
		'id' => 'core_combine_assets',
		'label' => __( 'Combine CSS/JS files', 'wp-builder-locale' ),
		'info' => __( 'If Yes, then all css and js files required for the framework and installed add-ons will be combined into one file.', 'wp-builder-locale' ),
		'default' => 'no',
		'options' => $this->helpers->arrays( 'yes-no' ), // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'sub-heading',
		'id' => 'core_wp_email_settings',
		'label' => '',
		'info' => '',
		'default' => __( 'WordPress Email Settings', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'email',
		'id' => 'core_from_email_address',
		'label' => __( 'From email address', 'wp-builder-locale' ),
		'info' => __( 'If specified, all WordPress emails will be sent via this address.', 'wp-builder-locale' ),
		'default' => get_option( 'admin_email' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'text',
		'id' => 'core_from_email_name',
		'label' => __( 'From email name', 'wp-builder-locale' ),
		'info' => __( 'If specified, all WordPress emails will be sent with this name in the headers.', 'wp-builder-locale' ),
		'default' => get_bloginfo( 'name' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
];