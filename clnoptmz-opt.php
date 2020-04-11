<?php

if (!defined('CLNOPTMZ_PLUGIN_SLUG'))
	define( 'CLNOPTMZ_PLUGIN_SLUG', basename( str_replace( ' ', '%20', plugins_url( '', __FILE__ ) ) ) );


/**
 */
function get_clnoptmz_setting($setting = false, $default = null) {
	global $ts_clnoptmz_settings;

	if (!isset($ts_clnoptmz_settings))
		$ts_clnoptmz_settings = get_option( 'ts_clnoptmz_settings' );

	if (!$setting) return $ts_clnoptmz_settings;
	elseif (isset($ts_clnoptmz_settings[$setting])) return $ts_clnoptmz_settings[$setting];
	else return $default;
}

/**
 * Tecsmith Options
 */
function ts_clnoptmz_admin_menu() {
	if (function_exists('add_tecsmith_page'))
		add_tecsmith_page(
			__('TS Clean and Optimize'),
			__('Clean and Optimize'),
			'manage_options',
			CLNOPTMZ_PLUGIN_SLUG,
			'ts_clnoptmz_options_page',
			'dashicons-wordpress-alt',
			999 );
	else
		add_plugins_page(
			__('TS Clean and Optimize'),
			__('Clean and Optimize'),
			'manage_options',
			CLNOPTMZ_PLUGIN_SLUG,
			'ts_clnoptmz_options_page',
			'dashicons-wordpress-alt',
			999 );
}

add_action( 'admin_menu', 'ts_clnoptmz_admin_menu' );

/*
 * Init settings
 */
function ts_clnoptmz_settings_init() {

	register_setting( 'ts_clnoptmz_plugin_settings', 'ts_clnoptmz_settings' );

	// ENABLE STUFF

	add_settings_section(
		'enabler',
		__( 'Enable Stuff' ),
		'__return_false',
		'ts_clnoptmz_plugin_settings'
		);

	add_settings_field(
		'enable_svg',
		__( 'Enable SVG support' ),
		'ts_clnoptmz_enable_svg_render',
		'ts_clnoptmz_plugin_settings',
		'enabler'
		);

	add_settings_field(
		'login_logo',
		__('Login Logo'),
		'ts_clnoptmz_login_logo_render',
		'ts_clnoptmz_plugin_settings',
		'enabler'
		);

	/* add_settings_field(
		'google_analytics_key',
		__( 'Enable Google Analytics' ),
		'ts_clnoptmz_google_analytics_key_render',
		'ts_clnoptmz_plugin_settings',
		'enabler'
		); */

	// DISABLE STUFF

	add_settings_section(
		'disabler',
		__( 'Disable Stuff' ),
		'__return_false',
		'ts_clnoptmz_plugin_settings'
		);

	add_settings_field(
		'disable_emoji',
		__( 'Disable Emoji support' ),
		'ts_clnoptmz_disable_emoji_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'disable_wp_logo',
		__('Disable Admin WP Logo'),
		'ts_clnoptmz_disable_wp_logo_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'disable_autop',
		__('Disable AutoP'),
		'ts_clnoptmz_disable_autop_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'remove_meta',
		__('Remove WP Meta'),
		'ts_clnoptmz_remove_meta_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'remove_rss',
		__('Remove RSS Feed links'),
		'ts_clnoptmz_remove_rss_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'remove_admin_bar',
		__('Remove Admin Bar'),
		'ts_clnoptmz_remove_admin_bar_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'remove_blog_clients',
		__('Remove Blog Clients meta'),
		'ts_clnoptmz_remove_blog_clients_render',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);

	add_settings_field(
		'remove_core_updates',
		__('Remove Wordpress Core updates'),
		'ts_clnoptmz_remove_core_updates',
		'ts_clnoptmz_plugin_settings',
		'disabler'
		);
}

add_action( 'admin_init', 'ts_clnoptmz_settings_init' );

function __ts_clnoptmz_checkbox_render($name, $description = false) {
	$value = get_clnoptmz_setting($name);
	?>
	<fieldset>
		<label for="<?= $name ?>">
			<input type="checkbox" name="ts_clnoptmz_settings[<?= $name ?>]" <?php
				checked( $value, 1 ); ?> value="1" id="<?= $name ?>">
			<?php if ($description) { echo '<span class="description">' .
				$description . '</span>'; } ?>
		</label>
	</fieldset>
	<?php

}

function ts_clnoptmz_disable_emoji_render() {
	__ts_clnoptmz_checkbox_render('disable_emoji');
}

function ts_clnoptmz_enable_svg_render() {
	__ts_clnoptmz_checkbox_render('enable_svg');
}

function ts_clnoptmz_disable_wp_logo_render() {
	__ts_clnoptmz_checkbox_render('disable_wp_logo');
}

