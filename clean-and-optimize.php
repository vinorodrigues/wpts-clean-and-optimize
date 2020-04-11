<?php
/**
 * Plugin Name: TS Clean and Optimize
 * Plugin URI: http://tecsmith.com.au
 * Description: Things that erk me about Wordpress and their fixes
 * Author: Vino Rodrigues
 * Version: 0.0.03
 * Author URI: https://vinorodrigues.com
 * License: MIT
**/


include_once 'clnoptmz-opt.php';


/* ----- EMOJI ------------------------------------------------------------ */

/**
 * Disable the emoji
 */
function ts_disable_emoji() {
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'embed_head',          'print_emoji_detection_script' );
	remove_action( 'wp_print_styles',     'print_emoji_styles' );
	remove_action( 'admin_print_styles',  'print_emoji_styles' );

	remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
	remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );
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
	add_action('login_head',        'ts_custom_login_logo');
        add_filter('login_headerurl',   'ts_custom_login_url');
        add_filter('login_headertitle', '__return_empty_string');  // 'ts_custom_login_title');
}


/* ----- GOOGLE ANALYTICS ------------------------------------------------- */

/* function ts_ga_wp_footer() {
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
	add_action( 'wp_footer', 'ts_ga_wp_footer', 995 ); */


/* ----- REMOVE META ------------------------------------------------------ */

if (get_clnoptmz_setting('remove_meta')) {
	remove_action('wp_head',    'wp_generator');  // Template
	add_filter('the_generator', '__return_empty_string');  // Generator
	remove_action('wp_head',    'woo_version');  // WooCommerce
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

/* ----- Wordpress Core Updates -------------------------------------------- */

function ts_disable_updates_pre_transient($update) {
	include( ABSPATH . WPINC . '/version.php' );

	$current = new stdClass;
	$current->updates = array();
	$current->version_checked = $wp_version;
	$current->last_checked = time();

	return $current;
}

function ts_disable_updates_admin_init() {
	if (!function_exists( 'remove_action' )) return;

	// Hide maintenance and update nag
	// remove_action( 'admin_notices',         'update_nag', 3 );
	// remove_action( 'network_admin_notices', 'update_nag', 3 );
	// remove_action( 'admin_notices',         'maintenance_nag' );
	// remove_action( 'network_admin_notices', 'maintenance_nag' );

	// Disable Core Updates 2.8 to 3.0
	add_filter( 'pre_option_update_core',   '__return_null' );
	remove_action( 'wp_version_check',      'wp_version_check' );
	remove_action( 'admin_init',            '_maybe_update_core' );
	wp_clear_scheduled_hook( 'wp_version_check' );

	// 3.0
	wp_clear_scheduled_hook( 'wp_version_check' );

	// 3.7+
	remove_action( 'wp_maybe_auto_update',  'wp_maybe_auto_update' );
	remove_action( 'admin_init',            'wp_maybe_auto_update' );
	remove_action( 'admin_init',            'wp_auto_update_core' );
	wp_clear_scheduled_hook( 'wp_maybe_auto_update' );
}

function ts_disable_updates_cron_event($event) {
	switch( $event->hook ) {
		case 'wp_version_check':
		case 'wp_maybe_auto_update':
			$event = false;
			break;
	}
	return $event;
}

if (get_clnoptmz_setting('remove_core_updates')) {
	add_action('admin_init',                           'ts_disable_updates_admin_init');

	// Disable Core Updates 2.8 to 3.0
	add_filter('pre_transient_update_core',            'ts_disable_updates_pre_transient');
	add_filter('pre_site_transient_update_core',       'ts_disable_updates_pre_transient');

	// Filter schedule checks
	add_action('schedule_event',                       'ts_disable_updates_cron_event');

	// Disable All Automatic Updates 3.7+
	add_filter( 'automatic_updater_disabled',          '__return_true' );
	add_filter( 'allow_minor_auto_core_updates',       '__return_false' );
	add_filter( 'allow_major_auto_core_updates',       '__return_false' );
	add_filter( 'allow_dev_auto_core_updates',         '__return_false' );
	add_filter( 'auto_update_core',                    '__return_false' );
	add_filter( 'wp_auto_update_core',                 '__return_false' );
	add_filter( 'auto_core_update_send_email',         '__return_false' );
	add_filter( 'send_core_update_notification_email', '__return_false' );
	add_filter( 'automatic_updates_send_debug_email',  '__return_false' );
	add_filter( 'automatic_updates_is_vcs_checkout',   '__return_true' );
	add_filter( 'automatic_updates_send_debug_email ', '__return_false', 1 );

	if (!defined('WP_AUTO_UPDATE_CORE'))
		define('WP_AUTO_UPDATE_CORE', false);
	if(!defined('AUTOMATIC_UPDATER_DISABLED'))
		define('AUTOMATIC_UPDATER_DISABLED', true);
}

/* ===== =================================================================== */

/* ----- Obscure login screen error messages ------------------------------- */
function ts_login_err_obscure() {
	return __('<strong>Sorry</strong> thats not right.');
}

add_filter('login_errors', 'ts_login_err_obscure');

/* ----- Disable the theme / plugin text editor in Admin ------------------- */
define('DISALLOW_FILE_EDIT', true);

/* ----- Remove the version number of WP ----------------------------------- */
remove_action('wp_head', 'wp_generator');


/* eof */
