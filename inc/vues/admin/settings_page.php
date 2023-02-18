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

	$db     = new Sitemap_Generator_Db();
	$parser = new Sitemap_Generator_Parser( $db );

	$max_depth = get_option( 'website_sitemap_max_depth', 5 );

	$wp_unslashed = wp_unslash( $_POST );

	if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $wp_unslashed['_wpnonce'], 'generate_sitemap' )
	&& isset( $wp_unslashed['submit'] ) ) {
		$max_depth = isset( $wp_unslashed['max_depth'] ) ? intval( $wp_unslashed['max_depth'], 10 ) : 5;

		update_option( 'website_sitemap_max_depth', $max_depth );

		$crawler    = new Sitemap_Generator_Crawler( $db );
		$sitemap_id = $crawler->generate_website_sitemap( $max_depth );

		if ( is_int( $sitemap_id ) ) {
			$parser->generate_html( $sitemap_id );

			echo '<div class="notice notice-success is-dismissible">
      				<p>The sitemap has been generated, consult it from this <a href="/sitemap.html" _blank="">url</a>.</p>
      			</div>';
		} else {
			echo '<div class="notice notice-error is-dismissible">
							<p><There was an error generating the sitemap./p>
						</div>';
		}
	}
	$pages = get_pages();
	$posts = get_posts();
	?>

<style>
.accordion {
	margin: 0;
	padding: 0;
}
.accordion h3 {
	background-color: #2573AA;
	color: white;
	cursor: pointer;
	font-size: 14px;
	font-weight: 600;
	margin: 0;
	padding: 16px 24px;
}
.accordion div {
	background-color: #fff;
	border: 1px solid #ddd;
	margin: 0;
	padding: 16px 24px;
}

.overflowing-div {
	overflow: auto;
}

.tree, .tree ul, .tree li {
list-style: none;
margin: 0;
padding: 0;
position: relative;
}

.tree {
margin: 0 0 1em;
text-align: center;
}
.tree, .tree ul {
display: table;
}
.tree ul {
width: 100%;
}
.tree li {
	display: table-cell;
	padding: .5em 0;
	vertical-align: top;
}
	/* _________ */
	.tree li:before {
		outline: solid 1px #666;
		content: "";
		left: 0;
		position: absolute;
		right: 0;
		top: 0;
	}
	.tree li:first-child:before {left: 50%;}
	.tree li:last-child:before {right: 50%;}

	.tree code, .tree span {
		border: solid .1em #666;
		border-radius: .2em;
		display: inline-block;
		margin: 0 .2em .5em;
		padding: .2em .5em;
		position: relative;
	}
	/* If the tree represents DOM structure */
	.tree code {
		font-family: monaco, Consolas, 'Lucida Console', monospace;
	}

		/* | */
		.tree ul:before,
		.tree code:before,
		.tree span:before {
			outline: solid 1px #666;
			content: "";
			height: .5em;
			left: 50%;
			position: absolute;
		}
		.tree ul:before {
			top: -.5em;
		}
		.tree code:before,
		.tree span:before {
			top: -.55em;
		}

/* The root node doesn't connect upwards */
.tree > li {margin-top: 0;}
.tree > li:before,
.tree > li:after,
.tree > li > code:before,
.tree > li > span:before {
	outline: none;
}
</style>

<div class="wrap">
	<h1>Sitemap Generator</h1>
	<div class="accordion">
		<h3>Generate a new sitemap</h3>
		<div class="overflowing-div">
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
				<p><em>Note: After the initial generation, a cron job will generate the sitemap hourly.</em></p>
			</form>
		</div>
		<h3>Current sitemap tree visualization</h3>
		<div class="overflowing-div">
			<?php
			// Unable to use wp_kses because there is an infinite overlap of nested ul/li.
			echo $parser->generate_latest_tree_view(); // phpcs:ignore
			?>
		</div>
	</div>
</div>

	<?php
}
