<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nocte_Slider
 * @subpackage Nocte_Slider/admin
 * @author     Obscurum Nocte <dev@obscurum-nocte.uk>
 */
class Nocte_Slider_Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct(){
		$this->load_dependencies();
	}

	/**
	 *  Load the required dependencies for the admin customisation and custom fields.
	 */
	private function load_dependencies(){
		/**
		 * The classes for field set up
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/class-ns-carousel-fields-abstract.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/types/class-ns-carousel-fields-checkbox.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/types/class-ns-carousel-fields-number.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/types/class-ns-carousel-fields-repeater.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/types/class-ns-carousel-fields-select.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/types/class-ns-carousel-fields-subfields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/types/class-ns-carousel-fields-text.php';
		/**
		 * The class responsible for creating field markup.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/fields/class-ns-carousel-metabox-fields.php';
		/**
		 * The class responsible for creating field markup.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/class-ns-carousel-slides-meta.php';
		/**
		 * The class responsible for creating field markup.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) .'admin/metaboxes/class-ns-carousel-owl-meta.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(){
		wp_enqueue_style( Nocte_Slider_Data::get_plugin_name(), plugin_dir_url( __FILE__ ) .'css/nocte-slider-admin.css', array(), Nocte_Slider_Data::get_plugin_version(), 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(){
		wp_enqueue_script( Nocte_Slider_Data::get_plugin_name(), plugin_dir_url( __FILE__ ) .'js/nocte-slider-admin.js', array('jquery'), Nocte_Slider_Data::get_plugin_version(), false );
	}


	/**
	 * Set up Carousel posttype
	 */
	public function ns_carousel_post_type_setup(){
    	//  Carousels post type
    	$custom_pt_labels = array(
    		'name'					=> __('Carousels', 'ns'),
    		'singular_name'			=> __('Carousel', 'ns'),
    		'menu_name'				=> __('Carousels', 'ns'),
    		'all_items'				=> __('Carousels', 'ns'),
    		'add_new'				=> __('Add New Carousel', 'ns'),
    		'add_new_item'			=> __('Add new carousel', 'ns'),
    		'edit_item'				=> __('Edit Carousel', 'ns'),
    		'new_item'				=> __('New Carousel', 'ns'),
    		'view_item'				=> __('View Carousel', 'ns'),
            'view_items'            => __('View Carousels', 'ns'),
            'search_item'			=> __('Search Carousels', 'ns'),
            'search_items'			=> __('Search Carousels', 'ns'),
    		'not_found'				=> __('No Carousels found', 'ns'),
    		'not_found_in_trash'	=> __('No Carousels found in Trash', 'ns'),
    		'parent_item_colon'		=> __('Parent Carousel', 'ns')
    	);
    	$custom_pt_args = array(
    		'labels'				=> $custom_pt_labels,
    		'description'			=> __('Create carousels to display image sliders on pages/posts.', 'ns'),
    		'public'				=> true,
			'publicly_queryable'    => false,
    		'exclude_from_search'	=> true,
			'show_ui'               => true,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => false,
    		'menu_position'			=> 55,
    		'menu_icon'				=> 'data:image/svg+xml;base64,'. base64_encode('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;" xml:space="preserve"><polyline points="25,11 27,13 25,15"/><polyline points="7,11 5,13 7,15"/><path d="M29,23H3c-1.1,0-2-0.9-2-2V5c0-1.1,0.9-2,2-2h26c1.1,0,2,0.9,2,2v16C31,22.1,30.1,23,29,23z"/><circle cx="16" cy="28" r="1"/><circle cx="10" cy="28" r="1"/><circle cx="22" cy="28" r="1"/></svg>'),
    		'hierarchical'			=> false,
    		'supports'				=> array(
    									'title',
										'revisions'
    								),
    		'has_archive'			=> false,
    		'rewrite'				=> false,
    		'query_var'				=> false,
    		'can_export'			=> true,
			'delete_with_user'      => true
    	);
    	register_post_type('ns_carousel', $custom_pt_args );
    }


    /**
     * 	Add filter to customise the text displayed when user updates
     */
    public function ns_carousel_updated_messages( $messages ){
        global $post, $post_id;

        $messages['ns_carousel'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __('Carousel updated. <a href="%s">View carousel</a>', 'ns'), esc_url( get_permalink( $post_id ) ) ),
            2 => __('Custom field updated.', 'ns'),
            3 => __('Custom field deleted.', 'ns'),
            4 => __('Carousel updated.', 'ns'),
            /* translators: %s: date and time of the revision */
            5 => isset( $_GET['revision'] ) ? sprintf( __('Carousel restored to revision from %s', 'ns'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __('Carousel published. <a href="%s">View carousel</a>', 'ns'), esc_url( get_permalink( $post_id ) ) ),
            7 => __('Carousel saved.', 'ns'),
            8 => sprintf( __('Carousel submitted. <a target="_blank" href="%s">Preview carousel</a>', 'gec'), esc_url( add_query_arg('preview', 'true', get_permalink( $post_id ) ) ) ),
            9 => sprintf( __('Carousel scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview carousel</a>', 'ns'),
                 // translators: Publish box date format, see http://php.net/date
                 date_i18n('M j, Y @ G:i', strtotime( $post->post_date ) ), esc_url( get_permalink( $post_id ) ) ),
            10 => sprintf( __('Carousel draft updated. <a target="_blank" href="%s">Carousel</a>', 'ns'), esc_url( add_query_arg('preview', 'true', get_permalink( $post_id ) ) ) )
        );

        return $messages;
    }

}
