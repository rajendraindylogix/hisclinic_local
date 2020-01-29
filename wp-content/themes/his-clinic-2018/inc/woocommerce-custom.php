<?php
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
// remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
remove_filter( 'wcs_cart_totals_order_total_html', 'wcs_add_cart_first_renewal_payment_date', 10, 2 );
remove_filter( 'woocommerce_order_button_text', 'WC_Subscriptions::order_button_text');

function hisclinic_myaccount_custom_myaccount() {
    add_rewrite_endpoint('help', EP_PAGES);
    add_rewrite_endpoint('medical-details', EP_PAGES);
    add_rewrite_endpoint('request-treatment-change', EP_PAGES);
    add_rewrite_endpoint('wc-user-logout', EP_PAGES);

}
add_action( 'init', 'hisclinic_myaccount_custom_myaccount' );

function hisclinic_custom_help_endpoint_content() {
    get_template_part('woocommerce/myaccount/help');
}
add_action( 'woocommerce_account_help_endpoint', 'hisclinic_custom_help_endpoint_content' );

function hisclinic_custom_medical_details_endpoint_content() {
    get_template_part('woocommerce/myaccount/medical-details');
}
add_action( 'woocommerce_account_medical-details_endpoint', 'hisclinic_custom_medical_details_endpoint_content' );

function hisclinic_custom_request_treatment_change_endpoint_content() {
    get_template_part('woocommerce/myaccount/request-treatment-change');
}
add_action( 'woocommerce_account_request-treatment-change_endpoint', 'hisclinic_custom_request_treatment_change_endpoint_content' );

function hisclinic_custom_wp_user_logout_endpoint_content() {
    get_template_part('woocommerce/myaccount/wc-user-logout');
}
add_action( 'woocommerce_account_wc-user-logout_endpoint', 'hisclinic_custom_wp_user_logout_endpoint_content' );

