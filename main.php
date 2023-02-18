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

	wp_clear_scheduled_hook( 'sitemap_generator_hourly_event' );
}
register_deactivation_hook( __FILE__, 'sitemap_generator_disabled' );

/**
 * Schedule cron hourly
 *
 * @return void
 */
function sitemap_generator_schedule_job() {
	if ( ! wp_next_scheduled( 'sitemap_generator_hourly_event' ) ) {
			wp_schedule_event( time(), 'hourly', 'sitemap_generator_hourly_event' );
	}
}

add_action( 'init', 'sitemap_generator_schedule_job' );

/**
 * Cron to run hourly
 *
 * @return void
 */
function sitemap_generator_do_this_hourly() {
	$db = new Sitemap_Generator_Db();

	$max_depth = get_option( 'website_sitemap_max_depth', 5 );

	$crawler    = new Sitemap_Generator_Crawler( $db );
	$sitemap_id = $crawler->generate_website_sitemap( $max_depth );

	if ( is_int( $sitemap_id ) ) {
		$parser = new Sitemap_Generator_Parser( $db );
		$parser->generate_html( $sitemap_id );
	}
}

add_action( 'sitemap_generator_hourly_event', 'sitemap_generator_do_this_hourly' );




