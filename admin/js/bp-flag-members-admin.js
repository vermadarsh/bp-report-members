jQuery(document).ready(function( $ ) {
	'use strict';

	$('.bpfm-select').select2({
		placeholder: "Select setting",
	});

	/**
	 * Close the modal
	 */
	$(document).on('click', '.close', function(){
		$('.modal').css('display', 'none');
	});

	/**
	 * Close the modal, when clicked anywhere other than the targer modal
	 */
	var modals = document.getElementsByClassName( 'modal' );
	$(window).click(function( event ){
		if ( event.target == modals ) {
			modals.style.display = "none";
		}
	});

	/**
	 * Close all modals when esc key is pressed.
	 */
	$(document).keyup(function(e) {
		if ( e.keyCode == 27 ) {
			$('.modal').css('display', 'none');
		}
	});

	/**
	 * Add html to add reason
	 */
	$(document).on('click', '#bpfm-add-reason-btn', function() {
		var html = '<div class="bpfm-reason-single">';
		html += '<input type="text" class="regular-text bpfm-reason-input" placeholder="' + BPFM_Admin_JS_Obj.reason_input_placeholder + '">';
		html += '<input type="button" id="bpfm-delete-reason-btn" value="' + BPFM_Admin_JS_Obj.remove_reason_btn_value + '" class="button button-secondary bpfm-remove-reason">';
		html += '</div>';
		$('.bpfm-reasons-list').append( html );
		$('.bpfm-reasons-save').show();
	});

	/**
	 * Save reasons
	 */
	$(document).on('click', '#bpfm-save-form-fields-btn', function() {
		var reasons = [];
		var form_fields = [];
		$('.bpfm-reasons-list .bpfm-reason-single input[type="text"]').each(function(){
			if( $(this).val() != '' ) {
				reasons.push( $(this).val() );
			}
		});
		var name_field = $('.bpfm-form-field-name select').val();
		var email_field = $('.bpfm-form-field-email select').val();
		var reason_field = $('.bpfm-form-field-reasons select').val();
		var description_field = $('.bpfm-form-field-description select').val();

		form_fields = {
			'name_field'		: name_field,
			'email_field'		: email_field,
			'reason_field'		: reason_field,
			'reasons'			: reasons,
			'description_field'	: description_field
		}
		$('.bpfm-save-form-fields-success').hide();
		var data = {
			'action'		: 'bpfm_save_form_fields',
			'form_fields'	: form_fields,
		}
		$.ajax({
			dataType 	: "JSON",
			url 		: BPFM_Admin_JS_Obj.ajaxurl,
			type 		: 'POST',
			data 		: data,
			success: function( response ) {
				if( response['data']['message'] == 'bpfm-form-fields-saved' ) {
					$('.bpfm-save-form-fields-success').show();
					if( reasons.length > 0 ) {
						var html = '';
						for( var i in reasons ) {
							html += '<div class="bpfm-reason-single">';
							html += '<input type="text" class="regular-text bpfm-reason-input" placeholder="' + BPFM_Admin_JS_Obj.reason_input_placeholder + '" value="' + reasons[i] + '">';
							html += '<input type="button" id="bpfm-delete-reason-btn" value="' + BPFM_Admin_JS_Obj.remove_reason_btn_value + '" class="button button-secondary bpfm-remove-reason">';
							html += '</div>';
						}
						$('.bpfm-reasons-list').html( html );
					}
				}
			},
		});
		
	});

	/**
	 * Remove reason
	 */
	$(document).on('click', '.bpfm-remove-reason', function() {
		var this_btn = $(this);
		setTimeout( function() {
			this_btn.parent('.bpfm-reason-single').remove();
		}, 1000);
	});

	/**
	 * Save general settings
	 */
	$(document).on('click', '#bpfm-save-general-settings', function() {
		// $('.bpfm-general-settings-waiting-loader').show();
		var hide_on_member_directory = ( $('#bpfm-hide-on-member-directory').prop("checked") == true ) ? 'yes' : 'no';
		var report_button_text = ( $('#bpfm-report-button-text').val() != '' ) ? $('#bpfm-report-button-text').val() : 'Report Member';
		var data = {
			'action'					: 'bpfm_save_general_settings',
			'hide_on_member_directory'	: hide_on_member_directory,
			'report_button_text'		: report_button_text
		}
		$.ajax({
			dataType 	: "JSON",
			url 		: BPFM_Admin_JS_Obj.ajaxurl,
			type 		: 'POST',
			data 		: data,
			success: function( response ) {
				if( response['data']['message'] == 'bpfm-general-settings-saved' ) {
					$('.bpfm-save-general-settings-success').show();
				}
			},
		});
	});

	/**
	 * Delete the flag
	 */
	$(document).on('click', '.bpfm-delete-flag', function() {
		var this_btn = $(this);
		var flag_id = $(this).data('fid');
		if( flag_id != '' ) {

			var del_cnf = confirm( BPFM_Admin_JS_Obj.flag_del_cnf_msg );

			if( del_cnf == true ) {
				this_btn.html( '<span><i class="fa fa-refresh fa-spin" aria-hidden="true"></i></span>' );
				var data = {
					'action'	: 'bpfm_delete_flag',
					'flag_id'	: flag_id,
				}
				$.ajax({
					dataType 	: "JSON",
					url 		: BPFM_Admin_JS_Obj.ajaxurl,
					type 		: 'POST',
					data 		: data,
					success: function( response ) {
						if( response['data']['message'] == 'bpfm-flag-deleted' ) {
							this_btn.closest('tr').remove();
							if( response['data']['remaining_posts_html'] != '' ) {
								$('.users_page_bp-flag-members tbody').html( response['data']['remaining_posts_html'] );
							}
						}
					},
				});
			}
		}
	});

	var discard_btn_clicked;
	var approve_btn_clicked;

	/**
	 * View Flag Details
	 */
	$(document).on('click', '.bpfm-view-flag', function() {
		var this_btn = $(this);

		discard_btn_clicked = this_btn;
		approve_btn_clicked = this_btn;
		
		var flag_id = $(this).data('fid');
		if( flag_id != '' ) {
			var flag_title = $(this).data('ftitle');
			var modal_html = '';
			$('#bpfm-view-flag-modal .modal-header h2').html( BPFM_Admin_JS_Obj.view_flag_txt + flag_title );
			$('.bpfm-view-flag-modal-content').html( '<img src=" ' + BPFM_Admin_JS_Obj.loader_url + ' " alt="Loader" /><p> ' + BPFM_Admin_JS_Obj.fetch_flag_wait_msg + '</p>' ).css('text-align', 'center');
			$('#bpfm-view-flag-modal').css('display', 'block');

			var data = {
				'action'	: 'bpfm_get_flag_details',
				'flag_id'	: flag_id
			}
			$.ajax({
				dataType 	: "JSON",
				url 		: BPFM_Admin_JS_Obj.ajaxurl,
				type 		: 'POST',
				data 		: data,
				success: function( response ) {
					if( response['data']['message'] == 'bpfm-flag-details-fetched' ) {
						$('.bpfm-view-flag-modal-content').html( response['data']['html'] ).css('text-align', 'justify');
					}
				},
			});
		}
	});

	/**
	 * Discard the flag
	 */
	$(document).on('click', '.bpfm-discard-flag', function() {
		var this_btn = $(this);
		var flag_id = $(this).data('fid');
		if( flag_id != '' ) {
			var data = {
				'action'	: 'bpfm_discard_flag',
				'flag_id'	: flag_id,
			}
			$.ajax({
				dataType 	: "JSON",
				url 		: BPFM_Admin_JS_Obj.ajaxurl,
				type 		: 'POST',
				data 		: data,
				success: function( response ) {
					if( response['data']['message'] == 'bpfm-flag-discarded' ) {
						$('#bpfm-view-flag-modal').hide();
						console.log( discard_btn_clicked.html() );
						discard_btn_clicked.closest('td').prev().html( response['data']['new_status'] );
					}
				},
			});
		}
	});

	/**
	 * Approve the flag
	 */
	$(document).on('click', '.bpfm-approve-flag', function() {
		var this_btn = $(this);
		var flag_id = $(this).data('fid');
		if( flag_id != '' ) {
			var flag_title = $(this).data('ftitle');
			$('.modal').hide();
			$('#bpfm-approve-flag-modal .modal-header h2').html( BPFM_Admin_JS_Obj.approve_flag_txt + flag_title );
			$('#bpfm-approve-flag-modal').css('display', 'block');
			$('.bpfm-ban-member, .bpfm-warn-member').data( 'fid', flag_id );
		}
	});

	/**
	 * Warn member
	 */
	$(document).on('click', '.bpfm-warn-member', function() {
		var this_btn = $(this);
		var flag_id = $(this).data('fid');
		
		if( flag_id != '' ) {
			$('.bpfm-modal-waiting-loader').show();
			var data = {
				'action'	: 'bpfm_warn_member',
				'flag_id'	: flag_id,
			}
			$.ajax({
				dataType 	: "JSON",
				url 		: BPFM_Admin_JS_Obj.ajaxurl,
				type 		: 'POST',
				data 		: data,
				success: function( response ) {
					if( response['data']['message'] == 'bpfm-flag-approved' ) {
						$('.bpfm-modal-waiting-loader').hide();
						$('.bpfm-approve-flag-success').text( response['data']['message_txt'] ).show();
						setTimeout( function() {
							$('.modal').css('display', 'none');
							approve_btn_clicked.closest('td').prev('td').html( response['data']['new_status'] + '<br /><small>(' + response['data']['status_action'] + ')</small>' );
							$('.bpfm-approve-flag-success').hide();
						}, 1000);
					}
				},
			});
		}
	});

	/**
	 * Ban member
	 */
	$(document).on('click', '.bpfm-ban-member', function() {
		var this_btn = $(this);
		var flag_id = $(this).data('fid');
		if( flag_id != '' ) {

			var ban_cnf = confirm( BPFM_Admin_JS_Obj.member_ban_cnf_msg );
			if( ban_cnf == true ) {
				var ban_duration = $('.bpfm-ban-user-duration').val();
				var ban_message = $('.bpfm-ban-user-message').val();
				$('.bpfm-modal-waiting-loader').show();
				var data = {
					'action'		: 'bpfm_ban_member',
					'flag_id'		: flag_id,
					'ban_duration'	: ban_duration,
					'ban_message'	: ban_message
				}
				$.ajax({
					dataType 	: "JSON",
					url 		: BPFM_Admin_JS_Obj.ajaxurl,
					type 		: 'POST',
					data 		: data,
					success: function( response ) {
						if( response['data']['message'] == 'bpfm-flag-approved' ) {
							$('.bpfm-modal-waiting-loader').hide();
							$('.bpfm-approve-flag-success').text( response['data']['message_txt'] ).show();
							setTimeout( function() {
								$('.modal').css('display', 'none');
								approve_btn_clicked.closest('td').prev('td').html( response['data']['new_status'] + '<br /><small>(' + response['data']['status_action'] + ')</small>' );
								approve_btn_clicked.closest('div.bpfm-flag-actions').append( response['data']['actions_html'] );
								$('.bpfm-approve-flag-success').hide();
							}, 1000);
						}
					},
				});
			}
		}
	});

	/**
	 * Redirect to member profile to unban the member
	 */
	$(document).on('click', '.bpfm-unban-member', function() {
		var url = $(this).data('url');
		if( url != '' ) {
			window.open( url, '_blank' );
		}
	});

	/**
	 * Unban member
	 */
	$(document).on('click', '#bpfm-unban-member-profile', function() {
		console.log('in');
		var user_id = $(this).data('uid');
		if( user_id != '' ) {
			var data = {
				'action'	: 'bpfm_unban_member',
				'user_id'	: user_id
			}
			$.ajax({
				dataType 	: "JSON",
				url 		: BPFM_Admin_JS_Obj.ajaxurl,
				type 		: 'POST',
				data 		: data,
				success: function( response ) {
					if( response['data']['message'] == 'bpfm-member-unbanned' ) {
						$('.bpfm-user-unban-success').text( response['data']['message_txt'] ).show();
					}
				},
			});
		}
	});

});