function save_account_password() {
    if (!$_POST || empty($_POST['action']) || $_POST['action'] !== 'save_account_password') {
        return;
    }

    $nonce_value = wc_get_var( $_POST['save-account-password-nonce'], wc_get_var( $_POST['_wpnonce'], '' ) );

    if ( ! wp_verify_nonce( $nonce_value, 'save_account_password' ) ) {
        return;
    }

    $user_id = get_current_user_id();
    if ( $user_id <= 0 ) {
        return;
    }

    $user = new stdClass();
    $current_user = get_user_by('id', $user_id);
    $user->ID = $user_id;

    $pass_cur = !empty($_POST['password_current']) ? $_POST['password_current'] : '';
    $pass1 = !empty($_POST['password_1']) ? $_POST['password_1'] : '';
    $pass2 = !empty($_POST['password_2']) ? $_POST['password_2'] : '';
    $save_pass = true;

    if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please fill out all password fields.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
        wc_add_notice( __( 'Please enter your current password.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please re-enter your password.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
        wc_add_notice( __( 'New passwords do not match.', 'woocommerce' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
        wc_add_notice( __( 'Your current password is incorrect.', 'woocommerce' ), 'error' );
        $save_pass = false;
    }

    if ($pass1 && $save_pass) {
        $user->user_pass = $pass1;

        wp_update_user( $user );
        wc_add_notice( __( 'Password changed successfully.', 'woocommerce' ) );
    }

    wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
    exit;
}
add_action( 'template_redirect', 'save_account_password' );

function save_account_avatar() {
    if (!$_POST || empty($_POST['action']) || $_POST['action'] !== 'save_account_avatar') {
        return;
    }

    $avatar = (!empty($_POST['avatar'])) ? esc_sql($_POST['avatar']) : null;

    if ($avatar) {
        $user_id = get_current_user_id();

        if (update_user_meta($user_id, 'avatar', $avatar)) {
            wc_add_notice('Avatar updated successfully');
        } else {
            wc_add_notice('There was an error when updating your avatar, please try again.', 'error');
        }
    }
}
add_action( 'template_redirect', 'save_account_avatar' );

function get_user_avatar($user_id) {
    if (!$avatar = get_usermeta($user_id, 'avatar')) {
        $avatar = 'default.png';
    }

    return $avatar;
}

function get_user_avatar_url($avatar) {
    return theme() . '/assets/img/avatars/' . $avatar;
}

function get_order_item_meta_value($item, $key, $display_value = true) {
    $data = $item->get_formatted_meta_data();
    $found = null;

    foreach ($data as $d) {
        if ($d->key == $key) {
            if ($display_value) {
                $found = $d->display_value;
            } else {
                $found = $d->value;
            }

            break;
        }
    }

    return strip_tags($found);
}

// Preventing redirection to some account endpoints
function maybe_redirect_endpoint($url, $endpoint, $value, $permalink) {
    if ($endpoint == 'edit-address') {
        $url = home_url('my-account');
    }

    return $url;
}
add_filter( 'woocommerce_get_endpoint_url', 'maybe_redirect_endpoint', 10, 4 );

/* Updating price labelling for subscriptions */
function service_custom_price_label($subscription_string, $product, $include) {
	return str_replace('/ day', '', $subscription_string);
}
add_filter( 'woocommerce_subscriptions_product_price_string', 'service_custom_price_label', 1, 3 );

function custom_price_tags($subscription_string, $subscription_details) {
    if (!empty($subscription_details['subscription_period']) && $subscription_details['subscription_period'] == 'day') {
        $subscription_string = str_replace('/ day', '', $subscription_string);
    }

	return $subscription_string;
}
add_filter( 'woocommerce_subscription_price_string', 'custom_price_tags', 1, 2 );

/* Removing "once" (one off payments) subscriptions from listings */
function filter_out_subscriptions($subscriptions) {
    foreach ($subscriptions as $key => $s) {
        $status = $s->get_status();

        if ($status != 'pending-cancel' && !$s->schedule_next_payment) {
            unset($subscriptions[$key]);
        }
    }

    return $subscriptions;
}
add_filter( 'wcs_get_users_subscriptions', 'filter_out_subscriptions', 1, 1 );
add_filter( 'woocommerce_got_subscriptions', 'filter_out_subscriptions', 1, 1 );

/* Cancelling next payment if product is only to be paid once. */
function subscription_cancel_single_package ($subscription) {
	$items = $subscription->get_items();
	$frequency = null;

	foreach ($items as $item) {
    	$data = $item->get_data();

    	foreach ($data['meta_data'] as $md) {
			$d = $md->get_data();

			if ($d['key'] == 'pa_frequency') {
				$frequency = $d['value'];
			}
    	}
	}

    if ($frequency == 'once') {
        $subscription->update_dates(['next_payment' => 0]);
	}
}
add_action( 'woocommerce_subscription_payment_complete', 'subscription_cancel_single_package', 10, 1 );

function custom_woocommerce_subscriptions_product_price_string($subscription_string, $product, $include) {
	$attributes = $product->get_attributes();
	$frequency = isset($attributes['pa_frequency']) ? $attributes['pa_frequency'] : '';

	if ( $frequency == 'once' ) $subscription_string = $include['price'];

    return $subscription_string;
}
add_filter('woocommerce_subscriptions_product_price_string', 'custom_woocommerce_subscriptions_product_price_string', 3, 10);

function custom_nav_classes($classes, $endpoint) {

    if ($endpoint == 'subscriptions' && WC()->query->get_current_endpoint() == 'view-subscription') {
        $classes[] = 'is-active';
    }

    return $classes;
}
add_filter( 'woocommerce_account_menu_item_classes', 'custom_nav_classes', 1, 2 );

/**
 * Outputs a checkout/address form field.
 *
 * @param string $key Key.
 * @param mixed  $args Arguments.
 * @param string $value (default: null).
 * @return string
 */
function woocommerce_checkout_form_field( $key, $args, $value = null ) {
    $defaults = array(
        'type'              => 'text',
        'label'             => '',
        'description'       => '',
        'placeholder'       => '',
        'maxlength'         => false,
        'required'          => false,
        'autocomplete'      => false,
        'id'                => $key,
        'class'             => array(),
        'label_class'       => array(),
        'input_class'       => array(),
        'return'            => false,
        'options'           => array(),
        'custom_attributes' => array(),
        'validate'          => array(),
        'default'           => '',
        'autofocus'         => '',
        'priority'          => '',
    );

    $args = wp_parse_args( $args, $defaults );
    $args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );

    if ( $args['required'] ) {
        $args['class'][] = 'validate-required';
        $required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
    } else {
        $required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
    }

    if ( is_string( $args['label_class'] ) ) {
        $args['label_class'] = array( $args['label_class'] );
    }

    if ( is_null( $value ) ) {
        $value = $args['default'];
    }

    // Custom attribute handling.
    $custom_attributes         = array();
    $args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

    if ( $args['maxlength'] ) {
        $args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
    }

    if ( ! empty( $args['autocomplete'] ) ) {
        $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
    }

    if ( true === $args['autofocus'] ) {
        $args['custom_attributes']['autofocus'] = 'autofocus';
    }

    if ( $args['description'] ) {
        $args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
    }

    if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
        foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
        }
    }

    if ( ! empty( $args['validate'] ) ) {
        foreach ( $args['validate'] as $validate ) {
            $args['class'][] = 'validate-' . $validate;
        }
    }

    $field           = '';
    $label_id        = $args['id'];
    $sort            = $args['priority'] ? $args['priority'] : '';
    $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

    switch ( $args['type'] ) {
        case 'country':
            $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

            if ( 1 === count( $countries ) ) {

                $field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

                $field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';

            } else {

                $field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . '><option value="">' . esc_html__( 'Select a country&hellip;', 'woocommerce' ) . '</option>';

                foreach ( $countries as $ckey => $cvalue ) {
                    $field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
                }

                $field .= '</select>';

                $field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'woocommerce' ) . '">' . esc_html__( 'Update country', 'woocommerce' ) . '</button></noscript>';

            }

            break;
        case 'state':
            /* Get country this state field is representing */
            $for_country = isset( $args['country'] ) ? $args['country'] : WC()->checkout->get_value( 'billing_state' === $key ? 'billing_country' : 'shipping_country' );
            $states      = WC()->countries->get_states( $for_country );

            if ( is_array( $states ) && empty( $states ) ) {

                $field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

                $field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" readonly="readonly" />';

            } elseif ( ! is_null( $for_country ) && is_array( $states ) ) {

                $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
                    <option value="">' . esc_html__( 'Select a state&hellip;', 'woocommerce' ) . '</option>';

                foreach ( $states as $ckey => $cvalue ) {
                    $field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
                }

                $field .= '</select>';

            } else {

                $field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

            }

            break;
        case 'textarea':
            $field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

            break;
        case 'checkbox':
            $field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
                    <input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';

            break;
        case 'text':
        case 'password':
        case 'datetime':
        case 'datetime-local':
        case 'date':
        case 'month':
        case 'time':
        case 'week':
        case 'number':
        case 'email':
        case 'url':
        case 'tel':
            $field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

            break;
        case 'select':
            $field   = '';
            $options = '';

            if ( ! empty( $args['options'] ) ) {
                foreach ( $args['options'] as $option_key => $option_text ) {
                    if ( '' === $option_key ) {
                        // If we have a blank option, select2 needs a placeholder.
                        if ( empty( $args['placeholder'] ) ) {
                            $args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
                        }
                        $custom_attributes[] = 'data-allow_clear="true"';
                    }
                    $options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) . '</option>';
                }

                $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
                        ' . $options . '
                    </select>';
            }

            break;
        case 'radio':
            $label_id = current( array_keys( $args['options'] ) );

            if ( ! empty( $args['options'] ) ) {
                foreach ( $args['options'] as $option_key => $option_text ) {
                    $field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
                    $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . $option_text . '</label>';
                }
            }

            break;
    }

    if ( ! empty( $field ) ) {
        $field_html = '';

        $field_container = '<div class="animate-input %1$s" id="%2$s" >%3$s</div>';
            
        //     if ( $args['label'] && 'checkbox' !== $args['type'] ) {
        //         $field_html .= '<label for="' . esc_attr( $label_id ) . '">' . $args['label'] .'</label>';
        //     }


        if ( $args['label'] && 'checkbox' !== $args['type'] ) {
            $field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . $args['label'] . $required . '</label>';
        }

        $field_html .= '<span class="woocommerce-input-wrapper">' . $field;

        if ( $args['description'] ) {
            $field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
        }

        $field_html .= '</span>';

        $container_class = esc_attr( implode( ' ', $args['class'] ) );
        $container_id    = esc_attr( $args['id'] ) . '_field';
        $field           = sprintf( $field_container, $container_class, $container_id, $field_html );

        // var_dump( $field );
    }

    /**
     * Filter by type.
     */
    $field = apply_filters( 'woocommerce_form_field_' . $args['type'], $field, $key, $args, $value );

    /**
     * General filter on form fields.
     *
     * @since 3.4.0
     */
    $field = apply_filters( 'woocommerce_form_field', $field, $key, $args, $value );

    if ( $args['return'] ) {
        return $field;
    } else {
        echo $field; // WPCS: XSS ok.
    }
}

