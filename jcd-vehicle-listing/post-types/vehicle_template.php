<?php
if (!class_exists('vehiclePostTemplate')) {
	/**
	* A vehiclePostTemplate class that provides additional fields
	*/
	class vehiclePostTemplate {
		const POST_TYPE = 'vehicle';
		private $_meta = array(
			'vin-number',
			'mileage',
			'model',
			'year',
			'price',
			'stock'
		);

		/**
		* The Constructor
		*/
		public function __construct() {
			// Register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
		} // END public function __construct()

		/**
		* Hook into WordPress' init action hook
		*/
		public function init() {
			// Initialize post type
			$this->create_post_type();
			add_action('save_post', array(&$this, 'save_post'));
		} // END public funtion init()

		/**
		* Create the post type
		*/
		public function create_post_type() {
			register_post_type(self::POST_TYPE, array(
				'labels' => array(
					'name' => __(sprintf('%ss', ucwords('dealer post'))),
					'singular_name' => __(ucwords('dealer post'))
				),
				'menu_icon' => plugins_url( 'images/'.self::POST_TYPE.'_icon.png' , dirname(__FILE__)),
				'menu_position' => 5,
				'public' => true,
				'taxonomies' => array('makes','types'),
				'has_archive' => true,
				'description' => __("This is a vehicle post type with information about the vehicle"),
				'supports' => array(
					'title', 'editor', 'author', 'thumbnail', 'excerpt'
				),
			));
		}

		/**
		* Save the metaboxes for this custom post type
		*/
		public function save_post($post_id) {
			// Verify if this is an auto save routine
			// If it is our form has not been submited, so we don't want to do anything
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id)) {
				foreach($this->_meta as $field_name) {
					// Update the post's meta field
					update_post_meta($post_id, $field_name, $_POST[$field_name]);
				}
			} else {
				return;
			} // if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
		} // END public function save_post($post_id)

		/**
		* Hook into WordPress' admin_init action hook
		*/
		public function admin_init() {
			// Add metaboxes
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
		} // END public function admin_init()

		/**
		* Hook into WordPress add_meta_boxes action hook
		*/
		public function add_meta_boxes() {
			// Add this metabox to every selected post
			add_meta_box(
				sprintf('wp_plugin_template_%s_section', self::POST_TYPE),
				sprintf('%s Information', ucwords(str_replace("_", " ", self::POST_TYPE))),
				array(&$this, 'add_inner_meta_boxes'),
				self::POST_TYPE,
				'side',
				'high'
			);
		} // END public function add_meta_boxes()

		/**
		* Called off of the add meta box
		*/
		public function add_inner_meta_boxes($post) {
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));
		} // END public function add_inner_meta_boxes($post)

	} // END class vehiclePostTemplate
} // END if (!class_exists('vehiclePostTemplate'))