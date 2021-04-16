<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Bp_Flag_Members
 * @subpackage Bp_Flag_Members/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bp_Flag_Members
 * @subpackage Bp_Flag_Members/includes
 * @author     Multidots <info@multidots.com>
 */
class Bp_Flag_Members {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bp_Flag_Members_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BPFM_PLUGIN_VERSION' ) ) {
			$this->version = BPFM_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bp-flag-members';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_globals();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bp_Flag_Members_Loader. Orchestrates the hooks of the plugin.
	 * - Bp_Flag_Members_i18n. Defines internationalization functionality.
	 * - Bp_Flag_Members_Admin. Defines all hooks for the admin area.
	 * - Bp_Flag_Members_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-flag-members-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-flag-members-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-flag-members-admin.php';

		/**
		 * The class responsible for defining the global variable of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-flag-members-globals.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-flag-members-public.php';

		$this->loader = new Bp_Flag_Members_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bp_Flag_Members_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bp_Flag_Members_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bp_Flag_Members_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'bpfm_add_custom_post_type' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bpfm_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bpfm_plugin_settings_content' );
		$this->loader->add_action( 'wp_ajax_bpfm_save_form_fields', $plugin_admin, 'bpfm_save_form_fields' );
		$this->loader->add_action( 'wp_ajax_bpfm_save_general_settings', $plugin_admin, 'bpfm_save_general_settings' );
		$this->loader->add_action( 'wp_ajax_bpfm_delete_flag', $plugin_admin, 'bpfm_delete_flag' );
		$this->loader->add_action( 'wp_ajax_bpfm_discard_flag', $plugin_admin, 'bpfm_discard_flag' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'bpfm_custom_modals' );
		$this->loader->add_action( 'wp_ajax_bpfm_warn_member', $plugin_admin, 'bpfm_warn_member' );
		$this->loader->add_action( 'wp_ajax_bpfm_ban_member', $plugin_admin, 'bpfm_ban_member' );
		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'bpfm_unban_user_profile_field' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'bpfm_unban_user_profile_field' );
		$this->loader->add_action( 'wp_ajax_bpfm_unban_member', $plugin_admin, 'bpfm_unban_member' );
		$this->loader->add_action( 'wp_ajax_bpfm_get_flag_details', $plugin_admin, 'bpfm_get_flag_details' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bp_Flag_Members_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'bp_directory_members_actions', $plugin_public, 'bpfm_member_reporting_on_directory', 10, 1 );
		$this->loader->add_action( 'bp_member_header_actions', $plugin_public, 'bpfm_member_reporting_on_profile' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'bpfm_custom_modals' );
		$this->loader->add_action( 'wp_ajax_bpfm_flag_member', $plugin_public, 'bpfm_flag_member' );
		$this->loader->add_filter( 'wp_authenticate_user', $plugin_public, 'bpfm_authenticate_user_login', 10, 2 );
		$this->loader->add_action( 'delete_user', $plugin_public, 'bpfm_delete_flags_on_user_delete', 10, 1 );

	}

	/**
	 * Registers a global variable of the plugin - bp-flagged-members
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function define_globals() {
		global $bp_flag_members;
		$bp_flag_members = new Bp_Flag_Members_Globals( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bp_Flag_Members_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