add_filter('woocommerce_add_to_cart_redirect', 'his_clinic_add_to_cart_redirect');
/**
 * checkout redirect
 *
 * @return void
 */
function his_clinic_add_to_cart_redirect() {
    global $woocommerce;
    $checkout_url = wc_get_checkout_url();
    return $checkout_url;
}

add_filter( 'wc_add_to_cart_message_html', '__return_null' );


function his_clinic_custom_override_checkout_fields( $fields ) {
    unset( $fields['billing']['billing_email'] );
    unset( $fields['billing']['billing_country'] );
    unset( $fields['shipping']['shipping_country'] );
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'his_clinic_custom_override_checkout_fields' );

// remove a template redirect from within a custom plugin.
// add_action( 'template_redirect', 'hisclinic_cart_page_redirect_my_action', 5 );

remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

function hisclinic_cart_page_redirect_my_action(){
    
    global $woocommerce;
    
    if( is_cart() ){
        
        wp_safe_redirect( home_url( '/order-details?prod_id=5924' ) );
    
    }
}

/**
 * @snippet       WooCommerce Max 1 Product @ Cart
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=560
 * @author        Rodolfo Melogli
 * @compatible    WC 3.5.4
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
add_filter( 'woocommerce_add_to_cart_validation', 'hisclinic_bbloomer_only_one_in_cart', 99, 2 );

function hisclinic_bbloomer_only_one_in_cart( $passed, $added_product_id ) {
    // empty cart first: new item will replace previous
    wc_empty_cart();

    return $passed;
}

// add_action('woocommerce_after_order_notes', 'hic_clinic_cw_custom_checkbox_fields');

// function hic_clinic_cw_custom_checkbox_fields( $checkout ) {
//     // echo '<div class="cw_custom_class"><h3>'.__('Give Sepration Heading: ').'</h3>';
//     woocommerce_form_field( 'medical_condition_checkbox', array(
//         'type'          => 'checkbox',
//         'label'         => __( 'Agreegation Policy.', 'woocommerce' ),
//         'required'  => true,
//     ), $checkout->get_value( 'medical_condition_checkbox' ));
//     // echo '</div>';
// }

// Uncheck ship to different address option by default.
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );


add_filter( 'woocommerce_subscriptions_calculated_total', function( $total ) {

    $total = 0;

    global $woocommerce;

    $items = $woocommerce->cart->get_cart();

        foreach($items as $item => $values) { 
            
            $_product =  wc_get_product( $values['data']->get_id()); 
            
            $total += $_product->get_price();
        } 

    return $total;

}, 999 );


// Show notice if customer does not tick

add_action( 'woocommerce_checkout_process', 'his_clinic_not_approved_privacy' );

function his_clinic_not_approved_privacy() {
    if ( ! (int) isset( $_POST['checked_medical_details_confirm'] ) ) {
        wc_add_notice( __( 'Please check both confirmation checkboxes' ), 'error' );
    }
}

/**
 * Looper
 */
