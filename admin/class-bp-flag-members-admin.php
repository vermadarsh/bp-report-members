<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Bp_Flag_Members
 * @subpackage Bp_Flag_Members/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bp_Flag_Members
 * @subpackage Bp_Flag_Members/admin
 * @author     Multidots <info@multidots.com>
 */
class Bp_Flag_Members_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_settings_tabs = array();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if( stripos( $_SERVER['REQUEST_URI'], 'bp-flag-members' ) !== false ) {
			wp_enqueue_style( $this->plugin_name . '-select2', BPFM_PLUGIN_URL . 'admin/css/select2.min.css' );
			wp_enqueue_style( $this->plugin_name . '-font-awesome', BPFM_PLUGIN_URL . 'admin/css/font-awesome.min.css' );
			wp_enqueue_style( $this->plugin_name, BPFM_PLUGIN_URL . 'admin/css/bp-flag-members-admin.css' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if( stripos( $_SERVER['REQUEST_URI'], 'bp-flag-members' ) !== false ||  stripos( $_SERVER['REQUEST_URI'], 'user-edit.php' ) !== false) {
			wp_enqueue_script( $this->plugin_name . '-select2-js', BPFM_PLUGIN_URL . 'admin/js/select2.min.js' );
			wp_enqueue_script( $this->plugin_name, BPFM_PLUGIN_URL . 'admin/js/bp-flag-members-admin.js', array( 'jquery' ) );
			wp_localize_script(
				$this->plugin_name,
				'BPFM_Admin_JS_Obj',
				array(
					'ajaxurl'						=>	admin_url( 'admin-ajax.php' ),
					'loader_url'					=>	includes_url( 'images/spinner-2x.gif' ),
					'reason_input_placeholder'		=>	__( 'Add Reason', BPFM_TEXT_DOMAIN ),
					'remove_reason_btn_value'		=>	__( 'Remove', BPFM_TEXT_DOMAIN ),
					'flag_del_cnf_msg'				=>	__( 'Are you sure you want to elete this flag?', BPFM_TEXT_DOMAIN ),
					'member_ban_cnf_msg'			=>	__( 'Are you sure you want to ban this member?', BPFM_TEXT_DOMAIN ),
					'approve_flag_txt'				=>	__( 'Approve Flag: ', BPFM_TEXT_DOMAIN ),
					'view_flag_txt'					=>	__( 'Flag: ', BPFM_TEXT_DOMAIN ),
					'fetch_flag_wait_msg'			=>	__( 'Please wait while the flag details are loading...', BPFM_TEXT_DOMAIN ),
				)
			);
		}

	}