function ts_clnoptmz_disable_autop_render() {
	__ts_clnoptmz_checkbox_render('disable_autop');
}

function ts_clnoptmz_remove_meta_render() {
	__ts_clnoptmz_checkbox_render('remove_meta');
}

function ts_clnoptmz_remove_rss_render() {
	__ts_clnoptmz_checkbox_render('remove_rss');
}

function ts_clnoptmz_remove_admin_bar_render() {
	__ts_clnoptmz_checkbox_render('remove_admin_bar');
}

function ts_clnoptmz_remove_blog_clients_render() {
	__ts_clnoptmz_checkbox_render('remove_blog_clients');
}

function ts_clnoptmz_remove_core_updates() {
	__ts_clnoptmz_checkbox_render('remove_core_updates');
}

function ts_clnoptmz_login_logo_render() {
	$value = get_clnoptmz_setting('login_logo');
	?>
	<input type="text" name="ts_clnoptmz_settings[login_logo]"
		id="clnoptmz-login-logo" class="regular-text"
		value="<?= esc_attr( $value ) ?>">
	<a class="button" onclick="clnoptmz_upload_image('clnoptmz-login-logo');"><?= __('Select Image') ?></a>
	<a class="button" onclick="clnoptmz_clear_image('clnoptmz-login-logo')"><?= __('Clear') ?></a>
	<?php
}

/* function ts_clnoptmz_google_analytics_key_render() {
	$value = get_clnoptmz_setting('google_analytics_key');
	?>
	<input type="text" name="ts_clnoptmz_settings[google_analytics_key]"
		id="google_analytics_key" class="irregular-text"
		maxlength="18" placeholder="UA-xxxxxxxx-y" pattern="UA-[0-9]{4,10}-[0-9]{1,4}"
		value="<?= esc_attr($value) ?>">
	<?php
} */

function ts_clnoptmz_options_page() {
	global $title, $TECSMITH_SVG;

	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

?>
<div class="wrap">
	<h2><?php
		if (function_exists('tecsmith_logo')) tecsmith_logo('width:32px;height:32px;vertical-align:middle;');
		echo $title;
	?></h2>
	<?php settings_errors(); ?>

	<form action='options.php' method='post'>
		<?php
			settings_fields( 'ts_clnoptmz_plugin_settings' );
			do_settings_sections( 'ts_clnoptmz_plugin_settings' );
			submit_button();
		?>
	</form>
</div>
<?php
}


function ts_clnoptmz_plugin_action_links($links) {
	if ( function_exists('add_tecsmith_page') ) $link = 'admin.php';
	else $link = 'plugins.php';
	$link .= '?page='.CLNOPTMZ_PLUGIN_SLUG;
	$link = '<a href="'.esc_url( get_admin_url(null, $link) ).'">'.__('Settings').'</a>';
	array_unshift($links, $link);
	return $links;
}

add_filter( 'plugin_action_links_'.dirname(plugin_basename(__FILE__)).'/clean-and-optimize.php',
	'ts_clnoptmz_plugin_action_links' );

function ts_clnoptmz_admin_scripts_1() {
	wp_enqueue_media();
}

// @see: http://stackoverflow.com/questions/25225132/wordpress-settings-page-upload-logo
function ts_clnoptmz_admin_footer() {
?>
<script type="text/javascript">
	var clnoptmz_uploader = new Array();

	function clnoptmz_upload_image(id) {
		if ( !clnoptmz_uploader[id] ) {

			clnoptmz_uploader[id] = wp.media.frames.file_frame = wp.media({
				className: 'media-frame',
				frame: 'select',
				multiple: false,
				title: '<?= __('Select Image') ?>',
				button: { text: '<?= __('Select Image') ?>' },
				library: { type: 'image' }
			});

			clnoptmz_uploader[id].on('select', function() {
				attachment = clnoptmz_uploader[id].state().get('selection').first().toJSON();
	 			var url = attachment['url'];
				jQuery('#'+id).val(url);
			});
		}

		clnoptmz_uploader[id].open();
	}

	function clnoptmz_clear_image(id) {
		jQuery('#'+id).val('');
	}
</script>
<?php
}

function ts_clnoptmz_admin_scripts_2() {
?>
<style>
	#wpfooter {
		position: static;
		bottom: auto;
		left: auto;
		right: auto;
		background-color: rgba(127,127,127,0.05);
	}
</style>
<?php
}

if (isset($_GET['page']) && $_GET['page'] == CLNOPTMZ_PLUGIN_SLUG) {
	add_action('admin_print_scripts', 'ts_clnoptmz_admin_scripts_1');
	add_action('admin_footer',        'ts_clnoptmz_admin_footer');
}
add_action('admin_print_scripts', 'ts_clnoptmz_admin_scripts_2', 999);

// --- eof ---