function hisclinic_looper_object_to_array( $data )
{
    if ( is_array( $data ) || is_object( $data ) )
    {
        $result = array();
        foreach ( $data as $key => $value )
        {
            $result[$key] = hisclinic_looper_object_to_array( $value );
        }
        return $result;
    }
    return $data;
}

//Function
function hisclinic_get_page_id_by_page_name($page_name){
    
    global $wpdb;
    
    $page_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
    
    return $page_name;
}

add_filter('woocommerce_save_account_details_required_fields', 'custom_account_required_fields');

/**
 * hide last name
 *
 * @param [type] $required_fields
 * @return void
 */
function custom_account_required_fields( $required_fields ) {
    unset( $required_fields["account_first_name"] );
    unset( $required_fields["account_last_name"] );
    unset( $required_fields['account_display_name'] );
    return $required_fields;
}

// Check and validate the mobile phone
add_action( 'woocommerce_save_account_details_errors','hc_billing_mobile_phone_field_validation', 20, 1 );
function hc_billing_mobile_phone_field_validation( $args ){
    if ( isset($_POST['billing_mobile_phone']) && empty($_POST['billing_mobile_phone']) )
        $args->add( 'error', __( 'Please fill in your Mobile phone', 'woocommerce' ),'');
}

// Save the mobile phone value to user data
add_action( 'woocommerce_save_account_details', 'hc_my_account_saving_billing_mobile_phone', 20, 1 );
function hc_my_account_saving_billing_mobile_phone( $user_id ) {
    if( isset($_POST['billing_mobile_phone']) && ! empty($_POST['billing_mobile_phone']) )
        update_user_meta( $user_id, 'billing_mobile_phone', sanitize_text_field($_POST['billing_mobile_phone']) );
}

