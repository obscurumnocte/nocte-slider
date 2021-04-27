<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Nocte_Slider
 * @subpackage Nocte_Slider/public
 * @author     Obscurum Nocte <dev@obscurum-nocte.uk>
 */
class Nocte_Slider_Public {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct(){}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(){
		wp_enqueue_style( Nocte_Slider_Data::get_plugin_name(), plugin_dir_url( __FILE__ ) .'css/nocte-slider-public.css', array(), Nocte_Slider_Data::get_plugin_version(), 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(){
		wp_enqueue_script( Nocte_Slider_Data::get_plugin_name(), plugin_dir_url( __FILE__ ) .'js/nocte-slider-public.js', array('jquery'), Nocte_Slider_Data::get_plugin_version(), false );
	}

}
