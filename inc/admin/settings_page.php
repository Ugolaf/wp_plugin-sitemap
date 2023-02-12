<?php

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'add_sitemap_settings_page' );

/**
 * Add sitemap configuration from settings menu
 *
 * @return void
 */
function add_sitemap_settings_page() {
	add_options_page(
		'Sitemap Generator',
		'Sitemap',
		'manage_options',
		'sitemap',
		'render_sitemap_settings_page'
	);
}

/**
 * Render sitemap settingspage
 *
 * @return void
 */
function render_sitemap_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	$max_depth     = get_option( 'website_sitemap_max_depth', 50 );
	$exclude_pages = get_option( 'website_sitemap_exclude_pages', [] );
	$exclude_posts = get_option( 'website_sitemap_exclude_posts', [] );

	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $wp_unslash( $_POST )['_wpnonce'], 'my_form_action' )
	&& isset( sanitize_text_field( wp_unslash( $_POST ) )['submit'] ) ) {
		$max_depth     = isset( $sanitized_post_data['max_depth'] ) ? $sanitized_post_data['max_depth'] : 50;
		$exclude_pages = isset( $sanitized_post_data['exclude_pages'] ) ? $sanitized_post_data['exclude_pages'] : [];
		$exclude_posts = isset( $sanitized_post_data['exclude_posts'] ) ? $sanitized_post_data['exclude_posts'] : [];

		update_option( 'website_sitemap_max_depth', $max_depth );
		update_option( 'website_sitemap_exclude_pages', $exclude_pages );
		update_option( 'website_sitemap_exclude_posts', $exclude_posts );

		echo '<div class="notice notice-success is-dismissible">
      <p>The settings have been updated.</p>
      </div>';
	}
	$pages = get_pages();
	$posts = get_posts();
	?>

<div class="wrap">
  <h1>Sitemap Generator</h1>
  <form method="post">
	<table class="form-table">
	  <tr>
		<th>Maximum depth for sitemap generation</th>
		<td>
		  <label>
			<input type="integer" name="max_depth" value="<?php echo esc_html( $max_depth ); ?>" >
			Number of recursivity
		  </label>
		</td>
	  </tr>
	  <tr>
		<th>Exclude Pages</th>
		<td>
		  <?php foreach ( $pages as $page ) : ?>
			<label>
			  <input type="checkbox" name="exclude_pages" value="<?php echo esc_attr( $page->ID ); ?>" <?php checked( in_array( $page->ID, $exclude_pages, true ) ); ?>>
				<?php echo esc_html( $page->post_title ); ?>
			</label>
			<br>
		  <?php endforeach; ?>
		</td>
	  </tr>
		<tr>
		<th>Exclude Posts</th>
		<td>
		  <?php foreach ( $posts as $post ) : ?>
			<label>
			  <input type="checkbox" name="exclude_psts" value="<?php echo esc_attr( $post->ID ); ?>" <?php checked( in_array( $post->ID, $exclude_posts, true ) ); ?>>
				<?php echo esc_html( $post->post_title ); ?>
			</label>
			<br>
		  <?php endforeach; ?>
		</td>
	  </tr>
	</table>
	<?php wp_nonce_field( 'my_form_action', '_wpnonce' ); ?>
	<p class="submit">
	  <input type="submit" name="submit" class="button-primary" value="Generate sitemap">
	</p>
  </form>
</div>

	<?php
}
