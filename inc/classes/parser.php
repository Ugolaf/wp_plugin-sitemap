<?php
require_once 'db.php';

/**
 * Crawler to store and generate sitemap
 */
class Sitemap_Generator_Parser {

	/**
	 * Class constructor
	 *
	 * @param  mixed $wpdb Database instance to use.
	 * @return void
	 */
	public function __construct( $wpdb ) {
		$this->db = $wpdb;
	}

	/**
	 * Database instance
	 *
	 * @var \Sitemap_Generator_Db
	 */
	public $db;

	/**
	 * Generate website sitemap recursivly
	 *
	 * @param  mixed $sitemap_id Sitemap Id database entrie.
	 * @return string
	 */
	public function generate_html( $sitemap_id ) {
		$home_page = get_home_url();
		if ( ! $home_page ) {
			return '';
		}

		$links_already_showed = [];

		$sitemap  = '<html><head><meta charset="UTF-8"><title>Sitemap</title></head><body><h1>Sitemap</h1>';
		$sitemap .= $this->fill_ul_sitemap( $sitemap_id, $home_page, $links_already_showed );
		$sitemap .= '</body></html>';

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$wp_filesystem->put_contents( ABSPATH . 'sitemap.html', $sitemap );
	}

	/**
	 * Fill li or ul if current element is an array
	 *
	 * @param  mixed $sitemap_id Sitemap Id database entrie.
	 * @param  mixed $parent_link Parent link.
	 * @param  mixed $links_already_showed Reference of links already taken.
	 * @return string
	 */
	private function fill_ul_sitemap( $sitemap_id, $parent_link, &$links_already_showed ) {

		$child_links = $this->db->get_child_links( $sitemap_id, $parent_link, $links_already_showed );

		if ( empty( $child_links ) ) {
			return '';
		}

		array_push( $links_already_showed, ...array_column( $child_links, 'link' ) );

		$html = '';
		foreach ( $child_links as $child_link ) {
			$html .= '<li><a href="' . esc_url( $child_link['link'] ) . '">' . esc_html( $child_link['name'] ) . '</a>';
			$html .= $this->fill_ul_sitemap( $sitemap_id, $child_link['link'], $links_already_showed );
			$html .= '</li>';
		}

		return '<ul>' . $html . '</ul>';
	}
}
