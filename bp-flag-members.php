<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.multidots.com/
 * @since             1.0.0
 * @package           Bp_Flag_Members
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Flag Members
 * Plugin URI:        https://www.multidots.com/
 * Description:       This plugin allows the members to flag/report other members on the directory.
 * Version:           1.0.0
 * Author:            Multidots
 * Author URI:        https://www.multidots.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-flag-members
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BPFM_PLUGIN_VERSION', '1.0.0' );

if ( ! defined( 'BPFM_PLUGIN_PATH' ) ) {
	define( 'BPFM_PLUGIN_PATH', plugin_dir_path(__FILE__) );
}

if ( ! defined( 'BPFM_TEXT_DOMAIN' ) ) {
	define( 'BPFM_TEXT_DOMAIN', 'bp-flag-members' );
}

if ( ! defined( 'BPFM_PLUGIN_URL' ) ) {
	define( 'BPFM_PLUGIN_URL', plugin_dir_url(__FILE__) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bp-flag-members-activator.php
 */
function activate_bp_flag_members() {
	require_once BPFM_PLUGIN_PATH . 'includes/class-bp-flag-members-activator.php';
	Bp_Flag_Members_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bp-flag-members-deactivator.php
 */
function deactivate_bp_flag_members() {
	require_once BPFM_PLUGIN_PATH . 'includes/class-bp-flag-members-deactivator.php';
	Bp_Flag_Members_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bp_flag_members' );
register_deactivation_hook( __FILE__, 'deactivate_bp_flag_members' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_flag_members() {

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require BPFM_PLUGIN_PATH . 'includes/class-bp-flag-members.php';
	$plugin = new Bp_Flag_Members();
	$plugin->run();

}

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires BuddyPress to be installed and active
 */
add_action('plugins_loaded', 'bpfm_plugin_init');
function bpfm_plugin_init() {
	$bp_active = in_array( 'buddypress/bp-loader.php', get_option( 'active_plugins' ) );
	if ( current_user_can('activate_plugins') && $bp_active !== true ) {
		add_action('admin_notices', 'bpfm_plugin_admin_notice');
	} else {
		run_bp_flag_members();
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bpfm_plugin_links' );
	}
}

/**
 * Show admin notice in case of BuddyPress plguin is missing
 */
function bpfm_plugin_admin_notice() {
	$bpfm_plugin = 'BuddyPress Flag Members';
	$bp_plugin = 'BuddyPress';

	echo '<div class="error"><p>'
	. sprintf(__('%1$s is ineffective as it requires %2$s to be installed and active.', BPFM_TEXT_DOMAIN), '<strong>' . esc_html( $bpfm_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>')
	. '</p></div>';
	if ( isset($_GET['activate'] ) ) unset( $_GET['activate'] );
}

/**
 * Settings link on plugin listing page
 */
function bpfm_plugin_links( $links ) {
	$bpfm_links = array(
		'<a href="'.admin_url('users.php?page=bp-flag-members').'">'.__( 'Settings', BPFM_TEXT_DOMAIN ).'</a>'
	);
	return array_merge( $links, $bpfm_links );
}

if( !  function_exists( 'debug' ) ) {
	function debug( $params ) {
		echo '<pre>';
		print_r( $params );
		echo '</pre>';
	}
}