<?php

/**
 * The core plugin data class.
 *
 * Used to store key plugin data that is statically available.
 *
 * @since      1.0.0
 * @package    Nocte_Slider
 * @subpackage Nocte_Slider/includes
 * @author     Obscurum Nocte <dev@obscurum-nocte.uk>
 */
class Nocte_Slider_Data {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private static $plugin_name = 'nocte-slider';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of the plugin.
	 */
	private static $plugin_version = NOCTE_SLIDER_VERSION;

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public static function get_plugin_name(){
		return self::$plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public static function get_plugin_version(){
		return self::$plugin_version;
	}

}
