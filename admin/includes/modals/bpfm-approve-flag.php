<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bp_flag_members;
?>
<div id="bpfm-approve-flag-modal" class="modal">
	<div class="modal-content">
		<div class="modal-header">
			<span class="close">&times;</span>
			<h2></h2>
		</div>
		<div class="modal-body bpfm-approve-flag-modal-content">
			<p class="bpfm-approve-flag-success"></p>
			<p><?php _e( 'Approving flag means, either the member would be banned or would be warned !!', BPFM_TEXT_DOMAIN );?></p>
			<h3><?php _e( 'Ban Member', BPFM_TEXT_DOMAIN );?></h3>
			<div class="bpfm-ban-member-content">
				<!-- BAN DURATION -->
				<div class="bpfm-ban-duration">
					<h4><?php _e( 'Ban Duration:', BPFM_TEXT_DOMAIN );?></h4>
					<select class="bpfm-ban-user-duration">
						<option value="1"><?php _e( 'Ban for a day.', BPFM_TEXT_DOMAIN );?></option>
						<option value="2"><?php _e( 'Ban for 2 days.', BPFM_TEXT_DOMAIN );?></option>
						<option value="7"><?php _e( 'Ban for a week.', BPFM_TEXT_DOMAIN );?></option>
						<option value="14"><?php _e( 'Ban for a fortnight.', BPFM_TEXT_DOMAIN );?></option>
						<option value="30"><?php _e( 'Ban for a month.', BPFM_TEXT_DOMAIN );?></option>
						<option value="183"><?php _e( 'Ban for 6 months.', BPFM_TEXT_DOMAIN );?></option>
						<option value="365"><?php _e( 'Ban for a year.', BPFM_TEXT_DOMAIN );?></option>
						<!--<option value="99999"><?php _e( 'Ban forever.', BPFM_TEXT_DOMAIN );?></option>-->
					</select>
				</div>
				<!-- BAN MESSAGE -->
				<div class="bpfm-ban-message">
					<h4><?php _e( 'Ban Custom Message:', BPFM_TEXT_DOMAIN );?></h4>
					<textarea class="bpfm-ban-user-message" placeholder="<?php _e( 'Ban Message...', BPFM_TEXT_DOMAIN );?>" rows="4"></textarea>
				</div>
				<!-- BAN SUBMIT -->
				<div class="bpfm-ban-submit">
					<input type="button" class="button button-secondary bpfm-ban-member" data-fid="" value="<?php _e( 'Ban Member', BPFM_TEXT_DOMAIN )?>">
					<input type="button" class="button button-secondary bpfm-warn-member" data-fid="" value="<?php _e( 'Warn Otherwise', BPFM_TEXT_DOMAIN )?>">
				</div>
			</div>
			<div class="bpfm-modal-waiting-loader"><img src="<?php echo includes_url( 'images/spinner-2x.gif' );?>" /></div>
		</div>
	</div>
</div>