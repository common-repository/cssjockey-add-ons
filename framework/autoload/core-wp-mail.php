<?php
	if( ! class_exists( 'cjwpbldr_core_wp_mail' ) ) {
	class cjwpbldr_core_wp_mail {
		private static $instance;
		public $helpers;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			add_filter( 'wp_mail_from', array($this, 'senderEmail') );
			add_filter( 'wp_mail_from_name', array($this, 'senderName') );
		}

		function senderEmail( $original_email_address ) {
			if( isset( $this->helpers->saved_options['core_from_email_address'] ) && $this->helpers->saved_options['core_from_email_address'] !== '' ) {
				return $this->helpers->saved_options['core_from_email_address'];
			}

			return $original_email_address;
		}

		function senderName( $original_email_from ) {
			if( isset( $this->helpers->saved_options['core_from_email_name'] ) && $this->helpers->saved_options['core_from_email_name'] !== '' ) {
				return $this->helpers->saved_options['core_from_email_name'];
			}

			return $original_email_from;
		}

	}

	cjwpbldr_core_wp_mail::getInstance();
}