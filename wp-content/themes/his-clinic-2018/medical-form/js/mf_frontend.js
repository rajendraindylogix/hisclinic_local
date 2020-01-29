jQuery(document).ready(function ($) {

	/*Write check rule for Password Strength*/
	$.validator.addMethod("pwcheck", function (value) {
		var regex = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
		return regex.test(value);
	}, 'Please enter a valid password');

	$.validator.addMethod("emailcheck", function (value) {
		var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return regex.test(value);
	}, 'Please enter a valid email');

	$('#md_popup_signup').validate({
		rules: {
			form2_email: {
				emailcheck: true
			},
			form2_password: {
				pwcheck: true
			}
		},
		submitHandler: function (form) {
			/*If validation fails stop form submission*/
			return false;
		}
	});

	// jQuery.validator.addClassRules('mask-date', {
	// 	required: true,
	// 	// date: true,
	// 	dateFormat: true
	// });
	
	// jQuery.validator.addMethod(
	// 	"dateFormat",
	// 	function(value, element) {

	// 		return true;

	// 		var check = false;
	// 		var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
	// 		var re = '/^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;';
	// 			if( re.test(value)){
	// 				var adata = value.split('/');
	// 				var dd = parseInt(adata[0],10);
	// 				var mm = parseInt(adata[1],10);
	// 				var yyyy = parseInt(adata[2],10);
	// 				var xdata = new Date(yyyy,mm,dd-1);
	// 				if ( ( xdata.getFullYear() === yyyy ) && ( xdata.getMonth() === mm ) && ( xdata.getDate() === dd - 1 ) ) {
	// 				check = true;
	// 			}
	// 			else {
	// 				check = false;
	// 			}
	// 		} else {
	// 		check = true;
	// 		}
	// 		return this.optional(element) || check;
	// 	},
	// 	"Wrong date format"
	// );

	$('#new-medical-form').validate({
		rules: {
			mf_email: {
				emailcheck: true
			},
			mf_password: {
				pwcheck: true
			},
		},
		submitHandler: function (form) {
			/*If validation fails stop form submission*/
			return false;
		}
	});

	// $( '.hc-dashboard-save-user-updates' ).on( 'click', function(e) {

	// 	e.preventDefault();

	// 	var inputs, messages, index;
	// 	var isValid = true;

	// 	var form_id = $(this).data('form');

	// 	inputs = $( '#' + form_id + ' input' );


	// 	for (index = 0; index < inputs.length; ++index) {
			
	// 		var currentInputName = inputs[index].name;
	// 		// console.log( index );
	// 		// console.log( inputs );
	// 		// console.log( currentInputName );
			
	// 		if ( ! $( 'input[name="'+ currentInputName +'"]' ).is(':checked') ) {
	// 			if( inputs[index].type !== 'hidden'){

	// 				if( ! $('#' + inputs[index].id ).parents('.mf-step').find('.cus-error').length ){
	// 					$('#' + inputs[index].id ).parents('.mf-step').append( '<span class="cus-error">This field is required</span>' );
	// 				}
	// 				// inputs[index].after( '<span>This field is required</span>' );
	// 				// messages[index].style.display = "block";
	// 				isValid = false;
	// 				return false;
	// 			}
	// 		}
	// 		// else{
	// 		// 	$('#' + inputs[index].id ).parents('.mf-step').find('.cus-error').remove();
	// 		// 	console.log('else :' + isValid);
	// 		// }
	// 	}
		
	// 	console.log( isValid );

	// 	if ( isValid ) {
	// 		$( 'cus-error' ).remove();
	// 			// $( '#' + form_id ).submit();
	// 		alert('success');
	// 	} else {
	// 		$(this).before( '<span class="cus-error">The form contains errors. Please fill in all the fields and submit the form again.</span>' )
	// 	}

		
	// } );

	$('.form-3-btn').on('click', function(event) {
		var to_validate = ['mf_fullname', 'mf_email', 'mf_password'];
        var valid = true;

        $.each(to_validate, function (k, v) {
			if ( $('#new-medical-form [name="' + v + '"]').length > 0 ) {

				if (!$('#new-medical-form [name="' + v + '"]').valid()) {
					valid = false;
				}
			}
        });

		if (valid) {
			$(this).parents('.mf-step').hide().next().fadeIn('slow');
		}
	});

	/*Pop form submission*/
	$('#md_popup_signup').submit(function (event) {
		var to_validate = ['form2_email','form2_password'];
        var valid = true;

        $.each(to_validate, function (k, v) {
        	if (!$('#md_popup_signup [name="' + v + '"]').valid()) {
            	valid = false;
            }
        });

        if (valid) {
			$('.form-submit').after('<span class="mf-spinner">');
			var params = $('#md_popup_signup').serializeArray();
			console.log(params);

           	params.push({ name: 'action', value: 'new_process_medical_form_popup' });

            $.post(new_wp_paths.new_admin, params, function(response) {
				var data = $.parseJSON(response);
				if (data.success != false) {
					dataLayer.push({
						event: 'eligibililtyAccount'
					});
                	$('.mf-spinner').remove();
                	$('.mf-message').remove();
                	$('.form-submit').after('<div class="mf-message mf-message--green">'+data.message+'</div>');
                	var modal = document.getElementById("MF_SIGNUP_MODAL");
                	modal.style.display = "none";
                	window.location.href = window.location.href.split('?')[0];
                } else {
                	$('.mf-spinner').remove();
                	$('.mf-message').remove();
                	$('.form-submit').after('<div class="mf-message mf-message--red">'+data.message+'</div>');
                    return false;
                }
			});
        }else{
        	$('.mf-spinner').remove();
        }
	});

	function hc_get_redirection_url() {

        // event.preventDefault();
        var formdata = $('#new-medical-form').serializeArray();
        var data = {};
        var redirection = '';
        var args = '';
        $(formdata).each(function(index, obj){
            if(obj.value != ''){
                data[obj.name] = obj.value;
            }
        });

        if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] != 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis][answer]'] == 'Cialis' &&
            data['medical_form_details[recommended_prescription][cialis_effective][answer]'] == 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]'] != 'Daily Cialis' &&
            data['medical_form_details[recommended_prescription][daily_cialis_effective][answer]'] != 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][cialis_redirection_link]'];
        }
        else if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] != 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis][answer]'] != 'Cialis' &&
            data['medical_form_details[recommended_prescription][cialis_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]'] == 'Daily Cialis' &&
            data['medical_form_details[recommended_prescription][daily_cialis_effective][answer]'] == 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][daily_cialis_redirection_link]'];
        }
        else if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] != 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis][answer]'] == 'Cialis' &&
            data['medical_form_details[recommended_prescription][cialis_effective][answer]'] == 'Yes' &&
            data['medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]'] == 'Daily Cialis' &&
            data['medical_form_details[recommended_prescription][daily_cialis_effective][answer]'] == 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][cialis_redirection_link]'];
        }
        else if(
            data['medical_form_details[recommended_prescription][previous_use_sildenafil][answer]'] == 'Sildenafil' &&
            data['medical_form_details[recommended_prescription][sildenafil_effective][answer]'] != 'Yes'
        ){
            redirection = data['medical_form_details[redirection_link][cialis_redirection_link]'];
        }
        else{
            redirection = data['medical_form_details[redirection_link][sildenafil_redirection_link]'];
        }

        return redirection;

    }

	/*Form submission*/
	$('#new-medical-form').submit(function (event) {

		var to_validate = ['medical_form_details[additional_details][answer]'];
        var valid = true;

        $.each(to_validate, function (k, v) {
			if ( $('#new-medical-form [name="' + v + '"]').length > 0 ) {
				if (!$('#new-medical-form [name="' + v + '"]').valid()) {
					valid = false;
				}
			}
        });

		if (valid) {
			$('.form-submit').after('<span class="mf-spinner">');
			
			$suggested_product = hc_get_redirection_url();
			$( '#flagged_user_suggested_product' ).val( $suggested_product );
			
			var params = $('#new-medical-form').serializeArray();

			console.log(params);
			//alert();
           	params.push({ name: 'action', value: 'new_process_medical_form' });

            $.post(new_wp_paths.new_admin, params, function(response) {
				var data = $.parseJSON(response);
				console.log(typeof data.success);
				if (data.success != false) {
                	$('.mf-spinner').remove();
                	$('.mf-message').remove();
					$('.form-submit').after('<div class="mf-message mf-message--green">'+data.message+'</div>');
					window.location.href = data.redirection;
					/* dataLayer.push({
						event: 'eligibililtyAccount',
					}); */
                } else {
                	$('.mf-spinner').remove();
                	$('.mf-message').remove();
                	$('.form-submit').after('<div class="mf-message mf-message--red">'+data.message+'</div>');
                	// $('[mf-step="16"] .mf-progress').css({"opacity":0, "pointer-events": "none"});
						return;
                    //alert(data.message);

                    return false;
                }
			});
        }else{
        	$('.mf-spinner').remove();
        }
	});
});