// Check and validate the shipping address
add_action( 'woocommerce_save_account_details_errors','hc_shipping_address_field_validation', 20, 1 );
function hc_shipping_address_field_validation( $args ){
    if ( isset($_POST['account_shipping_address']) && empty($_POST['account_shipping_address']) )
        $args->add( 'error', __( 'Please fill in your Mobile phone', 'woocommerce' ),'');
}

// Save the mobile phone value to user data
add_action( 'woocommerce_save_account_details', 'hc_my_account_saving_shipping_address', 20, 1 );
function hc_my_account_saving_shipping_address( $user_id ) {
    if( isset($_POST['account_shipping_address']) && ! empty($_POST['account_shipping_address']) )
        update_user_meta( $user_id, 'account_shipping_address', sanitize_text_field($_POST['account_shipping_address']) );
}

/*
 * New user registrations should have display_name set 
 * to 'firstname lastname'. This is best used on the
 * 'user_register' action.
 *
 * @param int $user_id The user ID
 */
function his_clinic_set_default_display_name( $user_id ) {
    $user = get_userdata( $user_id );
    $name = sprintf( '%s', $user->first_name );
    $args = array(
        'ID'           => $user_id,
        'display_name' => $name,
        'nickname'     => $name
    );
    wp_update_user( $args );
}
add_action( 'user_register', 'his_clinic_set_default_display_name' );

// Thank you page text
function his_woocommerce_thankyou_text($order_id) {
	$order = wc_get_order($order_id);
	$customer_id = $order->get_customer_id();
	$customer = new WC_Customer($customer_id);

	$form = get_user_meta($customer_id, 'medical-form', true);
/*
	$form = get_user_meta($customer_id, 'medical-form-new', true);
	$form_id = 'medical-forms-new';
	if (empty($form)) {
		$form = get_user_meta($customer_id, 'medical-form', true);
		$form_id = 'medical-forms';
	}
*/
	$form = json_decode($form, true);
	$updated_questionnaire = get_user_meta($customer_id, 'updated_questionnaire', true);
	$form_old = get_user_meta($customer_id, 'medical-form-old', true);
	$customer_name = $customer->display_name;
	$email = $customer->get_billing_email();
	(empty($form['date-of-birth'])) ? $dob = $form['date'] : $dob = $form['date-of-birth'];
	if ($form_old === '{}' && !$updated_questionnaire) {
		echo
		'<style>
		label {
			font-size: 14px !important;
		}
		.gform_next_button, .gform_submit_button {
			float: right;
			width: 30%;
			background-color: #fa1c41 !important;
			color: white;
		}
		.gform_previous_button {
			float: left;
			width: 30%;
			background-color: #606060 !important;
			color: white;
		}
		.instruction {
			display: none;
		}
		</style>';
		echo '<h2 class="text-center">Just a few more questions before you go...</h2>';
		echo do_shortcode('[gravityform id=1 title=false description=false ajax=true tabindex=49 field_values="customer_name='.$customer_name.'&email='.$email.'&dob='.$dob.'"]');
	} else {
		echo '<h2 class="text-center">Your order will now be reviewed by our medical team. One of our doctors will make contact via email and arrange a phone call if necessary.</h2>';
	}
}
add_action('woocommerce_thankyou', 'his_woocommerce_thankyou_text', 1, 1 );




