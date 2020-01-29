jQuery( function( $ ) {
	"use strict";
	// Color picker
	$( '.colorpick' ).iris({
		change: function( event, ui ) {
			$( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });

			var wcap_class_name = this.getAttribute('class');

			console.log(wcap_class_name.indexOf('heading_color_picker'));
			if ( wcap_class_name.indexOf('popup_heading_color_picker') > 0 ){
				wcap_atc_modal_data.wcap_atc_popup_heading.color = ui.color.toString();	
			}
			if ( wcap_class_name.indexOf('popup_text_color_picker') > 0 ){
				wcap_atc_modal_data.wcap_atc_popup_text.color = ui.color.toString();	
			}
			if ( wcap_class_name.indexOf('button_text_color_picker') > 0 ){
				wcap_atc_modal_data.wcap_atc_button.color = ui.color.toString();	
			}
			if ( wcap_class_name.indexOf('button_color_picker') > 0 ){
				wcap_atc_modal_data.wcap_atc_button.backgroundColor = ui.color.toString();	
			}
		},
		hide: true,
		border: true
	}).click( function() {
		$( '.iris-picker' ).hide();
		$( this ).closest( 'td' ).find( '.iris-picker' ).show();
	});

	$( 'body' ).click( function() {
		$( '.iris-picker' ).hide();
	});

	$( '.colorpick' ).click( function( event ) {
		event.stopPropagation();
	});

	var wcap_atc_modal_data = {
	    wcap_heading_section_text_email: wcap_atc_color_picker_params.wcap_atc_head,
	    wcap_text_section_text_field:    wcap_atc_color_picker_params.wcap_atc_text,
	    wcap_email_placeholder_section_input_text: wcap_atc_color_picker_params.wcap_atc_email_place,
	    wcap_button_section_input_text : wcap_atc_color_picker_params.wcap_atc_button,
	    wcap_button_bg_color : wcap_atc_color_picker_params.wcap_atc_button_bg_color,
	    wcap_button_text_color : wcap_atc_color_picker_params.wcap_atc_button_text_color,
	    wcap_popup_text_color : wcap_atc_color_picker_params.wcap_atc_popup_text_color,
	    wcap_popup_heading_color : wcap_atc_color_picker_params.wcap_atc_popup_heading_color,
	    wcap_non_mandatory_modal_input_text : wcap_atc_color_picker_params.wcap_atc_non_mandatory_input_text,
		wcap_atc_button: {
			backgroundColor: wcap_atc_color_picker_params.wcap_atc_button_bg_color,
			color          : wcap_atc_color_picker_params.wcap_atc_button_text_color  
		},
		wcap_atc_popup_text:{
			color          : wcap_atc_color_picker_params.wcap_atc_popup_text_color,	
		},
		wcap_atc_popup_heading:{
			color          : wcap_atc_color_picker_params.wcap_atc_popup_heading_color,	
		}
	};
	var myViewModel = new Vue({
	    el: '#wcap_popup_main_div',
	    data: wcap_atc_modal_data,
	});
	
});