<?php

/**
 * Crawler to store and generate sitemap
 */
class Sitemap_Generator_Crawler {

	/**
	 * Generate website sitemap recursivly
	 *
	 * @param  mixed $depth Max depth to recursivly parse dom.
	 * @return string
	 */
	public function generate_website_sitemap( $depth ) {
		$sitemap_array = [];
		$current_page  = get_home_url();

		$this->generate_website_sitemap_recursive( $current_page, $depth, $sitemap_array );

		$sitemap = '<html><head><title>Sitemap</title></head><body><h1>Sitemap</h1><ul>';
		foreach ( $sitemap_array as $page ) {
				$sitemap .= '<li><a href="' . get_permalink( $page ) . '">' . $page . '</a></li>';
		}
		$sitemap .= '</ul></body></html>';

		file_put_contents( ABSPATH . 'sitemap.html', $sitemap );

		/*
		 add_filter('rewrite_rules_array', 'sitemap_html_rewrite_rules');
		function sitemap_html_rewrite_rules($rules) {
			// Add a rewrite rule for the sitemap.html file
			$new_rules = array('sitemap\.html$' => 'sitemap.html');
			$rules += $new_rules;
			return $rules;
		}*/

		return $sitemap;
	}

	/**
	 * Crawl links by finding all a href html tags.
	 *
	 * @param  string $url Current url to crawl.
	 * @return array
	 */
	private function get_linked_pages( $url ) {

		if ( strpos( $url, get_home_url() ) !== false ) {
			$response = wp_remote_get( $url );
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$html = wp_remote_retrieve_body( $response );

				$dom = new DOMDocument();
				@$dom->loadHTML( $html );

				$links = $dom->getElementsByTagName( 'a' );

				error_log( __FUNCTION__ . ' links' . "\n" . print_r( $links, 1 ) );

				$linked_pages = [];
				foreach ( $links as $link ) {
					error_log( __FUNCTION__ . ' link' . "\n" . print_r( $link, 1 ) );
					$linked_pages[ $link->nodeValue ] = $link->getAttribute( 'href' );
				}

				return $linked_pages;

			}
		}
		return [];
	}

	/**
	 * Recursively get linked page.
	 *
	 * @param  mixed $current_page Current url to crawl.
	 * @param  mixed $depth Current depth.
	 * @param  mixed $sitemap Reference of sitemap array.
	 * @return void
	 */
	private function generate_website_sitemap_recursive( $current_page, $depth, &$sitemap ) {

		$sitemap[] = $current_page;

		if ( $depth > 0 ) {
			$linked_pages = $this->get_linked_pages( $current_page );
			error_log( __FUNCTION__ . ' linked_pages' . "\n" . print_r( $linked_pages, 1 ) );
			foreach ( $linked_pages as $linked_page ) {
					$this->generate_website_sitemap_recursive( $linked_page, $depth - 1, $sitemap );
			}
		}
	}
}
