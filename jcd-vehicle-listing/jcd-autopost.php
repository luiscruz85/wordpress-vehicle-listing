<?php
/**
 * Plugin Name: Vehicle Listings
 * Plugin URI: http://jcruzdesign.com
 * Description: Add dealer and vehicle listings to WordPress blogs
 * Version: 1.0
 * Author: Luis Cruz
 * Author URI: http://jcruzdesign.com
 * License: GPL2
 */

/*  Copyright 2013  Luis Cruz  (email : luis@jcruzdesign.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



if (!class_exists('JCD_Autopost')) {
	class JCD_Autopost {
		/**
		* Construct plugin object
		*/
		public function __construct() {
			// register actions
			add_action('init', array(&$this, 'create_taxonomy'));
			add_action('admin_init', array(&$this, 'remove_dealer_items'));
			add_action('load-edit.php', array(&$this, 'dealer_posts'));
			add_action('widgets_init', array(&$this, 'add_widgets'));
			add_filter('screen_options_show_screen', array(&$this, 'remove_screen_options_tab'));
			add_filter( 'contextual_help', array(&$this, 'remove_help_tabs'), 999, 3 );
			add_filter('admin_footer_text', array(&$this, 'change_footer_admin'), 9999);
			add_filter( 'update_footer', array(&$this, 'change_footer_version'), 9999);
			add_action( 'wp_before_admin_bar_render', array(&$this, 'remove_admin_bar_links'));
			add_action( 'admin_menu', array(&$this, 'change_post_menu_label'));
			add_action('wp_dashboard_setup', array(&$this, 'remove_dash_meta_boxes'));
			add_action( 'wp_dashboard_setup', array(&$this, 'example_add_dashboard_widgets'));

			require_once(sprintf("%s/post-types/vehicle_template.php", dirname(__FILE__)));
			$VehiclePost = new vehiclePostTemplate();

			require_once(sprintf("%s/widgets/types_widget_template.php", dirname(__FILE__)));
		} // END public function __construct

		/**
		* Activate the plugin
		*/
		public static function activate() {
			/**
			* Add Dealership role
			*/
			remove_role('dealership');

			$capabilities = array(
				'upload_files'	=> true,
				'edit_posts'	=> true,
				'edit_published_posts' => true,
				'publish_posts'	=> true,
				'read'			=> true,
				'level_2'		=> true,
				'level_1'		=> true,
				'level_0'		=> true,
				'delete_posts'	=> true,
				'delete_published_posts' => true,
				'publish_vehicle' => true
			);

			add_role( 'dealership', 'Dealership', $capabilities );

		} // END public static function activate

		/**
		* Deactivate the plugin
		*/
		public static function deactivate() {
			// Do nothing
		} // END public static function deactivate

		/**
		* Register custom taxonomies
		*/
		public function create_taxonomy() {
			$labels = array(
				'name'              => _x( 'Makes', 'taxonomy general name' ),
				'singular_name'     => _x( 'Make', 'taxonomy singular name' ),
				'search_items'      => __( 'Search Makes' ),
				'all_items'         => __( 'All Makes' ),
				'parent_item'       => __( 'Parent Make' ),
				'parent_item_colon' => __( 'Parent Make:' ),
				'edit_item'         => __( 'Edit Make' ),
				'update_item'       => __( 'Update Make' ),
				'add_new_item'      => __( 'Add New Make' ),
				'new_item_name'     => __( 'New Make Name' ),
				'menu_name'         => __( 'Makes' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true
			);

			register_taxonomy( 'makes', array('vehicle') , $args );

			$labels = array(
				'name'              => _x( 'Types', 'taxonomy general name' ),
				'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
				'search_items'      => __( 'Search Types' ),
				'all_items'         => __( 'All Types' ),
				'parent_item'       => __( 'Parent Type' ),
				'parent_item_colon' => __( 'Parent Type:' ),
				'edit_item'         => __( 'Edit Type' ),
				'update_item'       => __( 'Update Type' ),
				'add_new_item'      => __( 'Add New Type' ),
				'new_item_name'     => __( 'New Type Name' ),
				'menu_name'         => __( 'Types' ),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true
			);

			register_taxonomy( 'types', array('vehicle') , $args );
		}

		public function remove_dealer_items() {
			/*
			* Hide menu items for Dealerships
			*/
			if(current_user_can('publish_vehicle')) {
				remove_menu_page('edit.php');
				remove_menu_page('edit-comments.php');
				remove_menu_page('tools.php');
			}
		}

		public function dealer_posts() {
			/*
			* Only show posts for current dealer
			*/
			global $user_ID;
			if(current_user_can('publish_vehicle')) {
				if(!isset($_GET['author'])) {
					wp_redirect( add_query_arg('author', $user_ID, remove_query_arg('all_posts')));
					exit;
				}
			}
		}

		public function add_widgets() {
			/*
			* Register widgets to display types and models on sidebars
			*/
			register_widget( 'Vehicle_Type_Widget' );
		}

		public function remove_screen_options_tab() {
			/*
			* Hide 'Screen Options' tab
			*/
			if(current_user_can('publish_vehicle')) {
    			return false;
    		}
		}

		public function remove_help_tabs($old_help, $screen_id, $screen){
			/*
			* Hide 'Help' tab
			*/
			if(current_user_can('publish_vehicle')) {
			    $screen->remove_help_tabs();
			    return $old_help;
			}
		}

		public function change_footer_admin () {
			/*
			* Change footer link
			*/
			return '';
		}

		public function change_footer_version() {
			/*
			* Change footer version
			*/
			return '&copy; 2013';
		}

		public function remove_admin_bar_links() {
		    global $wp_admin_bar;
		    if(current_user_can('publish_vehicle')) {
			    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
			    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
			    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
			    $wp_admin_bar->remove_menu('new-post','new-content');      // Remove the content link
			}
		}

		public function change_post_menu_label() {
		    global $menu;
		    $menu[10][0] = 'Images';
		}

		public function remove_dash_meta_boxes() {
			if(current_user_can('publish_vehicle')) {
				remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
				remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	 			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	 			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	 			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	 			remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	 		}
		}

		public function example_add_dashboard_widgets() {
			wp_add_dashboard_widget(
	                 'oas_welcome_widget',         // Widget slug.
	                 'Welcome',         // Title.
	                 array(&$this,'disp_welcome_widget') // Display function.
	        );
		}

		public function disp_welcome_widget() {
			// Display whatever it is you want to show.
			echo "<h1>Thank you for using Vehicle Listings</h1>";
			echo '<p>You can add a new post <a href="'.admin_url( 'post-new.php?post_type=vehicle', 'admin' ).
			'" >HERE</a>, or you can view all your posts <a href="'.admin_url( 'edit.php?post_type=vehicle', 'admin' ).'" >HERE</a></p>';
		}

	 }
} //End Class JCD_Autopost

