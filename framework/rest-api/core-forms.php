<?php
if (!class_exists('cjwpbldr_api_core_forms')) {
	class cjwpbldr_api_core_forms
	{

		public $helpers, $addon_dir, $routes, $api_url;
		protected $custom_data_class;

		private static $instance;

		public static function getInstance()
		{
			if (!isset(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct()
		{
			$this->helpers = cjwpbldr_helpers::getInstance();
			$this->api_url = rest_url('cjwpbldr') . '/';
			$this->custom_data_class = cjwpbldr_custom_data::getInstance();
			$this->routes = [
				'core/forms/subscribe' => [
					'endpoint' => 'core/forms/subscribe',
					'name' => __('Product Info', 'wp-builder-locale'),
					'methods' => [
						'post' => [$this, 'processEmailSubscribeForm'],
					],
					'permissions' => function ($request) {
						return true;
						//return $this->helpers->checkApiRoutePermissions( $request, '' );
					},
				],
				'core/forms/contact' => [
					'endpoint' => 'core/forms/contact',
					'name' => __('Process Contact Form', 'wp-builder-locale'),
					'description' => '',
					'methods' => [
						'post' => [$this, 'processContactForm'], // callback function
					],
					'permissions' => function ($request) {
						return $this->helpers->checkApiRoutePermissions($request, '');
					},
				],
			];
			add_filter('cjwpbldr_register_api_route', [$this, 'registerRoute']);
		}

		public function registerRoute($routes)
		{
			$routes = array_merge($routes, $this->routes);

			return $routes;
		}

		public function processEmailSubscribeForm($request)
		{
			$post_data = $this->helpers->apiRequestParams($request);

			if (!isset($post_data['email_address'])) {
				return $this->helpers->apiError($request, 406, __('The email address field is required.', 'wp-builder-locale'), 406);
			}
			if (isset($post_data['email_address']) && !$this->helpers->isValidEmail($post_data['email_address'])) {
				return $this->helpers->apiError($request, 406, __('You must provide a valid email address', 'wp-builder-locale'), 406);
			}

			$custom_data_class = cjwpbldr_custom_data::getInstance();
			$custom_data_class->addCustomData('email_subscribers', $post_data['email_address']);
			$data['success'] = __('Thank You, ', 'wp-builder-locale');

			return $this->helpers->apiResponse($request, $data);
		}

		public function processContactForm($request)
		{
			$post_data = $this->helpers->apiRequestParams($request);
			$errors = [];
			$required_fields = [
				'first_name',
				'last_name',
				'email_address',
				'message',
			];

			foreach ($required_fields as $key => $field) {
				if (!array_key_exists($field, $post_data)) {
					$errors[$field] = sprintf(__('You must specify %s.', 'wp-builder-locale'), $field);
				}
				if (array_key_exists($field, $post_data) && $post_data[$field] == '') {
					$errors[$field] = sprintf(__('%s field is required.', 'wp-builder-locale'), ucwords(str_replace('_', ' ', $field)));
				}
			}
			if (!empty($errors)) {
				return $this->helpers->apiError($request, 406, $errors, 406, $post_data);
			}

			if (isset($post_data['email_address']) && !$this->helpers->isValidEmail($post_data['email_address'])) {
				$errors['email_address'] = __('Email address is not valid.', 'wp-builder-locale');

				return $this->helpers->apiError($request, 406, $errors, 406, $post_data);
			}

			$from_name = $post_data['first_name'] . ' ' . $post_data['last_name'];
			$message = nl2br($post_data['message']);
			$to_email = (array_key_exists('to_email', $post_data) && $post_data['to_email'] != '') ? $post_data['to_email'] : get_option('admin_email');
			$email_subject = (array_key_exists('email_subject', $post_data) && $post_data['email_subject'] != '') ? $post_data['email_subject'] : sprintf(__('Contact via %s!', 'wp-builder-locale'), get_bloginfo('name'));

			$email_data = [
				'to' => $to_email,
				'from_name' => $from_name,
				'from_email' => $post_data['email_address'],
				'subject' => $email_subject,
				'message' => $message,
			];
			$headers[] = 'Reply-To: ' . $from_name . ' <' . $post_data['email_address'] . '>';

			$this->helpers->sendEmail($email_data, [], 'wp-mail', $headers);
			$return['success'] = __('<b>Thank You!</b><br>We have received your message and will get back to you as soon as possible.', 'wp-builder-locale');

			if (isset($post_data['post_url']) && $post_data['post_url'] != '') {
				$this->helpers->wpRemotePost($post_data['post_url'], $email_data);
				$return['post_sent'] = $post_data;
			}

			return $this->helpers->apiResponse($request, $return);
		}
	}

	cjwpbldr_api_core_forms::getInstance();
}
