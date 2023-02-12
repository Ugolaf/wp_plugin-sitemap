<?php
/**
 * Plugin Name: Sitemap Generator
 * Description: Generates a sitemap for the current website
 * Version: 0.0.1
 * Author: Ugo LAFAILLE
 */

defined( 'ABSPATH' ) || exit;

/**
 * Function to trigger when the plugin is activated
 *
 * @return void
 */
function sitemap_generator_activate() {
	// Code that will run when the plugin is activated.
}
register_activation_hook( __FILE__, 'sitemap_generator_activate' );

/**
 * Function to trigger when the plugin is disabled
 *
 * @return void
 */
function sitemap_generator_disabled() {
	// Code that will run when the plugin is deactivated.
}
register_deactivation_hook( __FILE__, 'sitemap_generator_disabled' );

require_once plugin_dir_path( __FILE__ ) . 'inc/admin/settings_page.php';


