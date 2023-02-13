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

	$wp_unslashed = wp_unslash( $_POST );

	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $wp_unslashed['_wpnonce'], 'generate_sitemap' )
	&& isset( $wp_unslashed['submit'] ) ) {

		$crawler = new Sitemap_Generator_Crawler();
		$crawler->generate_website_sitemap( 1 );

		echo '<div class="notice notice-success is-dismissible">
      			<p>The sitemap has been generated, consult it from this <a href="/sitemap.html" _blank="">url</a>.</p>
      		</div>';
	}
	?>

<div class="wrap">
  <h1>Sitemap Generator</h1>
  <form method="post">
	<?php wp_nonce_field( 'generate_sitemap' ); ?>
	<p class="submit">
	  <input type="submit" name="submit" class="button-primary" value="Generate sitemap">
	</p>
  </form>
</div>

	<?php
}
