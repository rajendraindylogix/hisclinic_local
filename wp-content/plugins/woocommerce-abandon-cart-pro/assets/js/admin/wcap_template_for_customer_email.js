jQuery( function( $ ) {
	
	$(document).on('change', '#wcap_email_action', function() {
        var wcap_selected_value = this.value;
        if ( 'wcap_email_others' == wcap_selected_value) {
        	$( "#wcap_other_emails" ).fadeIn();
        }else {
        	$( "#wcap_other_emails" ).fadeOut();
        }
    });
});