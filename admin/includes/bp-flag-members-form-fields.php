<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit, if accessed directly

global $bp_flag_members;
$form_fields = array();
if( isset( $bp_flag_members->plugin_admin_settings['form_fields'] ) && ! empty( $bp_flag_members->plugin_admin_settings['form_fields'] ) ) {
	$form_fields = $bp_flag_members->plugin_admin_settings['form_fields'];
}
?>
<div class="bpfm-form-fields">
	<!-- SUCCESS MESSAGE -->
	<div class="updated notice bpfm-save-form-fields-success hide"><p><strong><?php _e( 'Settings saved.', BPFM_TEXT_DOMAIN );?></strong></p></div>

	<!-- FORM FIELD: NAME -->
	<div class="bpfm-form-field-name">
		<h3><?php _e( 'Field: Name', BPFM_TEXT_DOMAIN );?></h3>
		<select class="bpfm-select">
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['name_field'] == 'optional' ) ? 'selected' : '';?> value="optional"><?php _e( 'Optional - User does not need to enter a name', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['name_field'] == 'required' ) ? 'selected' : '';?> value="required"><?php _e( 'Required - User must enter a name to submit the form', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['name_field'] == 'hidden' ) ? 'selected' : '';?> value="hidden"><?php _e( 'Hidden - Do not display the name field', BPFM_TEXT_DOMAIN );?></option>
		</select>
	</div>

	<!-- FORM FIELD: EMAIL -->
	<div class="bpfm-form-field-email">
		<h3><?php _e( 'Field: Email', BPFM_TEXT_DOMAIN );?></h3>
		<select class="bpfm-select">
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['email_field'] == 'optional' ) ? 'selected' : '';?> value="optional"><?php _e( 'Optional - User does not need to enter an email', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['email_field'] == 'required' ) ? 'selected' : '';?> value="required"><?php _e( 'Required - User must enter an email to submit the form', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['email_field'] == 'hidden' ) ? 'selected' : '';?> value="hidden"><?php _e( 'Hidden - Do not display the email field', BPFM_TEXT_DOMAIN );?></option>
		</select>
	</div>

	<!-- REASONS -->
	<div class="bpfm-form-field-reasons">
		<h3><?php _e( 'Field: Reasons', BPFM_TEXT_DOMAIN );?></h3>
		<select class="bpfm-select">
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['reason_field'] == 'optional' ) ? 'selected' : '';?> value="optional"><?php _e( 'Optional - User does not need to select a reason', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['reason_field'] == 'required' ) ? 'selected' : '';?> value="required"><?php _e( 'Required - User must select a reason to submit the form', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['reason_field'] == 'hidden' ) ? 'selected' : '';?> value="hidden"><?php _e( 'Hidden - Do not display the reason field', BPFM_TEXT_DOMAIN );?></option>
		</select>
		<hr />
		<div class="bpfm-add-reason">
			<input type="button" id="bpfm-add-reason-btn" value="<?php _e( 'Add Reason', BPFM_TEXT_DOMAIN );?>" class="button button-secondary">
		</div>

		<div class="bpfm-reasons-list">
			<?php if( isset( $form_fields['reasons'] ) && ! empty( $form_fields['reasons'] ) ) :?>
				<?php foreach( $form_fields['reasons'] as $reason ) :?>
					<div class="bpfm-reason-single">
						<input type="text" class="regular-text bpfm-reason-input" placeholder="<?php _e( 'Add Reason', BPFM_TEXT_DOMAIN );?>" value="<?php echo $reason;?>">
						<input type="button" id="bpfm-delete-reason-btn" value="<?php _e( 'Remove', BPFM_TEXT_DOMAIN );?>" class="button button-secondary bpfm-remove-reason">
					</div>
				<?php endforeach;?>
			<?php endif;?>
		</div>
	</div>

	<!-- FORM FIELD: DESCRIPTION -->
	<div class="bpfm-form-field-description">
		<h3><?php _e( 'Field: Description', BPFM_TEXT_DOMAIN );?></h3>
		<select class="bpfm-select">
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['description_field'] == 'optional' ) ? 'selected' : '';?> value="optional"><?php _e( 'Optional - User does not need to enter a description', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['description_field'] == 'required' ) ? 'selected' : '';?> value="required"><?php _e( 'Required - User must enter a description to submit the form', BPFM_TEXT_DOMAIN );?></option>
			<option <?php echo ( ! empty( $form_fields ) && $form_fields['description_field'] == 'hidden' ) ? 'selected' : '';?> value="hidden"><?php _e( 'Hidden - Do not display the description field', BPFM_TEXT_DOMAIN );?></option>
		</select>
	</div>

	<!-- SAVE SETTINGS -->
	<div class="bpfm-save-form-fields">
		<input type="button" id="bpfm-save-form-fields-btn" value="<?php _e( 'Save Settings', BPFM_TEXT_DOMAIN );?>" class="button button-primary">
	</div>

	<!-- LOADER -->
	<div class="bpfm-form-fields-waiting-loader"><img src="<?php echo includes_url( 'images/spinner-2x.gif' );?>" /></div>
</div>