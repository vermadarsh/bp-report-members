<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Bp_Flag_Members
 * @subpackage Bp_Flag_Members/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bp_Flag_Members
 * @subpackage Bp_Flag_Members/public
 * @author     Multidots <info@multidots.com>
 */
class Bp_Flag_Members_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, BPFM_PLUGIN_URL . 'public/css/bp-flag-members-public.css' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, BPFM_PLUGIN_URL . 'public/js/bp-flag-members-public.js', array( 'jquery' ) );
		wp_localize_script(
			$this->plugin_name,
			'BPFM_Public_JS_Obj',
			array(
				'ajaxurl'						=>	admin_url( 'admin-ajax.php' ),
				'loader_url'					=>	includes_url( 'images/spinner-2x.gif' )
			)
		);

	}

	/**
	 * Report members on the member's directory
	 *
	 * @since    1.0.0
	 */
	public function bpfm_member_reporting_on_directory( $user_id ) {

		if( is_user_logged_in() ) {
			global $bp_flag_members;
			
			$curr_uid = get_current_user_id();
			$hide_on_members_directory = 'no';
			if( isset( $bp_flag_members->plugin_admin_settings['general_settings'] ) && ! empty( $bp_flag_members->plugin_admin_settings['general_settings'] ) ) {
				$hide_on_members_directory = $bp_flag_members->plugin_admin_settings['general_settings']['hide_on_member_directory'];
			}

			if( $hide_on_members_directory == 'no' && $curr_uid != $user_id ) {
				$btn_txt = ! empty( $bp_flag_members->plugin_admin_settings['general_settings']['report_button_text'] ) ? $bp_flag_members->plugin_admin_settings['general_settings']['report_button_text'] : __( 'Report Member', BPFM_TEXT_DOMAIN );
				$member = get_userdata( $user_id );

				$member_fn = xprofile_get_field_data( 1, $user_id );
				$member_ln = xprofile_get_field_data( 19, $user_id );
				
				if( $member_fn != '' && $member_ln != '' ) {
					$member_nm = $member_fn . ' ' . $member_ln;
				} elseif( $member_fn != '' && $member_ln == '' ) {
					$member_nm = $member_fn;
				} else {
					$member_nm = $member->data->user_login;
				}

				echo '<a href="javascript:void(0);" class="bpfm-report-member-modal view-profile-btn" data-uid="' . $user_id . '" data-uname="' . $member_nm . '">' . $btn_txt . '</a>';
			}
		}

	}

	/**
	 * Report members on the member's profile
	 *
	 * @since    1.0.0
	 */
	public function bpfm_member_reporting_on_profile() {

		if( is_user_logged_in() && bp_loggedin_user_id() != bp_displayed_user_id() ) {
			global $bp_flag_members;
			
			$curr_uid = get_current_user_id();
			$curr_member_id = bp_displayed_user_id();
			
			$btn_txt = ! empty( $bp_flag_members->plugin_admin_settings['general_settings']['report_button_text'] ) ? $bp_flag_members->plugin_admin_settings['general_settings']['report_button_text'] : __( 'Report Member', BPFM_TEXT_DOMAIN );
			$member = get_userdata( $curr_member_id );

			$member_fn = xprofile_get_field_data( 1, $curr_member_id );
			$member_ln = xprofile_get_field_data( 19, $curr_member_id );

			if( $member_fn != '' && $member_ln != '' ) {
				$member_nm = $member_fn . ' ' . $member_ln;
			} elseif( $member_fn != '' && $member_ln == '' ) {
				$member_nm = $member_fn;
			} else {
				$member_nm = $member->data->user_login;
			}

			$member_name = $member->data->display_name;
			echo '<div class="generic-button"><a href="javascript:void(0);" class="bpfm-report-member-modal" data-uid="' . $curr_member_id . '" data-uname="' . $member_nm . '">' . $btn_txt . '</a></div>';
			
		}

	}

	/**
	 * Modals used to report members
	 *
	 * @since    1.0.0
	 */
	public function bpfm_custom_modals() {

		if( is_user_logged_in() && stripos( $_SERVER['REQUEST_URI'], 'directory' ) !== false ) {
			$report_user_modal = BPFM_PLUGIN_PATH . 'public/templates/modals/bpfm-report-member.php';
			if( file_exists( $report_user_modal ) ) {
				include $report_user_modal;
			}
		}

	}

	/**
	 * AJAX called to flag a member
	 *
	 * @since    1.0.0
	 */
	public function bpfm_flag_member() {

		if( isset( $_POST['action'] ) && $_POST['action'] === 'bpfm_flag_member' ) {
			
			global $bp_flag_members;
			$name = sanitize_text_field( $_POST[ 'name' ] );
			$email = sanitize_text_field( $_POST[ 'email' ] );
			$reason = sanitize_text_field( $_POST[ 'reason' ] );
			$description = sanitize_text_field( $_POST[ 'description' ] );
			$flag_uid = sanitize_text_field( $_POST[ 'flag_uid' ] );
			$date_flagged = date( 'Y-m-d H:i:s' );
			$flagged_member_details = get_userdata( $flag_uid );
			$user_login = $flagged_member_details->data->user_login;
			$flag_post_title = '#' . $user_login;
			
			$flag_post = get_page_by_title( $flag_post_title, OBJECT, $bp_flag_members->cpt_slug );
			
			if( empty( $flag_post ) ) {
				// New arrival
				$args = array(
					'post_type'		=>	$bp_flag_members->cpt_slug,
					'post_status'	=>	'publish',
					'post_title'	=>	$flag_post_title,
					'post_parent'	=>	$flag_uid
				);
				$post_id = wp_insert_post( $args );
				update_post_meta( $post_id, '_bpfm_flag_status', 'pending' );

				/**
				 * Flagged by current logged in user
				 */
				$flagged_by = get_post_meta( $post_id, '_bpfm_flagged_by', true );
				if( empty( $flagged_by ) ) {
					$flagged_by = array();
				}
				$flagged_by[] = array(
					'user_id'	=>	get_current_user_id(),
					'name'		=>	$name,
					'email'		=>	$email
				);
				update_post_meta( $post_id, '_bpfm_flagged_by', $flagged_by );

				/**
				 * Flagged for reasons
				 */
				$flagged_reasons = get_post_meta( $post_id, '_bpfm_reason', true );
				if( empty( $flagged_reasons ) ) {
					$flagged_reasons = array();
				}
				$flagged_reasons[] = array(
					'reason'		=>	$reason,
					'description'	=>	$description
				);
				update_post_meta( $post_id, '_bpfm_reasons', $flagged_reasons );

				/**
				 * Flagged on date
				 */
				$flagged_date = get_post_meta( $post_id, '_bpfm_flagged_date', true );
				if( empty( $flagged_date ) ) {
					$flagged_date = array();
				}
				$flagged_date[] = $date_flagged;
				update_post_meta( $post_id, '_bpfm_flagged_date', $flagged_date );
			} else {
				// Already flagged
				$post_id = $flag_post->ID;

				/**
				 * Flagged by current logged in user
				 */
				$flagged_by = get_post_meta( $post_id, '_bpfm_flagged_by', true );
				$flagged_by[] = array(
					'user_id'	=>	get_current_user_id(),
					'name'		=>	$name,
					'email'		=>	$email
				);
				update_post_meta( $post_id, '_bpfm_flagged_by', $flagged_by );

				/**
				 * Flagged for reasons
				 */
				$flagged_reasons = get_post_meta( $post_id, '_bpfm_reasons', true );
				$flagged_reasons[] = array(
					'reason'		=>	$reason,
					'description'	=>	$description
				);
				update_post_meta( $post_id, '_bpfm_reasons', $flagged_reasons );

				/**
				 * Flagged on date
				 */
				$flagged_date = get_post_meta( $post_id, '_bpfm_flagged_date', true );
				$flagged_date[] = $date_flagged;
				update_post_meta( $post_id, '_bpfm_flagged_date', $flagged_date );

				update_post_meta( $post_id, '_bpfm_flag_status', 'pending' );

				/**
				 * Update the modified date
				 */
				wp_update_post(
					array(
						'ID'			=>	$post_id,
						'post_modified'	=>	$flagged_date
					)
				);
			}

			$result = array(
				'message'	=>	'bpfm-member-flagged',
				'id'		=>	$post_id
			);
			wp_send_json_success( $result );
			wp_die();
		}

	}

	/**
	 * Authenticate the user from logging in
	 *
	 * @since    1.0.0
	 */
	public function bpfm_authenticate_user_login( $user, $password ) {

		if( ! empty( $user ) ) {
			// Check if user is banned
			$banned_until = get_user_meta( $user->ID, '_bpfm_banned_until', true );
			if( ! empty( $banned_until ) ) {
				$today = date( 'Y-m-d H:i:s' );
				$date1 = strtotime( $banned_until );
				$date2 = strtotime( $today );
				$diff_mins = round( abs( $date1 - $date2 ) / 60, 2 );
				if( $diff_mins >= 0 ) {
					$ban_error = new WP_Error( 'user_banned', sprintf( __( 'Your account has been suspended. Please contact %1$s for further assistance.', BPFM_TEXT_DOMAIN), '<a href="mailto: ' . get_option( 'admin_email' ) . '">administrator</a>' ) );
					return $ban_error;
				}
			} else {
				return $user;
			}
		}

	}

	/**
	 * Delete the flag of the user, on user deletion
	 *
	 * @since    1.0.0
	 */
	public function bpfm_delete_flags_on_user_delete( $user_id ) {

		global $bp_flag_members;
		$user = get_userdata( $user_id );
		if( ! empty( $user ) ) {
			$flag_post_title = '#' . $user->data->user_login;
			$flag = get_page_by_title( $flag_post_title, OBJECT, $bp_flag_members->cpt_slug );
			if( ! empty( $flag ) ) {
				wp_delete_post( $flag->ID, true );
			}
		}

	}

	/*
	 * function to check user is banned or not
	 * */
	public static function bpfm_is_user_banned($user_id = 0){
		if(0 == $user_id){
			return false;
		}
		$date_banned = get_user_meta($user_id,'_bpfm_banned_on',true);

		if($date_banned){
			return true;
		}else{
			return false;
		}

	}

}