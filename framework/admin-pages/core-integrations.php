<?php
return [
	[
		'type' => 'heading',
		'id' => 'core_integrations_config_heading',
		'label' => '',
		'info' => '',
		'default' => __( 'Third Party Integrations', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'info-full',
		'id' => 'core_integrations_config_info',
		'label' => '',
		'info' => '',
		'default' => __( 'Here you can specify third party configuration which will be used by different WP Builder features.', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'sub-heading',
		'id' => 'core_google_heading',
		'label' => '',
		'info' => '',
		'default' => __( 'Google Services', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'text',
		'id' => 'core_google_maps_key',
		'label' => __( 'Google API Key', 'wp-builder-locale' ),
		'info' => __( 'Leave this field blank if you theme or any other plugin adds google maps script and you see a conflict in browser console.', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'sub-heading',
		'id' => 'core_google_recaptcha_heading',
		'label' => __( 'Global reCaptcha (v2)', 'wp-builder-locale' ),
		'info' => __( 'Leave these fields blank if you theme or any other plugin adds google recaptcha script and you see a conflict in browser console.', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'text',
		'id' => 'core_google_recaptcha_site_key',
		'label' => __( 'Site Key', 'wp-builder-locale' ),
		'info' => sprintf( __( '<a href="%s" tabindex="-1" target="_blank">Click here</a> to get reCaptcha keys.', 'wp-builder-locale' ), 'https://www.google.com/recaptcha/intro/' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'text',
		'id' => 'core_google_recaptcha_site_secret',
		'label' => __( 'Site Secret', 'wp-builder-locale' ),
		'info' => sprintf( __( '<a href="%s" tabindex="-1" target="_blank">Click here</a> to get reCaptcha keys.', 'wp-builder-locale' ), 'https://www.google.com/recaptcha/intro/' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'id' => 'core_google_recaptcha_theme',
		'type' => 'dropdown',
		'label' => __( 'Theme', 'wp-builder-locale' ),
		'info' => 'Select reCAPTCHA theme',
		'default' => 'light',
		'options' => ['light' => __( 'Light', 'wp-builder-locale' ), 'dark' => __( 'Dark', 'wp-builder-locale' )], // array in case of dropdown, checkbox and radio buttons
	],
	[
		'id' => 'core_google_recaptcha_language',
		'type' => 'text',
		'label' => __( 'Language', 'wp-builder-locale' ),
		'info' => sprintf( __( 'Specify language, <a href="%s" target="_blank">Click here</a> for language options.', 'wp-builder-locale' ), 'https://developers.google.com/recaptcha/docs/language' ),
		'default' => 'en',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'hidden',
		'id' => 'core_unsplash_heading',
		'label' => '',
		'info' => '',
		'default' => __( 'Unsplash Services', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'hidden',
		'id' => 'core_unsplash_app_id',
		'label' => __( 'Unsplash App ID', 'wp-builder-locale' ),
		'info' => __( 'Specify Unsplash app id.', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'hidden',
		'id' => 'core_unsplash_secret',
		'label' => __( 'Unsplash App Secret', 'wp-builder-locale' ),
		'info' => __( 'Specify Unsplash app secret.', 'wp-builder-locale' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'sub-heading',
		'id' => 'core_pixabay_heading',
		'label' => '',
		'info' => '',
		'default' => __( 'Pixabay Images', 'wp-builder-locale' ),
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
	[
		'type' => 'text',
		'id' => 'core_pixabay_key',
		'label' => __( 'Pixabay Api Key', 'wp-builder-locale' ),
		'info' => sprintf( __( 'Specify Pixabay api key to enable pixabay key features. <a href="%s" target="_blank">Click here</a> to get API Key.', 'wp-builder-locale' ), 'https://pixabay.com/api/docs/#api_search_images' ),
		'default' => '',
		'options' => '', // array in case of dropdown, checkbox and radio buttons
	],
];