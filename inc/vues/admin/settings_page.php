<?php

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'sitemap_generator_settings_page' );

/**
 * Add sitemap configuration from settings menu
 *
 * @return void
 */
function sitemap_generator_settings_page() {
	add_options_page(
		'Sitemap Generator',
		'Sitemap',
		'manage_options',
		'sitemap',
		'sitemap_generator_render_settings_page'
	);
}

/**
 * Render sitemap settingspage
 *
 * @return void
 */
function sitemap_generator_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	$max_depth = get_option( 'website_sitemap_max_depth', 50 );

	$wp_unslashed = wp_unslash( $_POST );

	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $wp_unslashed['_wpnonce'], 'generate_sitemap' )
	&& isset( $wp_unslashed['submit'] ) ) {
		$max_depth = isset( $wp_unslashed['max_depth'] ) ? intval( $wp_unslashed['max_depth'], 10 ) : 5;

		update_option( 'website_sitemap_max_depth', $max_depth );

		$db = new Sitemap_Generator_Db();

		$crawler    = new Sitemap_Generator_Crawler( $db );
		$sitemap_id = $crawler->generate_website_sitemap( $max_depth );

		if ( is_int( $sitemap_id ) ) {
			$parser = new Sitemap_Generator_Parser( $db );
			$parser->generate_html( $sitemap_id );

			echo '<div class="notice notice-success is-dismissible">
      			<p>The sitemap has been generated, consult it from this <a href="/sitemap.html" _blank="">url</a>.</p>
      		</div>';
		} else {

		}
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
		  </label>
		</td>
	  </tr>
	</table>
	<?php wp_nonce_field( 'generate_sitemap' ); ?>
	<p class="submit">
	  <input type="submit" name="submit" class="button-primary" value="Generate sitemap">
	</p>
  </form>
</div>

	<?php
}
