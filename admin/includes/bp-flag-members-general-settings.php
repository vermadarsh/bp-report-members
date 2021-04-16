<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit, if accessed directly

global $bp_flag_members;
$general_settings = array();
if( isset( $bp_flag_members->plugin_admin_settings['general_settings'] ) && ! empty( $bp_flag_members->plugin_admin_settings['general_settings'] ) ) {
	$general_settings = $bp_flag_members->plugin_admin_settings['general_settings'];
}
?>
<div class="bpfm-general-settings">
	<!-- SUCCESS MESSAGE -->
	<div class="updated notice bpfm-save-general-settings-success hide"><p><strong><?php _e( 'Settings saved.', BPFM_TEXT_DOMAIN );?></strong></p></div>

	<table class="form-table">
		<tbody>
			<!-- SHOW ON MEMBERS LISTING PAGE -->
			<tr>
				<th scope="row"><label for="bpfm-hide-on-member-directory"><?php _e( 'Hide on Member Directory', BPFM_TEXT_DOMAIN ); ?></label></th>
				<td>
					<label class="switch">
						<input type="checkbox" id="bpfm-hide-on-member-directory" <?php echo ( isset( $general_settings['hide_on_member_directory'] ) && $general_settings['hide_on_member_directory'] == 'yes' ) ? 'checked' : '';?>>
						<span class="slider round"></span>
					</label>
					<p class="description"><?php _e( 'Keep this switch on to hide the reporting button on member\'s directory.', BPFM_TEXT_DOMAIN ); ?></p>
				</td>
			</tr>

			<!-- REPORT BUTTON TEXT -->
			<tr>
				<th scope="row"><label for="bpfm-report-button-text"><?php _e( 'Report Button Text', BPFM_TEXT_DOMAIN ); ?></label></th>
				<td>
					<input type="text" id="bpfm-report-button-text" class="regular-text" placeholder="<?php _e( 'Report Button Text', BPFM_TEXT_DOMAIN );?>" value="<?php echo ( isset( $general_settings['report_button_text'] ) && ! empty( $general_settings['report_button_text'] ) ) ? $general_settings['report_button_text'] : '';?>">
					<p class="description"><?php _e( 'Button text on the report button. Default Value: <strong>Report Member</strong>', BPFM_TEXT_DOMAIN ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" id="bpfm-save-general-settings" class="button button-primary" value="<?php _e( 'Save Changes', BPFM_TEXT_DOMAIN ); ?>">
	</p>

	<!-- LOADER -->
	<div class="bpfm-general-settings-waiting-loader"><img src="<?php echo includes_url( 'images/spinner-2x.gif' );?>" /></div>
</div>