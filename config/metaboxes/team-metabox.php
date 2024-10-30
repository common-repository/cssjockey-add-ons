<?php
if( ! class_exists( 'cjwpbldr_team_metabox' ) ) {
	class cjwpbldr_team_metabox {
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
			$prefix = '_team_';
			$cmb = new_cmb2_box( array(
				'id' => 'cjwpbldr_team_metabox',
				'title' => $metabox_title,
				'object_types' => array('wpb-team'), // Post type
				'context' => 'advanced',
				'priority' => 'high',
				'show_names' => true, // Show field names on the left
				'cmb_styles' => false, // false to disable the CMB stylesheet
				'classes' => 'cssjockey-ui cj-bg-white', // false to disable the CMB stylesheet
				// 'closed'     => true, // Keep the metabox closed by default
			) );

			//Designation
			$cmb->add_field( array(
				'name' => __( 'Designation', 'wp-builder-locale' ),
				'id' => $prefix . 'designation',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				)
			) );

			//Skills
			$cmb->add_field( array(
				'name' => __( 'Skills', 'wp-builder-locale' ),
				'desc' => '',
				'id' => $prefix . 'skills',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input cj-select',
				),
				'repeatable' => true
			) );

			$group_field_id = $cmb->add_field( array(
				'id' => $prefix . 'social_media',
				'type' => 'group',
				'description' => __( 'Add Social Media Icons', 'cmb2' ),
				// 'repeatable'  => false, // use false if you want non-repeatable group
				'options' => array(
					'group_title' => __( 'Icon {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
					'add_button' => __( 'Add Another Icon', 'cmb2' ),
					'remove_button' => __( 'Remove Icon', 'cmb2' ),
					'sortable' => false,
					'closed' => true, // true to have the groups closed by default
					// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
				),
			) );

			$cmb->add_group_field( $group_field_id, array(
				'name' => 'Icon',
				'id' => 'icon',
				'type' => 'select',
				'options' => $this->helpers->fontAwesomeIconsArray()
			) );

			$cmb->add_group_field( $group_field_id, array(
				'name' => 'Link',
				'id' => 'link',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input cj-select',
				),
			) );
		}
	}

	cjwpbldr_team_metabox::getInstance();
}