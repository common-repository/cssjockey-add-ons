<?php
if (!class_exists('cjwpbldr_testimonials_metabox')) {
	class cjwpbldr_testimonials_metabox
	{
		private static $instance;
		public $helpers, $post_id;

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
			add_action('cmb2_init', array($this, 'metabox'));
			$this->post_id = (isset($_GET['post'])) ? $_GET['post'] : '';
		}

		public function metabox()
		{
			$metabox_title = __('Additional Information', 'wp-builder-locale');
			$prefix = '_testimonial_';
			$cmb = new_cmb2_box(array(
				'id' => 'cjwpbldr_testimonials_metabox',
				'title' => $metabox_title,
				'object_types' => array('wpb-testimonials'), // Post type
				'context' => 'advanced',
				'priority' => 'high',
				'show_names' => true, // Show field names on the left
				'cmb_styles' => false, // false to disable the CMB stylesheet
				'classes' => 'cssjockey-ui cj-bg-white', // false to disable the CMB stylesheet
				// 'closed'     => true, // Keep the metabox closed by default
			));

			//Star Rating
			$cmb->add_field(array(
				'name' => __('Star Rating', 'wp-builder-locale'),
				'id' => $prefix . 'star_rating',
				'type' => 'select',
				'options' => ['0' => 'none', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'],
				'attributes' => [
					'class' => 'cj-input'
				]
			));

			//Designation
			$cmb->add_field(array(
				'name' => __('Designation', 'wp-builder-locale'),
				'id' => $prefix . 'designation',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				)
			));

			//Company
			$cmb->add_field(array(
				'name' => __('Company', 'wp-builder-locale'),
				'desc' => '',
				'id' => $prefix . 'company',
				'type' => 'text',
				'attributes' => array(
					'class' => 'cj-input',
				),
			));

			//Logo
			$cmb->add_field(array(
				'name' => __('Logo', 'wp-builder-locale'),
				'desc' => '',
				'id' => $prefix . 'logo',
				'type' => 'file',
			));

			// Social Icons
			$group_field_id = $cmb->add_field(array(
				'id'          => $prefix . 'social_icons',
				'type'        => 'group',
				'description' => __('Social Icons', 'cmb2'),
				// 'repeatable'  => false, // use false if you want non-repeatable group
				'options'     => array(
					'group_title'       => __('Social Icon {#}', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
					'add_button'        => __('Add Another Social Icon', 'cmb2'),
					'remove_button'     => __('Remove Social Icon', 'cmb2'),
					'sortable'          => true,
					'closed'         => true, // true to have the groups closed by default
					// 'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'cmb2' ), // Performs confirmation before removing group.
				),
			));

			$cmb->add_group_field($group_field_id, array(
				'name' => 'Icon',
				'id'   => 'icon',
				'type' => 'select',
				'options' => $this->helpers->fontAwesomeIconsArray(),
				'default' => 'fas fa-facebook',
				'attributes' => [
					'class' => 'cj-input'
				]
				// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
			));

			$cmb->add_group_field($group_field_id, array(
				'name' => 'Link',
				'id'   => 'link',
				'type' => 'text',
				'default' => '#',
				'attributes' => [
					'class' => 'cj-input'
				]
				// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
			));
		}
	}

	cjwpbldr_testimonials_metabox::getInstance();
}
