<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bp_flag_members;

$name_field_type = ! empty( $bp_flag_members->plugin_admin_settings['form_fields']['name_field'] ) ? $bp_flag_members->plugin_admin_settings['form_fields']['name_field'] : 'optional';
$email_field_type = ! empty( $bp_flag_members->plugin_admin_settings['form_fields']['email_field'] ) ? $bp_flag_members->plugin_admin_settings['form_fields']['email_field'] : 'optional';
$reason_field_type = ! empty( $bp_flag_members->plugin_admin_settings['form_fields']['reason_field'] ) ? $bp_flag_members->plugin_admin_settings['form_fields']['reason_field'] : 'optional';
$description_field_type = ! empty( $bp_flag_members->plugin_admin_settings['form_fields']['description_field'] ) ? $bp_flag_members->plugin_admin_settings['form_fields']['description_field'] : 'optional';
$reasons = ! empty( $bp_flag_members->plugin_admin_settings['form_fields']['reasons'] ) ? $bp_flag_members->plugin_admin_settings['form_fields']['reasons'] : array();
$btn_txt = ! empty( $bp_flag_members->plugin_admin_settings['general_settings']['report_button_text'] ) ? $bp_flag_members->plugin_admin_settings['general_settings']['report_button_text'] : __( 'Report Member', BPFM_TEXT_DOMAIN );

$name = '';
$email = '';
if( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$member_fn = xprofile_get_field_data( 1, $user_id );
	$member_ln = xprofile_get_field_data( 19, $user_id );
	$member = get_userdata( $user_id );
	
	if( $member_fn != '' && $member_ln != '' ) {
		$name = $member_fn . ' ' . $member_ln;
	} elseif( $member_fn != '' && $member_ln == '' ) {
		$name = $member_fn;
	} else {
		$name = $member->data->user_login;
	}

	$email = $member->data->user_email;
}
?>
<div id="bpfm-report-member-modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="close">&times;</span>
			<h2><?php _e( 'Flag: ', BPFM_TEXT_DOMAIN );?></h2>
		</div>
		<div class="modal-body bpfm-report-member-modal-content">
			<div class="bpfm-report-member-form">

				<div class="bpfm-flagging-error"></div>
				<p class="bpfm-flagging-success"><?php _e( 'Member Flagged Successfully !!', BPFM_TEXT_DOMAIN );?></p>

				<!-- NAME FIELD -->
				<?php if( $name_field_type != 'hidden' ) :?>
					<div class="bpfm-form-field-name">
						<input type="text" id="bpfm-rm-name" placeholder="<?php _e( 'Your name...', BPFM_TEXT_DOMAIN );?>" data-ftype="<?php echo $name_field_type;?>" value="<?php echo $name;?>">
					</div>
				<?php endif;?>

				<!-- EMAIL FIELD -->
				<?php if( $email_field_type != 'hidden' ) :?>
					<div class="bpfm-form-field-email">
						<input type="text" id="bpfm-rm-email" placeholder="<?php _e( 'Your email...', BPFM_TEXT_DOMAIN );?>" data-ftype="<?php echo $email_field_type;?>" value="<?php echo $email;?>">
					</div>
				<?php endif;?>

				<!-- REASON FIELD -->
				<?php if( $reason_field_type != 'hidden' && ! empty( $reasons ) ) :?>
					<div class="bpfm-form-field-reason">
						<select id="bpfm-rm-reason" data-ftype="<?php echo $reason_field_type;?>">
							<option value=""><?php _e( '--Select A Reason--', BPFM_TEXT_DOMAIN );?></option>
							<?php foreach( $reasons as $reason ) :?>
								<option value="<?php echo $reason;?>"><?php echo $reason;?></option>
							<?php endforeach;?>
						</select>
					</div>
				<?php endif;?>

				<!-- DESCRIPTION FIELD -->
				<?php if( $description_field_type != 'hidden' ) :?>
					<div class="bpfm-form-field-description">
						<textarea id="bpfm-rm-description" placeholder="<?php _e( 'Why flag this member...', BPFM_TEXT_DOMAIN );?>" data-ftype="<?php echo $description_field_type;?>"></textarea>
					</div>
				<?php endif;?>

				<div class="bpfm-form-field-submit">
					<input type="button" id="bpfm-rm-submit" data-uid="" value="<?php echo $btn_txt;?>">
				</div>

			</div>
		</div>
	</div>
</div>