	/**
	 * Register a CPT - flagged-members
	 *
	 * @since    1.0.0
	 */
	public function bpfm_add_custom_post_type() {

		global $bp_flag_members;
		$labels = array(
			'name'					=>	__( 'Flagged Members', BPFM_TEXT_DOMAIN ),
			'singular_name'			=>	__( 'Flagged Member', BPFM_TEXT_DOMAIN ),
			'menu_name'				=>	__( 'Flagged Members', BPFM_TEXT_DOMAIN ),
			'name_admin_bar'		=>	__( 'Flagged Member', BPFM_TEXT_DOMAIN ),
			'add_new'				=>	__( 'Add New', BPFM_TEXT_DOMAIN ),
			'add_new_item'			=>	__( 'Add New Flagged Member', BPFM_TEXT_DOMAIN ),
			'new_item'				=>	__( 'New Flagged Member', BPFM_TEXT_DOMAIN ),
			'edit_item'				=>	__( 'Edit Flagged Member', BPFM_TEXT_DOMAIN ),
			'view_item'				=>	__( 'View Flagged Member', BPFM_TEXT_DOMAIN ),
			'all_items'				=>	__( 'All Flagged Members', BPFM_TEXT_DOMAIN ),
			'search_items'			=>	__( 'Search Flagged Members', BPFM_TEXT_DOMAIN ),
			'parent_item_colon'		=>	__( 'Parent Flagged Members:', BPFM_TEXT_DOMAIN ),
			'not_found'				=>	__( 'No Flagged Members Found.', BPFM_TEXT_DOMAIN ),
			'not_found_in_trash'	=>	__( 'No Flagged Members Found In Trash.', BPFM_TEXT_DOMAIN )
		);

		$args = array(
			'labels'				=>	$labels,
			'public'				=>	true,
			'menu_icon'				=>	'dashicons-asd',
			'publicly_queryable'	=>	true,
			'show_ui'				=>	true,
			'show_in_menu'			=>	false,
			'query_var'				=>	true,
			'rewrite'				=>	array( 'slug' => $bp_flag_members->cpt_slug ),
			'capability_type'		=>	'post',
			'has_archive'			=>	true,
			'hierarchical'			=>	false,
			'menu_position'			=>	null,
			'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);
		register_post_type( $bp_flag_members->cpt_slug, $args );

		$set = get_option( 'cpt_' . $bp_flag_members->cpt_slug . '_flushed_rewrite_rules' );
		if ( $set !== 'yes' ){
			flush_rewrite_rules( false );
			update_option( 'cpt_' . $bp_flag_members->cpt_slug . '_flushed_rewrite_rules', 'yes' );
		}
		
	}

	/**
	 * Register a menu page to handle plugin settings - under Users
	 *
	 * @since    1.0.0
	 */
	public function bpfm_settings_page() {
		add_users_page( __( 'BuddyPress Flag Members Settings', BPFM_TEXT_DOMAIN ), __( 'BuddyPress Flag Members', BPFM_TEXT_DOMAIN ), 'manage_options', $this->plugin_name, array( $this, 'bpfm_admin_settings_page' ) );
	}

	/**
	 * Function called to create settings page under Users page
	 *
	 * @since    1.0.0
	 */
	public function bpfm_admin_settings_page() {
		$tab = isset($_GET['tab']) ? $_GET['tab'] : $this->plugin_name;
		?>
		<div class="wrap">
			<h2><?php _e( 'BuddyPress Flag Members', BPFM_TEXT_DOMAIN );?></h2>
			<?php $this->bpfm_plugin_settings_tabs();
			do_settings_sections( $tab );?>
		</div>
		<?php
	}

	/**
	 * Function called to create settings tabs
	 *
	 * @since    1.0.0
	 */
	public function bpfm_plugin_settings_tabs() {
		$current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->plugin_name;
		echo '<h2 class="nav-tab-wrapper">';
		foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" id="' . $tab_key . '-tab" href="?page=' . $this->plugin_name . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}
		echo '</h2>';
	}

	/**
	 * Function called to create settings page content
	 *
	 * @since    1.0.0
	 */
	public function bpfm_plugin_settings_content() {
		//Flagged Users Listing Tab
		$this->plugin_settings_tabs[ $this->plugin_name ] = __( 'Flagged Members', BPFM_TEXT_DOMAIN );
		register_setting( $this->plugin_name, $this->plugin_name );
		add_settings_section( $this->plugin_name . '-section', ' ', array(&$this, 'bpfm_list'), $this->plugin_name );

		//Flag User General Settings Tab
		$this->plugin_settings_tabs['general-settings'] = __( 'General Settings', BPFM_TEXT_DOMAIN );
		register_setting( 'general-settings', 'general-settings');
		add_settings_section( 'general-settings-section', ' ', array(&$this, 'bpfm_general_settings'), 'general-settings' );

		//Flag User Form Fields Tab
		$this->plugin_settings_tabs['form-fields'] = __( 'Form Fields', BPFM_TEXT_DOMAIN );
		register_setting( 'form-fields', 'form-fields');
		add_settings_section( 'form-fields-section', ' ', array(&$this, 'bpfm_form_fields'), 'form-fields' );
	}

	/**
	 * Function called to show the list of flagged users listing.
	 *
	 * @since    1.0.0
	 */
	public function bpfm_list() {

		$file = BPFM_PLUGIN_PATH . 'admin/includes/bp-flag-members-list.php';
		if ( file_exists( $file ) ) {
			require_once( $file );
			$flagged_members_list_obj = new Bp_Flagged_Members_List();
			$flagged_members_list_obj->prepare_items();
			$flagged_members_list_obj->display();
		}

	}

	/**
	 * Function called to manage form fields
	 *
	 * @since    1.0.0
	 */
	public function bpfm_form_fields() {

		$file = BPFM_PLUGIN_PATH . 'admin/includes/bp-flag-members-form-fields.php';
		if ( file_exists( $file ) ) {
			require_once( $file );
		}

	}

	/**
	 * Function called to manage plugin general settings
	 *
	 * @since    1.0.0
	 */
	public function bpfm_general_settings() {

		$file = BPFM_PLUGIN_PATH . 'admin/includes/bp-flag-members-general-settings.php';
		if ( file_exists( $file ) ) {
			require_once( $file );
		}

	}

	/**
	 * AJAX called to save form fields
	 *
	 * @since    1.0.0
	 */
	public function bpfm_save_form_fields() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_save_form_fields' ) {
			$form_fields = wp_unslash( $_POST[ 'form_fields' ] );

			$plugin_admin_settings = get_option( 'bp_flag_members_settings' );
			if( empty( $plugin_admin_settings ) ) {
				$plugin_admin_settings = array();
			}

			$plugin_admin_settings['form_fields'] = $form_fields;
			update_option( 'bp_flag_members_settings', $plugin_admin_settings );

			$result = array(
				'message'	=>	'bpfm-form-fields-saved'
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * AJAX called to save general settings
	 *
	 * @since    1.0.0
	 */
	public function bpfm_save_general_settings() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_save_general_settings' ) {
			
			$hide_on_member_directory = sanitize_text_field( $_POST[ 'hide_on_member_directory' ] );
			$report_button_text = sanitize_text_field( $_POST[ 'report_button_text' ] );

			$plugin_admin_settings = get_option( 'bp_flag_members_settings' );
			if( empty( $plugin_admin_settings ) ) {
				$plugin_admin_settings = array();
			}

			$plugin_admin_settings['general_settings']['hide_on_member_directory'] = $hide_on_member_directory;
			$plugin_admin_settings['general_settings']['report_button_text'] = $report_button_text;
			update_option( 'bp_flag_members_settings', $plugin_admin_settings );

			$result = array(
				'message'	=>	'bpfm-general-settings-saved'
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * AJAX called to delete the flag
	 *
	 * @since    1.0.0
	 */
	public function bpfm_delete_flag() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_delete_flag' ) {
			
			$flag_id = sanitize_text_field( $_POST[ 'flag_id' ] );
			$flag = get_post( $flag_id );
			wp_delete_post( $flag_id, true );

			// if the flagged member is banned, unban automatically
			$flagged_member = $flag->post_parent;
			delete_user_meta( $flagged_member, '_bpfm_banned_on' );
			delete_user_meta( $flagged_member, '_bpfm_ban_duration' );
			delete_user_meta( $flagged_member, '_bpfm_banned_until' );

			global $bp_flag_members;
			$posts_left = wp_count_posts( $bp_flag_members->cpt_slug );
			$posts_left_count = $posts_left->publish;

			$remaining_posts_html = '';
			if( $posts_left_count == 0 ) {
				$remaining_posts_html .= '<tr class="no-items"><td class="colspanchange" colspan="5">' . __( 'No Flagged Members Found.', BPFM_TEXT_DOMAIN ) . '</td></tr>';
			}

			$result = array(
				'message'				=>	'bpfm-flag-deleted',
				'remaining_posts_html'	=>	$remaining_posts_html
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * AJAX called to discard the flag
	 *
	 * @since    1.0.0
	 */
	public function bpfm_discard_flag() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_discard_flag' ) {
			global $bp_flag_members;
			$flag_id = sanitize_text_field( $_POST[ 'flag_id' ] );
			$new_status = 'discarded';
			$new_status_label = ! empty( $new_status ) ? $bp_flag_members->flag_statuses[ $new_status ] : '';

			update_post_meta( $flag_id, '_bpfm_flag_status', $new_status );

			$result = array(
				'message'		=>	'bpfm-flag-discarded',
				'new_status'	=>	$new_status_label
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * Modals used to report members
	 *
	 * @since    1.0.0
	 */
	public function bpfm_custom_modals() {

		if( stripos( $_SERVER['REQUEST_URI'], 'bp-flag-members' ) !== false ) {
			$report_user_modal = BPFM_PLUGIN_PATH . 'admin/includes/modals/bpfm-approve-flag.php';
			if( file_exists( $report_user_modal ) ) {
				include $report_user_modal;
			}

			$view_flag_modal = BPFM_PLUGIN_PATH . 'admin/includes/modals/bpfm-view-flag.php';
			if( file_exists( $view_flag_modal ) ) {
				include $view_flag_modal;
			}
		}

	}

	/**
	 * AJAX called to warn a member
	 *
	 * @since    1.0.0
	 */
	public function bpfm_warn_member() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_warn_member' ) {
			global $bp_flag_members;
			$flag_id = sanitize_text_field( $_POST[ 'flag_id' ] );
			$flag = get_post( $flag_id );
			$flagged_member = $flag->post_parent;

			$flag_reasons = get_post_meta( $flag_id, '_bpfm_reasons', true );
			$flag_reasons_html = '';
			$flag_reasons_html .= '<ul>';
			foreach( $flag_reasons as $flag_reason ) {
				$reason = $flag_reason['reason'];
				$description = $flag_reason['description'];
				$flag_reasons_html .= '<li>' . $reason;
				if( $description != '' ) {
					$flag_reasons_html .= ' (' . $description . ')';
				}
				$flag_reasons_html .= '</li>';
			}
			$flag_reasons_html .= '</ul>';

			$flagged_member_data = get_userdata( $flagged_member );
			$to = $flagged_member_email = $flagged_member_data->data->user_email;
			$subject = get_bloginfo( 'title' ) . ' - ' . __( 'Warn Member', BPFM_TEXT_DOMAIN );
			$admin_email = get_option( 'admin_email' );
			
			$message = '<p>Dear ' . $flagged_member_data->data->display_name . ',<br />';
			$message .= 'Your user account is under investigation and at a risk of being banned. Please review our comments below.<br />';
			
			$message .= $flag_reasons_html . '<br />';
			
			$message .= 'If you believe this message has been sent in error, or if you have any questions concerning this notification, please email us at <a href="' . $admin_email . '">' . $admin_email . '</a>.</p>';

			wp_mail( $to, $subject, $message );
			update_post_meta( $flag_id, '_bpfm_flag_status_action', 'warned' );
			$html = '<input type="button" class="button button-secondary bpfm-delete-flag" data-fid="' . $flag_id . '" value="' . __( 'Delete Flag', BPFM_TEXT_DOMAIN ) . '">';

			$new_status = 'approved';
			$new_status_label = ! empty( $new_status ) ? $bp_flag_members->flag_statuses[ $new_status ] : '';
			update_post_meta( $flag_id, '_bpfm_flag_status', $new_status );

			$result = array(
				'message'		=>	'bpfm-flag-approved',
				'message_txt'	=>	__( 'The member has been warned !!', BPFM_TEXT_DOMAIN ),
				'status_action'	=>	$bp_flag_members->flag_status_actions[ 'warned' ],
				'new_status'	=>	$new_status_label
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * AJAX called to ban a member
	 *
	 * @since    1.0.0
	 */
	public function bpfm_ban_member() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_ban_member' ) {
			global $bp_flag_members;
			$flag_id = sanitize_text_field( $_POST[ 'flag_id' ] );
			$ban_duration = sanitize_text_field( $_POST[ 'ban_duration' ] );
			$ban_message = sanitize_text_field( $_POST[ 'ban_message' ] );
			
			$flag = get_post( $flag_id );
			$flagged_member = $flag->post_parent;

			$banned_on_date = date( 'Y-m-d H:i:s' );
			$timestamp = time() + ( 60 * 60 * 24 * $ban_duration );
			$banned_until_date = date( 'Y-m-d H:i:s', $timestamp );

			$banned_until_date_human_readable = date("F jS, Y", strtotime( $banned_until_date ) );

			update_user_meta( $flagged_member, '_bpfm_banned_on', $banned_on_date );
			update_user_meta( $flagged_member, '_bpfm_ban_duration', $ban_duration );
			update_user_meta( $flagged_member, '_bpfm_banned_until', $banned_until_date );

			$flag_reasons = get_post_meta( $flag_id, '_bpfm_reasons', true );
			$flag_reasons_html = '';
			$flag_reasons_html .= '<ul>';
			foreach( $flag_reasons as $flag_reason ) {
				$reason = $flag_reason['reason'];
				$description = $flag_reason['description'];
				$flag_reasons_html .= '<li>' . $reason;
				if( $description != '' ) {
					$flag_reasons_html .= ' (' . $description . ')';
				}
				$flag_reasons_html .= '</li>';
			}
			$flag_reasons_html .= '</ul>';

			$flagged_member_data = get_userdata( $flagged_member );
			$to = $flagged_member_email = $flagged_member_data->data->user_email;
			$subject = get_bloginfo( 'title' ) . ' - ' . __( 'Account suspended', BPFM_TEXT_DOMAIN );
			$admin_email = get_option( 'admin_email' );
			
			$message = '<p>Dear ' . $flagged_member_data->data->display_name . ',<br />';
			$message .= 'Your account has been suspended for the following reason. You will therefore be unable to login until your access has been reinstated.<br />';
			
			$message .= $flag_reasons_html . '<br />';

			if( ! empty( $ban_message ) ) $message .= $ban_message . '<br />';

			$message .= 'This ban will be lifted: ' . $banned_until_date_human_readable . '<br />';
			$message .= 'If you believe this message has been sent in error, or if you have any questions concerning this notification, please email us at <a href="' . $admin_email . '">' . $admin_email . '</a>.</p>';

			wp_mail( $to, $subject, $message );
			update_post_meta( $flag_id, '_bpfm_flag_status_action', 'banned' );

			$new_status = 'approved';
			$new_status_label = ! empty( $new_status ) ? $bp_flag_members->flag_statuses[ $new_status ] : '';
			update_post_meta( $flag_id, '_bpfm_flag_status', $new_status );

			$user_edit_url = get_edit_user_link( $flagged_member ) . '#bpfm-unban-member';
			$html = '<button type="button" class="button button-secondary bpfm-unban-member" data-url="' . $user_edit_url . '"><span><i class="fa fa-ban" aria-hidden="true"></i></span></button>';

			$result = array(
				'message'		=>	'bpfm-flag-approved',
				'message_txt'	=>	__( 'The member has been banned !!', BPFM_TEXT_DOMAIN ),
				'status_action'	=>	$bp_flag_members->flag_status_actions[ 'banned' ],
				'actions_html'	=>	$html,
				'new_status'	=>	$new_status_label
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * Create field in user profile to unban the user
	 *
	 * @since    1.0.0
	 */
	public function bpfm_unban_user_profile_field( $user ) {

		$banned_until = get_user_meta( $user->ID, '_bpfm_banned_until', true );
		if( ! empty( $banned_until ) ) {
			?>
			<h3><?php _e( 'Unban User', BPFM_TEXT_DOMAIN ); ?></h3>
			<p class="bpfm-user-unban-success"></p>
			<table class="form-table" id="bpfm-unban-member">
				<tr>
					<th><label for=""><?php _e( 'Unban', BPFM_TEXT_DOMAIN ); ?></label></th>
					<td>
						<input type="button" id="bpfm-unban-member-profile" value="<?php _e( 'Click to unban', BPFM_TEXT_DOMAIN );?>" class="button button-secondary" data-uid="<?php echo $user->ID;?>" />
						<p class="description"><?php _e( 'Click on the button to lift the ban imposed on the user.', BPFM_TEXT_DOMAIN ); ?></p>
					</td>
				</tr>
			</table>
			<?php 
		}

	}

	/**
	 * AJAX called to unban a member
	 *
	 * @since    1.0.0
	 */
	public function bpfm_unban_member() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_unban_member' ) {
			global $bp_flag_members;
			$user_id = sanitize_text_field( $_POST['user_id'] );
			$user = get_userdata( $user_id );
			$admin_email = get_option( 'admin_email' );

			$flag_post_title = '#' . $user->data->user_login;
			$flag_post = get_page_by_title( $flag_post_title, OBJECT, $bp_flag_members->cpt_slug );

			if( ! empty( $flag_post ) ) {
				wp_delete_post( $flag_post->ID, true );
			}

			delete_user_meta( $user_id, '_bpfm_banned_on' );
			delete_user_meta( $user_id, '_bpfm_ban_duration' );
			delete_user_meta( $user_id, '_bpfm_banned_until' );

			$to = $user->data->user_email;
			$subject = get_bloginfo( 'title' ) . ' - ' . __( 'Account reinstated', BPFM_TEXT_DOMAIN );
			$message = '<p>Dear ' . $user->data->display_name . ',<br />';
			$message .= 'We are pleased to inform you that your account has been reinstated. You are now able to login as normal.<br /';
			$message .= 'If you believe this message has been sent in error, or if you have any questions concerning this notification, please email us at <a href="' . $admin_email . '">' . $admin_email . '</a>.</p>';
			wp_mail( $to, $subject, $message );

			$result = array(
				'message'		=>	'bpfm-member-unbanned',
				'message_txt'	=>	__( 'The member has been unbanned !!', BPFM_TEXT_DOMAIN ),
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * AJAX called to fetch flag details
	 *
	 * @since    1.0.0
	 */
	public function bpfm_get_flag_details() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_get_flag_details' ) {
			global $bp_flag_members;
			$flag_id = sanitize_text_field( $_POST['flag_id'] );
			$flag = get_post( $flag_id );

			$flagged_by = get_post_meta( $flag_id, '_bpfm_flagged_by', true );
			$flagged_reasons = get_post_meta( $flag_id, '_bpfm_reasons', true );
			$flagged_dates = get_post_meta( $flag_id, '_bpfm_flagged_date', true );

			$flag_status = get_post_meta( $flag_id, '_bpfm_flag_status', true );
			
			$html = '';
			$html .= '<table class="bpfm-flag-details">';
			$html .= '<thead>';
			$html .= '<tr>';
			$html .= '<th>' . __( 'Flagged By', BPFM_TEXT_DOMAIN ) . '</th>';
			$html .= '<th>' . __( 'Reason', BPFM_TEXT_DOMAIN ) . '</th>';
			$html .= '<th>' . __( 'Date', BPFM_TEXT_DOMAIN ) . '</th>';
			$html .= '</tr>';
			$html .= '<tbody>';
			foreach( $flagged_dates as $key => $dt ) {
				$reason = $flagged_reasons[ $key ];
				$reason_html = $reason['reason'];
				if( ! empty( $reason['description'] ) ) {
					$reason_html .= '<br><small>' . $reason['description'] . '</small>';
				}

				$flagged_by_name = ! empty( $flagged_by[$key]['name'] ) ? $flagged_by[$key]['name'] : '';
				$flagged_by_email = ! empty( $flagged_by[$key]['email'] ) ? $flagged_by[$key]['email'] : '';
				if( $flagged_by_email != '' ) {
					$flagged_by_name .= '<br /><small><a href="mailto:' . $flagged_by_email . '">' . $flagged_by_email . '</a></small>';
				}

				$flagged_by_user = '';
				$html .= '<tr>';
				$html .= '<td>' . $flagged_by_name . '</td>';
				$html .= '<td>' . $reason_html . '</td>';
				$html .= '<td>' . date( 'F j, Y, g:i A', strtotime( $dt ) ) . '</td>';
				$html .= '</tr>';
			}
			
			$html .= '</tbody>';
			$html .= '<tfoot>';
			$html .= '<tr colspan="3">';
			$html .= '<td class="bpfm-flag-details-tbl-footer-td">';

			if( $flag_status == 'pending' ) {
				$html .= '<input type="button" class="button button-primary bpfm-discard-flag" data-fid="' . $flag_id . '" value="' . __( 'Discard', BPFM_TEXT_DOMAIN ) . '">';
				$html .= '<input type="button" class="button button-primary bpfm-approve-flag" data-fid="' . $flag_id . '" data-ftitle="' . $flag->post_title . '" value="' . __( 'Approve', BPFM_TEXT_DOMAIN ) . '">';
			} elseif( $flag_status == 'discarded' ) {
				$html .= __( 'This flag has been discarded.', BPFM_TEXT_DOMAIN );
			} else {
				$html .= __( 'This flag has been approved.', BPFM_TEXT_DOMAIN );
			}
			$html .= '</td>';
			$html .= '<tfoot>';
			$html .= '</table>';

			$result = array(
				'message'	=>	'bpfm-flag-details-fetched',
				'html'		=>	$html,
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

}