if (class_exists('JCD_Autopost')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('JCD_Autopost', 'activate'));
	register_deactivation_hook(__FILE__, array('JCD_Autopost', 'deactivate'));

	// Instatiate the plugin class
	$jcd_ap = new JCD_Autopost();

	function auto_makes_filter($current_term, $current_tax) {
		$taxonomy = 'makes';
		$query_type = 'and';
		$display_type = '';
		if ( ! taxonomy_exists( $taxonomy ) ){
			return;
		}

		$terms = get_terms( $taxonomy, array( 'hide_empty' => true ) );

		if ( count( $terms ) > 0 ) {
			ob_start();

			$found = false;
			
			if ( $display_type == 'dropdown' ) {
				// skip when viewing the taxonomy
				if ( $current_tax && $taxonomy == $current_tax ) {
					$found = false;
				} else {

					$taxonomy_filter = $taxonomy;

					$found = false;

					echo '<select id="dropdown_layered_nav_' . $taxonomy_filter . '">';

					echo '<option value="">' . sprintf( __( 'Any %s', 'woocommerce' ), $taxonomy ) .'</option>';

					foreach ( $terms as $term ) {

						// If on a term page, skip that term in widget list
						if ( $term->term_id == $current_term ){
							continue;
						}

						// Get count based on current view - uses transients
						$transient_name = 'auto_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );
						if ( false === ( $vehicles_in_make = get_transient( $transient_name ) ) ) {
							$vehicles_in_make = get_objects_in_term( $term->term_id, $taxonomy );

							set_transient( $transient_name, $vehicles_in_make );
						}

						$transient_name = 'auto_count_' . md5( sanitize_key( $current_tax ) . sanitize_key( $current_term ) );

						if ( false === ( $vehicles_in_type = get_transient( $transient_name ) ) ) {
							$vehicles_in_type = get_objects_in_term( $current_term, $current_tax );

							set_transient( $transient_name, $vehicles_in_type );
						}

						$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) );

						// If this is an AND query, only show options with count > 0
						if ( $query_type == 'and' ) {

							$count = sizeof( array_intersect( $vehicles_in_make, $vehicles_in_type ) );

							if ( $count > 0 ){
								$found = true; }

							if ( $count == 0 && ! $option_is_set ){
								continue; }

						// If this is an OR query, show all options so search can be expanded
						} /*else {

							$count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

							if ( $count > 0 )
								$found = true;

						}*/

						echo '<option value="' . $term->name . '" '.selected( isset( $_GET[ $taxonomy_filter ] ) ? $_GET[ $taxonomy_filter ] : '' , $term->name, false ) . '>' . $term->name . '</option>';
					}

					echo '</select>';

					echo "<script>
					jQuery('#dropdown_layered_nav_$taxonomy_filter').change(function(){
							location.href = '" . esc_url_raw( preg_replace( '%\/page/[0-9]+%', '', remove_query_arg( array( 'page', $taxonomy_filter ) ) ) ) . "?$taxonomy_filter=' + jQuery('#dropdown_layered_nav_$taxonomy_filter').val();
						});
					</script>";
				}
			} else {
				echo '<ul>';
				$taxonomy_filter = $taxonomy;

				foreach ($terms as $term) {

					// Get count based on current view - uses transients
					$transient_name = 'auto_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );
					if ( false === ( $vehicles_in_make = get_transient( $transient_name ) ) ) {
						$vehicles_in_make = get_objects_in_term( $term->term_id, $taxonomy );

						set_transient( $transient_name, $vehicles_in_make );
					}

					$transient_name = 'auto_count_' . md5( sanitize_key( $current_tax ) . sanitize_key( $current_term ) );

					if ( false === ( $vehicles_in_type = get_transient( $transient_name ) ) ) {
						$vehicles_in_type = get_objects_in_term( $current_term, $current_tax );

						set_transient( $transient_name, $vehicles_in_type );
					}

					// If this is an AND query, only show options with count > 0
					if ( $query_type == 'and' ) {

						$count = sizeof( array_intersect( $vehicles_in_make, $vehicles_in_type ) );

						// skip the term for the current archive
						if ( $current_term == $term->term_id )
							continue;

						if ( $count > 0 && $current_term !== $term->term_id )
							$found = true;

						if ( $count == 0 && ! $option_is_set )
							continue;

					// If this is an OR query, show all options so search can be expanded
					} /*else {

						// skip the term for the current archive
						if ( $current_term == $term->term_id )
							continue;

						$count = sizeof( array_intersect( $_products_in_term, $woocommerce->query->unfiltered_product_ids ) );

						if ( $count > 0 )
							$found = true;

					}*/

					$link = esc_url_raw( preg_replace( '%\/page/[0-9]+%', '', remove_query_arg( array( 'page', 'filter_' . $taxonomy_filter ) ) ) ) . "?$taxonomy_filter=$term->name";

					echo '<li ' . $class . '>';

					echo ( $count > 0 || $option_is_set ) ? '<a href="' . esc_url( $link ) . '">' : '<span>';

					echo $term->name;

					echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';

					echo ' <small class="count">(' . $count . ')</small></li>';
				}

				echo '</ul>';
			}
		}
		if ( ! $found ){
			ob_end_clean();
		} else {
			echo ob_get_clean();
		}
	}
}