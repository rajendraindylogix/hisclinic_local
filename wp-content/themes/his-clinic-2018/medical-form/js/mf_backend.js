jQuery(function($){
	$( '#jaywing-merge-gf-orders' ).on( 'click', function(e) {

        e.preventDefault();

        $('.loading-spinner').fadeIn();

        formdata = {};

        formdata['action'] = 'jaywing_merge_gf_orders';

        $.post(new_wp_paths.new_admin, formdata, function (data, form, jqXHR) {

            if (data.success) {

                alert( 'COMPLETED: Merge Gravity Forms and Order data\n\n' + data.data ); 
            } else {
				alert('There was an issue.');
            }
        }, 'json');

    } );

    $( '.approve-reject' ).on( 'change', function(e) {
        $('.loading-spinner').fadeIn();
        e.preventDefault();

        form = $(this).parent('.admin-treatment-change-request');

        formdata = form.serializeArray();

        $.post(new_wp_paths.new_admin, formdata, function (data, form, jqXHR) {

            if (data.success) {
                
                $('#form-' + data.data.key ).html(data.data.message);
                $('.loading-spinner').fadeOut();
            } else {

            }
        }, 'json');
    
    } )

    $( '#hc-send-chat-message-backend' ).on( 'submit', function(e) {
        
        e.preventDefault();

        data = $(this).serializeArray();

        // console.log ( data );

        $( '#hc-send-chat-submit' ).attr( 'disabled', 'disabled' ).text('Sending...');

        $.post(new_wp_paths.new_admin, data, function (data, textStatus, jqXHR) {

            // console.log(data);
            
            if (data.success) {
                
                $( '#msg-reply' ).val('');
                $('#hc-send-chat-submit').removeAttr( 'disabled' ).text('Send Message');

                var template = wp.template('hc-chat-block');
                // var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
                $('#chat-blocks-wrap').append(template({ data: data }));
        
            } else {
                
                

            }
        }, 'json');

    } );

    $( '#suggested-product' ).on( 'change', function(e) {
        e.preventDefault();

        if ( '' !== $(this).val() ) {

            formdata = $('#suggest-products-form').serializeArray(); 

            console.log( formdata );

            $.post(new_wp_paths.new_admin, formdata, function (data, textStatus, jqXHR) {

                console.log(data);

                
                if (data.success) {
                    
                    location.reload();
            
                } else {
                    
                    
    
                }
            }, 'json');

        }

    } );

});

jQuery(window).load(function(){
    //trigger tab based on url 
    if(jQuery('.product-information').length){
        jQuery(window.location.hash).tab('show');
    }
    window.scrollTo(0,0);
});

jQuery(function($){

    $( '#hc-merge-old-medical-form-users' ).on( 'click', function(e) {

        e.preventDefault();

        $('.loading-spinner').fadeIn();

        formdata = {};

        formdata['action'] = 'hc_merge_sync_old_mf_users';

        $.post(new_wp_paths.new_admin, formdata, function (data, form, jqXHR) {

            if (data.success) {

                alert( 'Customers Medical forms data imported' ); 
            } else {

            }
        }, 'json');

    } )

    $( '#hc-merge-gravity-form-users' ).on( 'click', function(e) {

        e.preventDefault();

        $('.loading-spinner').fadeIn();

        formdata = {};

        formdata['action'] = 'hc_merge_sync_gravity_forms_mf_users';

        $.post(new_wp_paths.new_admin, formdata, function (data, form, jqXHR) {

            if (data.success) {

                alert( 'Customers Medical forms data imported' ); 
            } else {

            }
        }, 'json');

    } )

    $( '#hc-merge-orders-allergies-question' ).on( 'click', function(e) {

        e.preventDefault();

        $('.loading-spinner').fadeIn();

        formdata = {};

        formdata['action'] = 'hc_merge_sync_order_allergy_details';

        $.post(new_wp_paths.new_admin, formdata, function (data, form, jqXHR) {

            if (data.success) {

                alert( 'Customers allergy details imported from orders' ); 
            } else {

            }
        }, 'json');

    } )

});
