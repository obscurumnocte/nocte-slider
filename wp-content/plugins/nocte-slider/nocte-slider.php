<?php

/**
 * This file includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://obscurum-nocte.uk
 * @since             1.0.0
 * @package           Nocte_Slider
 *
 * @wordpress-plugin
 * Plugin Name:       Nocte Slider with Owl Carousel
 * Plugin URI:        https://obscurum-nocte.uk/nocte-slider
 * Description:       An easy to use slider that provides a widget and shortcode to embed Owl Carousels on your website.
 * Version:           1.0.0
 * Author:            Obscurum Nocte
 * Author URI:        https://obscurum-nocte.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nocte-slider
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'NOCTE_SLIDER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nocte-slider-activator.php
 */
function activate_nocte_slider() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nocte-slider-activator.php';
	Nocte_Slider_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nocte-slider-deactivator.php
 */
function deactivate_nocte_slider() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-nocte-slider-deactivator.php';
	Nocte_Slider_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nocte_slider' );
register_deactivation_hook( __FILE__, 'deactivate_nocte_slider' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-nocte-slider.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_nocte_slider() {

	$plugin = new Nocte_Slider();

}
run_nocte_slider();
