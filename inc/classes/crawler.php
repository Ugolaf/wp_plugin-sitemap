<?php
require_once 'db.php';

/**
 * Crawler to store and generate sitemap
 */
class Sitemap_Generator_Crawler {

	/**
	 * Database instance
	 *
	 * @var \Sitemap_Generator_Db
	 */
	public $db;

	/**
	 * Class constructor
	 *
	 * @param  mixed $wpdb Database instance to use.
	 * @return void
	 */
	public function __construct( $wpdb ) {
		$this->db = $wpdb;

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$this->db->truncate_tables();

		if ( $wp_filesystem->exists( ABSPATH . 'sitemap.html' ) ) {
			$wp_filesystem->delete( ABSPATH . 'sitemap.html' );
		}
	}

	/**
	 * Generate website sitemap recursivly
	 *
	 * @param  mixed $depth Max depth to recursivly parse dom.
	 * @return int|boolean
	 */
	public function generate_website_sitemap( $depth ) {
		$sitemap_array           = [];
		$sitemap_already_crawled = [];
		$current_page            = get_home_url();

		$current_key = $this->db->new_entry(
			'website_sitemap',
			[
				'date_created' => current_time( 'mysql' ),
				'home_page'    => $current_page,
			],
			[
				'%s',
				'%s',
			]
		);
		if ( $current_key ) {
			$this->generate_website_sitemap_recursive( $current_key, $current_page, $depth, $sitemap_array, $sitemap_already_crawled );
		}

		return $current_key;
	}

	/**
	 * Recursively get linked page.
	 *
	 * @param  mixed $current_key Database initial entrie.
	 * @param  mixed $current_page Current url to crawl.
	 * @param  mixed $depth Current depth.
	 * @param  mixed $sitemap Reference of sitemap array.
	 * @param  mixed $sitemap_already_crawled Reference of links already crawled.
	 * @return void
	 */
	private function generate_website_sitemap_recursive( $current_key, $current_page, $depth, &$sitemap, &$sitemap_already_crawled ) {
		if ( $depth > 0 && ! in_array( $current_page, $sitemap_already_crawled, true ) ) {
			$sitemap[]                 = $current_page;
			$sitemap_already_crawled[] = $current_page;
			$linked_pages              = $this->get_linked_pages( $current_page, $sitemap_already_crawled );

			if ( ! isset( $sitemap[ $current_page ] ) ) {
				$sitemap[ $current_page ] = [];
			}

			$array_entries = [];

			foreach ( $linked_pages as $name => $link ) {
				$array_entries[] = [
					'sitemap_id'  => $current_key,
					'parent_link' => $current_page,
					'link'        => $link,
					'name'        => $name,
				];
			}

			$this->db->new_entries(
				'website_sitemap_links',
				$array_entries,
				[
					'%d',
					'%s',
					'%s',
					'%s',
				]
			);

			foreach ( $linked_pages as $linked_page_title => $linked_page_url ) {
				if ( strpos( $linked_page_url, get_home_url() ) === 0 && ! in_array( $linked_page_url, $sitemap_already_crawled, true ) ) {
					$this->generate_website_sitemap_recursive( $current_key, $linked_page_url, $depth - 1, $sitemap[ $current_page ], $sitemap_already_crawled );
				}
			}
		}
	}

	/**
	 * Crawl links by finding all a href html tags.
	 *
	 * @param  string $url Current url to crawl.
	 * @param  mixed  $sitemap_already_crawled Reference of links already crawled.
	 * @return array
	 */
	private function get_linked_pages( $url, &$sitemap_already_crawled ) {

		if ( strpos( $url, get_home_url() ) === 0 ) {
			$response = wp_remote_get( $url );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$html = wp_remote_retrieve_body( $response );

				$dom = new DOMDocument();
				// Warning in silence because we do not control the incoming dom.
				@$dom->loadHTML( $html ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

				$links = $dom->getElementsByTagName( 'a' );

				$linked_pages = [];
				foreach ( $links as $link ) {
					$linked_pages[ $link->nodeValue ] = $link->getAttribute( 'href' ); //phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}

				return $linked_pages;

			}
		}
		return [];
	}
}
