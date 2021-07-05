<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Nocte_Slider
 * @subpackage Nocte_Slider/includes
 * @author     Obscurum Nocte <dev@obscurum-nocte.uk>
 */
class Nocte_Slider {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct(){
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_action('plugins_loaded', array( $this, 'set_locale') );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies(){
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-nocte-slider-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-nocte-slider-public.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_locale() {
		load_plugin_textdomain(
			Nocte_Slider_Data::get_plugin_name(),
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) .'/languages/'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks(){
		$plugin_admin = new Nocte_Slider_Admin();

		add_action('admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles') );
		add_action('admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts') );

		//  Add carousel post type
		add_action('init', array( $plugin_admin, 'ns_carousel_post_type_setup'), 11 );
        add_filter('post_updated_messages', array( $plugin_admin, 'ns_carousel_updated_messages') );
		//  Add carousel listing shortcode column
		add_filter('manage_'. $plugin_admin->get_posttype_name() .'_posts_columns', array( $plugin_admin, 'ns_carousel_additional_columns'), 11 );
        add_action('manage_posts_custom_column', array( $plugin_admin, 'ns_carousel_custom_columns_content'), 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks(){
		$plugin_public = new Nocte_Slider_Public();

		add_action('wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles') );
		add_action('wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts') );
	}

}
