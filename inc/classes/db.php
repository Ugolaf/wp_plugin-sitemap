<?php

/**
 * Crawler to store and generate sitemap
 */
class Sitemap_Generator_Db {

	/**
	 * Wpdb local instance
	 *
	 * @var mixed
	 */
	private $wpdb;

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Delete database tables for this plugin
	 *
	 * @return void
	 */
	public function delete_tables() {

		$sitemap_links = $this->wpdb->prefix . 'website_sitemap_links';
		$sitemap       = $this->wpdb->prefix . 'website_sitemap';

		$this->wpdb->query( "DROP TABLE IF EXISTS $sitemap_links" );
		$this->wpdb->query( "DROP TABLE IF EXISTS $sitemap" );
	}

	/**
	 * Set database tables for this plugin
	 *
	 * @return void
	 */
	public function set_tables() {

		$sitemap_table_name = $this->wpdb->prefix . 'website_sitemap';
		if ( $this->wpdb->get_var( "SHOW TABLES LIKE '$sitemap_table_name'" ) !== $sitemap_table_name ) {
			$sql = "CREATE TABLE $sitemap_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        date_created datetime NOT NULL,
        home_page text NOT NULL,
        PRIMARY KEY (id)
    );";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

		$sitemap_links_table_name = $this->wpdb->prefix . 'website_sitemap_links';
		if ( $this->wpdb->get_var( "SHOW TABLES LIKE '$sitemap_links_table_name'" ) !== $sitemap_links_table_name ) {
			$sql = "CREATE TABLE $sitemap_links_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        sitemap_id mediumint(9) NOT NULL,
        parent_link text NOT NULL,
        link text NOT NULL,
        name text NOT NULL,
        PRIMARY KEY (id),
				UNIQUE(sitemap_id, parent_link(255), link(255), name(255)),
        FOREIGN KEY (sitemap_id) REFERENCES $sitemap_table_name(id) ON DELETE CASCADE
    );";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}

	/**
	 * Add a new entrie in database and get id inserted
	 *
	 * @param  mixed $table Database table.
	 * @param  mixed $values Values array to insert.
	 * @param  mixed $types Types array to respect.
	 * @return int|boolean
	 */
	public function new_entrie( $table, $values, $types ) {

		if ( $this->wpdb->insert(
			$this->wpdb->prefix . $table,
			$values,
			$types
		) !== false ) {
			return $this->wpdb->insert_id;
		}
		return false;
	}

	/**
	 * Add new entries in database and get ids inserted
	 *
	 * @param string $table Database table.
	 * @param array  $values_array Array of values arrays to insert.
	 * @param array  $types_array Types array to respect.
	 * @return array|boolean Array of inserted ids, or false on failure.
	 */
	public function new_entries( $table, $values_array, $types_array ) {
		$ids = [];
		foreach ( $values_array as $values ) {
			$query           = $this->wpdb->prepare(
				"SELECT id FROM {$this->wpdb->prefix}{$table} WHERE sitemap_id = %d AND parent_link = %s AND link = %s AND name = %s",
				$values['sitemap_id'],
				$values['parent_link'],
				$values['link'],
				$values['name']
			);
			$existing_record = $this->wpdb->get_var( $query );

			if ( $existing_record ) {
				$ids[] = $existing_record;
			} elseif ( $this->wpdb->insert(
				$this->wpdb->prefix . $table,
				$values,
				$types_array
			) !== false ) {
				$ids[] = $this->wpdb->insert_id;
			} else {
				return false;
			}
		}
		return $ids;
	}

	/**
	 * Get child links for a given parent link, excluding certain links
	 *
	 * @param  mixed  $sitemap_id Sitemap Id database entrie.
	 * @param  string $parent_link Parent link to get child links.
	 * @param  array  $exclude_links Array of links to exclude.
	 * @return array  Array of child links for the parent link, excluding the specified links.
	 */
	public function get_child_links( $sitemap_id, $parent_link, $exclude_links = [] ) {

		$query = $this->wpdb->prepare(
			"SELECT link, name FROM {$this->wpdb->prefix}website_sitemap_links
			WHERE parent_link = %s AND sitemap_id = %s",
			[ $parent_link, $sitemap_id ]
		);

		if ( ! empty( $exclude_links ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $exclude_links ), '%s' ) );
			$query       .= $this->wpdb->prepare( " AND link NOT IN ($placeholders)", $exclude_links );
		}

		$results = $this->wpdb->get_results( $query, ARRAY_A );

		return $results;
	}

}