/*Allergy code - 7-1-2019*/
// define the woocommerce_before_order_notes callback
function action_woocommerce_before_order_notes() {
    // make action magic happen here...
    echo '<div id="allergies_checkout_field"><h3>' . __('Allergies') . '</h3>';
    ?>
    <table>
        <tr>
            <td>
                <span><?php _e( 'Do you have any severe allergies to food or medication?', 'woocommerce' ); ?></span>
                <div class="options">
                    <label class="option field radio" for="allergies-yes">
                        <input class="allergies-options" name="allergies_check" id="allergies-yes" type="radio" value="yes">
                        <span class="box"><?php echo __( 'Yes', 'woocommerce' ); ?></span>
                    </label>
                    <label class="option field radio" for="allergies-no">
                        <input class="allergies-options" name="allergies_check" id="allergies-no" type="radio" checked value="no">
                        <span class="box"><?php echo __( 'No', 'woocommerce' ); ?></span>
                    </label>
                </div>
            </td>
        </tr>
        <tr id="allergies-details">
            <td>
                <form id="allergy-text">
                    <div class="detail-textarea" style="display:none;">
                        <span><?php _e( 'Please provide details of the allergies', 'woocommerce' ); ?></span>
                        <div class="animate-input">
                            <textarea bind="bindedAllergiesDetails" id="allergies-details-input" name="allergies_details" cols="50" rows="10"></textarea>
                        </div>

                    </div>
                </form>

            </td>

        </tr>
    </table>
    <?php
    echo '</div>';
};

// add the action
// add_action( 'woocommerce_review_order_before_payment', 'action_woocommerce_before_order_notes', 10, 1 );

/**
 * Process the checkout
 */
// add_action('woocommerce_checkout_process', 'allergies_checkout_field_process');

function allergies_checkout_field_process() {
    // Check if set, if its not set add an error.
    if ( ! $_POST['allergies_check'] )
        wc_add_notice( __( 'Please enter your allergy information.' ), 'error' );
    if ( isset($_POST['allergies_check']) && $_POST['allergies_check'] == 'yes' && ! $_POST['allergies_details'] )
        wc_add_notice( __( 'Please enter your allergy information.' ), 'error' );
}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['allergies_check'] ) ) {
        update_post_meta( $order_id, 'allergies_check', sanitize_text_field( $_POST['allergies_check'] ) );
    }
    if ( ! empty( $_POST['allergies_details'] ) ) {
        update_post_meta( $order_id, 'allergies_details', sanitize_text_field( $_POST['allergies_details'] ) );
    }

    if ( is_user_logged_in() ) {

        $user_id = get_current_user_id();

        if ( isset( $_POST['allergies_details'] ) && 'yes' === $_POST['allergies_check'] ) {

            update_user_meta( $user_id, 'flagged_for_review', 'true' );

/*
            // Merge Allergies data on checkout to MF.
            $saved_mf_user_data       = get_user_meta( $user_id, MF_KEY_NEW, true );
            $saved_mf_user_data_array = json_decode( maybe_unserialize( $saved_mf_user_data ) );
            $saved_mf_user_data_array = his_clinic_object_to_array( $saved_mf_user_data_array );

            $allergies_check   = $_POST['allergies_check'];
            $allergies_details = $_POST['allergies_details'];


            if ( isset( $saved_mf_user_data_array['form4_description'] ) ) :
                
                $saved_mf_user_data_array['form4_description'] .= 'Severe allergies to food or medication?: '.$allergies_check;
                $saved_mf_user_data_array['form4_description'] .= ' Allergies details: '.$allergies_details;
                
                else:
                    
                    $saved_mf_user_data_array['form4_description'] = 'Severe allergies to food or medication?: '.$allergies_check;
                    $saved_mf_user_data_array['form4_description'] .= ' Allergies details: '.$allergies_details;

            endif;
                
            
            $new_updated_mf_data = json_encode( $saved_mf_user_data_array );
            
            $updated = update_user_meta( $user_id, MF_KEY_NEW, $new_updated_mf_data );
*/

        }

    }
}
/**
 * Display field value on the order edit page
 */
