<?php
if (!class_exists('cjwpbldr_custom_data')) {
	class cjwpbldr_custom_data
	{
		private static $instance;
		protected $custom_data_key;
		public $helpers;

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
			$this->custom_data_key = '_cjwpbldr_data_';
		}

		public function getCustomData($key = '', $index = '')
		{
			if ($key != '' && $index == '') {
				$option_key_name = '_cjwpbldr_data_' . $key;
				$saved_data = get_option($option_key_name, []);

				return $saved_data;
			}

			if ($key != '' && $index != '') {
				$option_key_name = '_cjwpbldr_data_' . $key;
				$saved_data = get_option($option_key_name, []);

				return (isset($saved_data[$index])) ? $saved_data[$index] : [];
			}

			$all_data = $this->helpers->dbSelect('options', '*', ' option_name LIKE "%_cjwpbldr_data_%"');
			if (!empty($all_data)) {
				$return = [];
				foreach ($all_data as $key => $datum) {
					$data_key = str_replace($this->custom_data_key, '', $datum['option_name']);
					$return[$data_key] = @unserialize($datum['option_value']);
				}

				return $return;
			}

			return [];
		}

		public function addCustomData($key, $data)
		{
			$option_key_name = '_cjwpbldr_data_' . $key;
			if (get_option($option_key_name) != '') {
				$previous_option = get_option($option_key_name);
				if (in_array($data, $previous_option)) {
					return $previous_option;
				}
				array_push($previous_option, $data);
				$saved_data = update_option($option_key_name, $previous_option);
			} else {
				$data = (array) $data;
				$saved_data = add_option($option_key_name, $data);
			}

			return $saved_data;
		}

		public function deleteCustomData($key, $index = '')
		{
			$option_key_name = '_cjwpbldr_data_' . $key;
			$saved_data = $this->getCustomData($key);

			if ($index != '' && isset($saved_data[$index])) {
				unset($saved_data[$index]);
				update_option($option_key_name, $saved_data);
			}
			if ($index == '') {
				delete_option($option_key_name);
			}
			$saved_data = $this->getCustomData($key);

			return $saved_data;
		}

		public function createDataKey($data)
		{
			$sha_key = (is_array($data)) ? json_encode($data) : $data;

			return sha1($sha_key);
		}
	}

	cjwpbldr_custom_data::getInstance();
}
