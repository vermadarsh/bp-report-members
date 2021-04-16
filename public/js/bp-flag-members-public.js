jQuery(document).ready(function( $ ) {
	'use strict';

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
	 * Open the report member modal from member directory
	 */
	$(document).on('click', '.bpfm-report-member-modal', function(){
		console.log('in');
		var uid = $(this).data('uid');
		var uname = $(this).data('uname');
		console.log(uid);
		console.log(uname);
		$('#bpfm-report-member-modal .modal-header h2').html( 'Flag: ' + uname );
		$('#bpfm-report-member-modal').css('display', 'block');
		$('#bpfm-rm-submit').data('uid', uid);
	});

	/**
	 * Submit the flagging member modal
	 */
	$(document).on('click', '#bpfm-rm-submit', function(){
		var uid = $(this).data('uid');
		var name = '', email = '', reason = '', description = '', process_flagging = false, error_html = '';
		var name_ok = true, email_ok = true, reason_ok = true, description_ok = true;
		error_html += '<ul>';

		/**
		 * Process name field
		 */
		if( $('.bpfm-form-field-name').length == 0 ) {
			name = 'Anonymous';
			name_ok = true;
		} else {
			name = $('#bpfm-rm-name').val();
			var name_field_type = $('#bpfm-rm-name').data('ftype');
			if( name_field_type == 'required' ) {
				if( name == '' ) {
					name_ok = false;
					error_html += '<li>Name is required !!</li>';
				} else {
					var hasNumber = /\d/;
					var hasSpecialCharacters = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
					if( hasNumber.test( name ) != true && hasSpecialCharacters.test( name ) != true ) {
						name_ok = true;
					} else {
						name_ok = false;
						error_html += '<li>Name is invalid. Contains numbers or special characters !!</li>';
					}
				}
			} else {
				if( name == '' ) {
					name = 'Anonymous';
				} else {
					var hasNumber = /\d/;
					var hasSpecialCharacters = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
					if( hasNumber.test( name ) != true && hasSpecialCharacters.test( name ) != true ) {
						name_ok = true;
					} else {
						name_ok = false;
						error_html += '<li>Name is invalid. Contains numbers or special characters !!</li>';
					}
				}
			}
		}

		/**
		 * Process email field
		 */
		if( $('.bpfm-form-field-email').length == 0 ) {
			email_ok = true;
		} else {
			email = $('#bpfm-rm-email').val();
			var email_field_type = $('#bpfm-rm-email').data('ftype');
			if( email_field_type == 'required' ) {
				if( email == '' ) {
					email_ok = false;
					error_html += '<li>Email is required !!</li>';
				} else {
					var filter_email = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
					if ( filter_email.test( email ) ) {
						email_ok = true;
					} else {
						email_ok = false;
						error_html += '<li>Email is invalid !!</li>';
					}


				}
			} else {
				email_ok = true;
			}
		}

		/**
		 * Process reason field
		 */
		if( $('.bpfm-form-field-reason').length == 0 ) {
			reason_ok = true;
		} else {
			reason = $('#bpfm-rm-reason').val();
			var reason_field_type = $('#bpfm-rm-reason').data('ftype');
			if( reason_field_type == 'required' ) {
				if( reason == '' ) {
					reason_ok = false;
					error_html += '<li>Reason is required !!</li>';
				}
			} else {
				reason_ok = true;
			}
		}

		/**
		 * Process description field
		 */
		if( $('.bpfm-form-field-description').length == 0 ) {
			description_ok = true;
		} else {
			description = $('#bpfm-rm-description').val();
			var description_field_type = $('#bpfm-rm-description').data('ftype');
			if( description_field_type == 'required' ) {
				if( description == '' ) {
					description_ok = false;
					error_html += '<li>Description is required !!</li>';
				}
			} else {
				description_ok = true;
			}
		}

		error_html += '</ul>';

		var data = {
			'action'		: 'bpfm_flag_member',
			'name'			: name,
			'email'			: email,
			'reason'		: reason,
			'description'	: description,
			'flag_uid'		: uid
		}

		if( name_ok == true && email_ok == true && reason_ok == true && description_ok == true ) {
			process_flagging = true;
		}

		if( process_flagging == true ) {
			$('.bpfm-flagging-error').hide();
			$.ajax({
				dataType 	: "JSON",
				url 		: BPFM_Public_JS_Obj.ajaxurl,
				type 		: 'POST',
				data 		: data,
				success: function( response ) {
					if( response['data']['message'] == 'bpfm-member-flagged' ) {
						
						$('.bpfm-flagging-success').fadeIn(3000).fadeOut();						
						$('#bpfm-rm-description').val('');
						$('#bpfm-rm-reason option:selected').removeAttr('selected');
						setTimeout( function() {
							$('.modal').fadeOut();
						}, 4000);
					}
				},
			});
		} else {
			$('.bpfm-flagging-error').html( error_html ).show();
		}
	});

});