// add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Severe allergies to food or medication?').':</strong> ' . get_post_meta( $order->id, 'allergies_check', true ) . '</p>';
    echo '<p><strong>'.__('Allergies details').':</strong> ' . get_post_meta( $order->id, 'allergies_details', true ) . '</p>';
}

add_filter( 'woocommerce_show_variation_price', '__return_true' );

// Redirect users to Medical Details if not updated medical form
function his_redirect_customers_medical_details() {
	if ( strpos($_SERVER['REQUEST_URI'], 'order-details/') !== false || strpos($_SERVER['REQUEST_URI'], 'cart/') !== false || strpos($_SERVER['REQUEST_URI'], 'checkout/') !== false ) {
		$user_id = get_current_user_id();

        if ($user_id) {
            $personal_information_updated = get_user_meta( $user_id, 'mf-personal_information-updated', true );
            $sexual_activity_updated = get_user_meta( $user_id, 'mf-sexual_activity-updated', true );
            $medical_history_updated = get_user_meta( $user_id, 'mf-medical_history-updated', true );
            $valid_dob = valid_date_of_birth($user_id);

            if (!$personal_information_updated || !$sexual_activity_updated || !$medical_history_updated) {
                wp_redirect(home_url('my-account/medical-details/?redirected=true'));
                exit();
            } else if (!$valid_dob) {
                wp_redirect(home_url('my-account/medical-details/?redirected=dob'));
                exit();
            }
        }
	}
}
add_action('init', 'his_redirect_customers_medical_details', 1);
 
/* Start custom product variation fields */
function product_variation_html($loop, $variation_data, $variation) {
    $required_pre_purchases = get_post_meta($variation->ID, 'required_pre_purchases', true);
    if (!$required_pre_purchases) $required_pre_purchases = 0;

    woocommerce_wp_text_input([
        'id' => 'required_pre_purchases[' . $loop . ']',
        'type' => 'number',
        'class' => 'short',
        'label' => 'Required purchases before available',
        'value' => $required_pre_purchases,
        'wrapper_class' => 'form-row',
    ]);
}
add_action( 'woocommerce_variation_options_pricing', 'product_variation_html', 10, 3 );

function product_variation_save($variation_id, $i) {
    if (isset($_POST['required_pre_purchases'][$i])) {
        update_post_meta($variation_id, 'required_pre_purchases', esc_attr($_POST['required_pre_purchases'][$i]));
    }
}
add_action( 'woocommerce_save_product_variation', 'product_variation_save', 10, 2 );
/* End custom product variation fields */

// Change from email for Welcome email
function his_welcome_from_email($from_email, $email, $trigger) {
	if ( $trigger->get_email_id() ) {
		$from_email = 'drarora@hisclinic.com';
	}

	return $from_email;
}
add_filter('rp_wcec_from_email', 'his_welcome_from_email', 10, 3);

function his_welcome_from_name($from_name, $email, $trigger) {
	if ( $trigger->get_email_id() ) {
		$from_name = 'His Clinic - Dr. Arora';
	}

	return $from_name;
}
add_filter('rp_wcec_from_name', 'his_welcome_from_name', 10, 3);