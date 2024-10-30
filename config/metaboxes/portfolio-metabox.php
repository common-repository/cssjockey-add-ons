<?php
if( ! class_exists( 'cjwpbldr_portfolio_metabox' ) ) {
	class cjwpbldr_portfolio_metabox {
		private static $instance;
		public $helpers, $post_id;

		public static function getInstance() {
			if( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			$this->helpers = cjwpbldr_helpers::getInstance();
			add_action( 'cmb2_init', array($this, 'metabox') );
			$this->post_id = (isset( $_GET['post'] )) ? $_GET['post'] : '';
		}

		public function metabox() {
			$metabox_title = __( 'Additional Information', 'wp-builder-locale' );
			$prefix = '_portfolio_';
			$cmb = new_cmb2_box( array(
				'id' => 'cjwpbldr_portfolio_metabox',
				'title' => $metabox_title,
				'object_types' => array('wpb-portfolio'), // Post type
				'context' => 'advanced',
				'priority' => 'high',
				'show_names' => true, // Show field names on the left
				'cmb_styles' => false, // false to disable the CMB stylesheet
				'classes' => 'cssjockey-ui cj-bg-white', // false to disable the CMB stylesheet
				// 'closed'     => true, // Keep the metabox closed by default
			) );

			//Video
			$cmb->add_field( array(
				'name' => __( 'Video (iframe)', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'video',
				'type' => 'textarea',
				'attributes' => array(
					'class' => 'cj-textarea',
				),
				'sanitization_cb' => false,
			) );

			//Video
			$cmb->add_field( array(
				'name' => __( 'Gallery', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'gallery',
				'type' => 'file_list',
			) );

			//Live URL
			$cmb->add_field( array(
				'name' => __( 'Live Project URL', 'wp-builder-locale' ),
				'desc' => __( 'Add URL to Live Project or to a project demo.', 'wp-builder-locale' ),
				'id' => $prefix . 'live_url',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				)
			) );

			//Document
			$cmb->add_field( array(
				'name' => __( 'Document', 'wp-builder-locale' ),
				'desc' => __( 'Upload a document for the users to download.', 'wp-builder-locale' ),
				'id' => $prefix . 'document',
				'type' => 'file',
			) );

			//Client Name
			$cmb->add_field( array(
				'name' => __( 'Client Name', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'client',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				),
			) );

			//Duration
			$cmb->add_field( array(
				'name' => __( 'Project Duration', 'wp-builder-locale' ),
				'desc' => __( 'Specify the completion period of this project. For e:g 6 Months or 1 Year', 'wp-builder-locale' ),
				'id' => $prefix . 'duration',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				),
			) );

			//Date
			$cmb->add_field( array(
				'name' => __( 'Completion Date', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'completion_date',
				'type' => 'text_date',
				'attributes' => array(
					'class' => 'cj-input',
				),
			) );

			//Technologies
			$cmb->add_field( array(
				'name' => __( 'Technologies', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'technologies',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				),
				'repeatable' => true,
			) );

			//Skills
			$cmb->add_field( array(
				'name' => __( 'Skills', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'skills',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				),
				'repeatable' => true,
			) );
		}
	}

	cjwpbldr_portfolio_metabox::getInstance();
}