<?php
return [
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'strings' => [
		'are_you_sure' => __( 'Are you sure?', 'wp-builder-locale' ),
		'this_cant_be_undone' => __( 'This cannot be undone!', 'wp-builder-locale' ),
		'yes' => __( 'Yes', 'wp-builder-locale' ),
		'no' => __( 'No', 'wp-builder-locale' ),
		'cancel' => __( 'Cancel', 'wp-builder-locale' ),
	],
	'swal' => [
		'title_error' => __( 'Error!', 'wp-builder-locale' ),
		'title_success' => __( 'Success!', 'wp-builder-locale' ),
	],
	'vue_auth_forms' => [
		'global' => [
			'input_class' => 'cj-input cj-is-medium',
			'button_class' => 'cj-button cj-is-primary cj-is-medium',
			'button_link_class' => 'cj-button cj-is-link cj-mt-6',
		],
		'login' => [
			'input_user' => [
				'label' => __( 'Username or Email address', 'wp-builder-locale' ),
				'placeholder' => __( 'Username or Email address', 'wp-builder-locale' ),
			],
			'input_password' => [
				'label' => __( 'Password', 'wp-builder-locale' ),
				'placeholder' => __( 'Password', 'wp-builder-locale' ),
			],
			'submit_button' => [
				'text' => __( 'Login', 'wp-builder-locale' ),
			],
			'forgot_password_link' => [
				'text' => __( 'Forgot Password?', 'wp-builder-locale' ),
			],
			'sign_up_link' => [
				'text' => __( 'New User? Sign Up', 'wp-builder-locale' ),
			]
		],
		'register' => [
			'input_first_name' => [
				'label' => __( 'First name', 'wp-builder-locale' ),
				'placeholder' => __( 'First name', 'wp-builder-locale' ),
			],
			'input_last_name' => [
				'label' => __( 'Last name', 'wp-builder-locale' ),
				'placeholder' => __( 'Last name', 'wp-builder-locale' ),
			],
			'input_user_login' => [
				'label' => __( 'Choose a username', 'wp-builder-locale' ),
				'placeholder' => __( 'Choose a username', 'wp-builder-locale' ),
			],
			'input_user_email' => [
				'label' => __( 'Your email address', 'wp-builder-locale' ),
				'placeholder' => __( 'Your email address', 'wp-builder-locale' ),
			],
			'input_user_password' => [
				'label' => __( 'Password', 'wp-builder-locale' ),
				'placeholder' => __( 'Password', 'wp-builder-locale' ),
			],
			'input_user_password_confirmation' => [
				'label' => __( 'Confirm password', 'wp-builder-locale' ),
				'placeholder' => __( 'Confirm password', 'wp-builder-locale' ),
			],
			'submit_button' => [
				'text' => __( 'Create Account', 'wp-builder-locale' ),
			],
			'sign_in_link' => [
				'text' => __( 'Have an account? Sign In', 'wp-builder-locale' ),
			]
		],
		'password' => [
			'input_user' => [
				'label' => __( 'Username or Email address', 'wp-builder-locale' ),
				'placeholder' => __( 'Username or Email address', 'wp-builder-locale' ),
			],
			'submit_button' => [
				'text' => __( 'Submit', 'wp-builder-locale' ),
			],
			'sign_in_link' => [
				'text' => __( 'Have an account? Sign In', 'wp-builder-locale' ),
			],
			'sign_up_link' => [
				'text' => __( 'New User? Sign Up', 'wp-builder-locale' ),
			]
		],
		'reset_password' => [
			'input_user_pass' => [
				'label' => __( 'New password', 'wp-builder-locale' ),
				'placeholder' => __( 'New password', 'wp-builder-locale' ),
			],
			'input_user_pass_confirmation' => [
				'label' => __( 'Type again', 'wp-builder-locale' ),
				'placeholder' => __( 'Type again', 'wp-builder-locale' ),
			],
			'submit_button' => [
				'text' => __( 'Reset Password', 'wp-builder-locale' ),
			],
			'cancel_link' => [
				'text' => __( 'Cancel', 'wp-builder-locale' ),
			]
		],
	]

];