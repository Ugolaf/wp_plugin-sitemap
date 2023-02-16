<?php
/**
 * Plugin Name: Sitemap Generator
 * Description: Generates a sitemap for the current website
 * Version: 0.0.1
 * Author: Ugo LAFAILLE
 */

defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path( __FILE__ ) . 'inc/vues/admin/settings_page.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/classes/crawler.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/classes/parser.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/classes/db.php';

/**
 * Function to trigger when the plugin is activated
 *
 * @return void
 */
function sitemap_generator_activate() {
	$db = new Sitemap_Generator_Db();
	$db->set_tables();
	$crawler = new Sitemap_Generator_Crawler( $db );
	$crawler->generate_website_sitemap( 3 );
}
register_activation_hook( __FILE__, 'sitemap_generator_activate' );

/**
 * Function to trigger when the plugin is disabled
 *
 * @return void
 */
function sitemap_generator_disabled() {
	$db = new Sitemap_Generator_Db();
	$db->delete_tables();
}
register_deactivation_hook( __FILE__, 'sitemap_generator_disabled' );



