<?php
/**
 * Plugin Name: TS Clean and Optimize
 * Plugin URI: http://tecsmith.com.au
 * Description: Things that erk me about Wordpress and their fixes
 * Author: Vino Rodrigues
 * Version: 0.0.02
 * Author URI: http://vinorodrigues.com
**/


include_once 'clnoptmz-opt.php';


/* ----- EMOJI ------------------------------------------------------------ */

/**
 * Disable the emoji
 */
function ts_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

if (get_clnoptmz_setting('disable_emoji'))
	add_action( 'init', 'ts_disable_emoji' );

/**
 * Remove the TinyMCE emoji
 */
function ts_disable_emoji_tinymce( $plugins ) {
	if (is_array($plugins))
		if (isset($plugins['wpemoji'])) unset($plugins['wpemoji']);
	return $plugins;
}

if (get_clnoptmz_setting('disable_emoji'))
	add_filter( 'tiny_mce_plugins', 'ts_disable_emoji_tinymce' );


/* ----- SVG -------------------------------------------------------------- */

/**
 * @see: https://css-tricks.com/snippets/wordpress/allow-svg-through-wordpress-media-uploader/
 */
function ts_svg_upload_mimes($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}

if (get_clnoptmz_setting('enable_svg'))
	add_filter( 'upload_mimes', 'ts_svg_upload_mimes' );


/* ----- WP Logo in admin bar --------------------------------------------- */

function ts_remove_wp_logo() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
}

if (get_clnoptmz_setting('disable_wp_logo')) {
	add_action( 'wp_before_admin_bar_render', 'ts_remove_wp_logo' );
}


/* ----- AUTOP ------------------------------------------------------------ */

if (get_clnoptmz_setting('disable_autop')) {
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_excerpt', 'wpautop' );
}


/* ----- LOGIN LOGO ------------------------------------------------------- */

function ts_custom_login_logo() {
	?>
	<style type="text/css">
		.login h1 a {
			width: 100%;
			xxxheight: auto;
			background-image: url("<?php echo get_clnoptmz_setting('login_logo')  ?>") !important;
			background-size: contain !important;
		}
	</style>
	<?php
}

function ts_custom_login_url() { return home_url(); }

// function ts_custom_login_title() { return ''; }

if (!empty( get_clnoptmz_setting('login_logo') )) {
	add_action('login_head', 'ts_custom_login_logo');
        add_filter('login_headerurl', 'ts_custom_login_url');
        add_filter('login_headertitle', '__return_empty_string');  // 'ts_custom_login_title');
}


/* ----- GOOGLE ANALYTICS ------------------------------------------------- */

function ts_ga_wp_footer() {
	$value = get_clnoptmz_setting('google_analytics_key');
?>
<!-- Google Analytics -->
<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?= $value ?>', 'auto');
  ga('send', 'pageview');
</script>
<?php
}

if (!empty( get_clnoptmz_setting('google_analytics_key') ))
	add_action( 'wp_footer', 'ts_ga_wp_footer', 995 );


/* ----- REMOVE META ------------------------------------------------------ */

if (get_clnoptmz_setting('remove_meta')) {
	remove_action('wp_head', 'wp_generator');  // Template
	add_filter('the_generator','__return_empty_string');  // Generator
	remove_action('wp_head', 'woo_version');  // WooCommerce
}

/* ----- REMOVE RSS ------------------------------------------------------- */

if (get_clnoptmz_setting('remove_rss')) {
	remove_action('wp_head', 'feed_links', 2);
}

/* ----- REMOVE ADMIN BAR -------------------------------------------------- */

if (get_clnoptmz_setting('remove_admin_bar')) {
	add_filter( 'show_admin_bar' , '__return_false');
}

/* ----- REMOVE BLOG CLIENTS META ------------------------------------------ */

if (get_clnoptmz_setting('remove_blog_clients')) {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
}

/* eof */
