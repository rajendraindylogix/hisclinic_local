<?php
function process_medical_form() {
    if (!$_POST) {
        return false;
    }

    $fields = [
        'first-name' => null,
        'last-name' => null,
        'email' => null,
        'date-of-birth' => null,
        'gender' => null,
        'symptoms-of-ed' => null, 
        'advised-not-to-use' => null,
        'do-you-get-angina' => null,
        'had-a-heart-attack' => null,
        'had-a-stroke-or-tia' => null,
        'taking-any-nitrate-medications' => null,
        'password' => null,
    ];

    $approving_fields = [
        'advised-not-to-use',
        'do-you-get-angina',
        'had-a-heart-attack',
        'had-a-stroke-or-tia',
        'taking-any-nitrate-medications',
    ];

    $valid = true;

    foreach ($fields as $k => $v) {
        if (!empty($_POST[$k])) {
            $fields[$k] = $_POST[$k];
        } else {
            $valid = false;
        }
    }

    if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $valid = false;
    }

    $success = false;
    $message = null;

    if ($valid) {
        $approved = 1;

        foreach ($approving_fields as $key) {
            if (strtolower($fields[$key]) == 'yes') {
                $approved = 0;
                break;
            }
        }

        // Checking user does not exist
        if (!get_user_by('email', $fields['email'])) {
            $user_id = wc_create_new_customer( $fields['email'], $fields['email'], $fields['password'] );
    
            if ($user_id) {                
                update_user_meta($user_id, 'first_name', $fields['first-name']);
                update_user_meta($user_id, 'last_name', $fields['last-name']);
                update_user_meta($user_id, 'approved', $approved);
                wp_set_auth_cookie($user_id);
                
                $medical_form = new MedicalForm($fields);
                $serialized_data = $medical_form->get_serialized_data();
                
                if (update_user_meta($user_id, 'medical-form', $serialized_data)) {
                    $success = true;

                    update_user_meta( $user_id, 'mf-personal_information-updated', time() );
					update_user_meta( $user_id, 'mf-sexual_activity-updated', time() );
					update_user_meta( $user_id, 'mf-medical_history-updated', time() );
                } else {
                    $message = 'There was a problem when saving data, please try again.';
                }

                if (!$approved) {
                    $args = [
                        'subject' => 'New customer that requires verification.',
                        'heading' => 'New customer checkup',
                        'content' => "
                            <p>Hi, there is a new customer that didn't pass the medical form. Details are below:</p>
                            <p>User #{$user_id}</p>
                            <p>{$fields['first-name']} {$fields['last-name']}, {$fields['email']}</p>
                        ",
                    ];
                    email_admin($args);
                    
                    $args = [
                        'name' => $fields['first-name'] . ' ' . $fields['last-name'],
	                    'email' => $fields['email'],
                    ];
                    email_customer($args);
                }
            } else {
                $message = 'There was a problem when creating the user, please try again.';
            }
        } else {
            $message = 'The email you entered is already registered. Please login.';
        }
    } else {
        $message = 'There was a problem with your form, please check the form and try again.';
    }

    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}
add_action( 'wp_ajax_process_medical_form', 'process_medical_form');
add_action( 'wp_ajax_nopriv_process_medical_form', 'process_medical_form');

/* Ajax call to fetch posts, allowing filter and pagination */
function fetch_posts() {
	if ($_POST) {
		$page = (!empty($_POST['page'])) ? $_POST['page'] : 1;
		$category = (!empty($_POST['category'])) ? $_POST['category'] : null; // slug

		$json = [];

		$args = [
			'paged' => $page,
			'tax_query' => [
				'AND',
			],
			'posts_per_page' => get_option('posts_per_page')
		];

		if ($category && $category != 'all') {
			$args['tax_query'][] = [
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => $category,
			];
		}

		if ($posts = get_posts($args)) {
			$success = true;

			ob_start();

            foreach ($posts as $p) {
                get_template_part_with_args('template-parts/post-item', ['p' => $p]);
            }

			$html = ob_get_contents();
			ob_end_clean();

			$json['html'] = $html;
			$json['success'] = true;

			$args['paged']++;
			$next = get_posts($args);

			$json['more'] = count($next);
		} else {
			$json['success'] = false;
		}

		echo json_encode($json);
		exit;
	}
}
add_action('wp_ajax_nopriv_fetch_posts', 'fetch_posts');
add_action('wp_ajax_fetch_posts', 'fetch_posts');

/* Ajax call to to process treatment change request */
function hc_dashboard_treatment_change() {
    
    if ( $_POST ) {
        
        if ( isset( $_POST['action'] ) && 'hc_dashboard_treatment_change' === $_POST['action'] ) {

            if ( empty( $_POST['treatment_change_reson'] ) ) {
                    
                    wp_send_json_error( array( 'message' => __( 'Request message is required', 'woocommerce' ) ) );
                
                die;
            }

            $requester_id = $_POST['requested_by'];
            $user         = get_user_by( 'ID', $requester_id );

            $treatment_change_requests = ! empty( get_user_meta( $requester_id, 'treatment_change_requests', true ) ) ? get_user_meta( $requester_id, 'treatment_change_requests', true ) : array();

            $timestamp = time();

            $treatment_change_requests[$timestamp] = $_POST;

            update_user_meta( $requester_id, 'approved', false );
            update_user_meta( $requester_id, 'active_change_request', true );
            update_user_meta( $requester_id, 'treatment_change_requests', $treatment_change_requests );

            $email_data = [
                'heading' => 'Hello His Clinic Admin',
                'intro' => "{$user->display_name} has requested for treatment change. Please find the request details below:",
                'content' => '
                    <table>
                        <tr>
                            <th>Field</th>
                            <th>Details</th>                    
                        </tr>
                        <tr>
                            <td>Requested By</td>
                            <td>'. $user->display_name .'</td>    
                        </tr>
                        <tr>
                            <td>Currently Prescribed Product</td>
                            <td>'. $_POST['current_treatment'] .'</td>
                        </tr>
                        <tr>
                            <td>Requested product change</td>
                            <td>'. get_the_title( $_POST['treatment_change_to'] ) .'</td>
                        </tr>
                        <tr>
                            <td>Reason for change request</td>
                            <td>'. $_POST['treatment_change_reson'] .'</td>
                        </tr>
                        <tr>
                            <td colspan="2"><a target="_blank" href="'. admin_url( '/users.php?page=medical-forms-new&user_id=' . $requester_id ) . '#treatment-change-request">View in Dashboard</a></td>
                        </tr>
                    </table>
                ',
            ];

            $to = get_option('admin_email');
            $subject = __( 'Medical Treatment Change Request', 'woocommerce' );
    
            if (send_email_template($to, $subject, $email_data)) {
                
                wp_send_json_success( array(
                    'message' => __( 'Your message has been sent', 'woocommerce' ),
                ) );
            
            } else {
                
                wp_send_json_error( array(
                    'message' => __( 'Your message could not be sent', 'woocommerce' ),
                ) );
            
            }
        }
        die;
	}
}
add_action('wp_ajax_nopriv_hc_dashboard_treatment_change', 'hc_dashboard_treatment_change');
add_action('wp_ajax_hc_dashboard_treatment_change', 'hc_dashboard_treatment_change');

add_action('wp_ajax_admin_handle_treatment_change', 'hc_admin_handle_treatment_change');

/**
 * Handle admin treatment chnage request.
 *
 * @return void
 */
function hc_admin_handle_treatment_change() {

    if ( isset( $_POST['action'] ) && $_POST['action'] === 'admin_handle_treatment_change' ) :

        if ( ! isset( $_POST['user_id'] ) || empty( $_POST['user_id'] ) )
            wp_send_json_error( array( 'message' => __( 'Invalid user', 'woocommerce' ) ) );

        $change_requests = get_user_meta( $_POST['user_id'], 'treatment_change_requests', true );

        $user_info  = get_userdata( $_POST['user_id'] );
        
        $user_email = $user_info->user_email;

        //   CHANGE THE BELOW VARIABLES TO YOUR NEEDS
        $to      = $user_email;
        $subject = __( 'Medical Treatment Change Request', 'woocommerce' );
        
        if ( isset( $_POST['approve'] ) && 'true' === $_POST['approve'] && isset( $_POST['treatment_change_to'] ) && ! empty(  $_POST['treatment_change_to']  ) ) :

            $new_suggested_url = get_permalink( $_POST['treatment_change_to'] );

            // Change prescription
            $suggested_product = update_user_meta( $_POST['user_id'], 'suggested_product', $new_suggested_url );
            
            // Update status
            if ( ! empty( $change_requests ) && isset( $change_requests[$_POST['request_key']] ) ) :

                $change_requests[$_POST['request_key']]['approved'] = true;

            endif;

            $args = array(
                'message' => '<span class="approved">Approved</span>',
                'key'     => $_POST['request_key'],
            );

            $message = '
                <p>Your request for change of treatment has been approved. Your prescription has been updated.</p>
                <p>Please check your account details page for more information.</p>
                <p><a href="' . get_permalink( get_option('woocommerce_myaccount_page_id') ).'">View Account dashboard</a></p>
            ';
            
        else:

            // Update status
            if ( ! empty( $change_requests ) && isset( $change_requests[$_POST['request_key']] ) ) :

                $change_requests[$_POST['request_key']]['approved'] = false;

            endif;

            $args = array(
                'message' => '<span class="rejected">Rejected</span>',
                'key'     => $_POST['request_key'],
            );

            $message = '
                <p>Sorry, your request for change of treatment has been rejected. Your prescription status has been reverted to your current medicine.</p>
                <p>If you require further assistance, please contact us with the link below:</p>
                <p><a href="' . get_the_permalink( hisclinic_get_page_id_by_page_name( 'contact-us' ) ) .'">Contact Us</a></p>
            ';

        endif;
        
        $email_content = [
            'heading' => 'Hello ' . $user_info->display_name,
            'content' => $message,
        ];

        send_email_template($to, $subject, $email_content);

        update_user_meta( $_POST['user_id'], 'treatment_change_requests', $change_requests );
        update_user_meta( $_POST['user_id'], 'active_change_request', false );
        // Approve User
        update_user_meta( $_POST['user_id'], 'approved',  true );

        // var_dump( $new_suggested_url, $_POST['treatment_change_to'] ); die;

        wp_send_json_success( $args );

    endif;
    
    die;

}

add_action('wp_ajax_nopriv_hc_dr_support_send_mesage', 'hc_dr_support_send_mesage');
add_action('wp_ajax_hc_dr_support_send_mesage', 'hc_dr_support_send_mesage');

function hc_dr_support_send_mesage() {

    // print_r( $_POST );

    if ( isset( $_POST['action'] ) && 'hc_dr_support_send_mesage' === $_POST['action'] ) :

        if ( isset( $_POST['user_id'] ) && !empty( $_POST['user_id'] ) ) :
            $user_id = $_POST['user_id'];

            if ( isset( $_POST['dr_support_chat'] ) && ! empty( $_POST['dr_support_chat'] ) ) :

                $saved_messages = ! empty( get_user_meta( $user_id, 'hc_dr_support_messages', true ) ) ? get_user_meta( $user_id, 'hc_dr_support_messages', true ) : array();

                $data_to_save = $_POST;

                $data_to_save['avatar'] = get_user_avatar_url(get_user_avatar($user_id));

                $time = time();
                $saved_messages[$time] = $data_to_save;
                
                $updated = update_user_meta( $user_id, 'hc_dr_support_messages', $saved_messages );

                if ( $updated ) {
                    update_user_meta($user_id, MF_UNREAD_QUERY_KEY, true);

                    // Email
                    $is_current_user = get_current_user_id() == (int) $user_id;

                    if ($is_current_user) { // Email admin
                        $url = admin_url('users.php?page=medical-forms-new&user_id=' . $user_id);
                        $to = get_option('admin_email');
                        $subject = 'New message from a patient';
                        $email_content = [
                            'heading' => 'Hello Doctor',
                            'intro' => 'You have a new message from a patient.',
                            'content' => 'Please <a href="' . $url . '">click here</a> to see the latest message.',
                        ];
                    } else { // Email user
                        $user_info  = get_userdata($user_id);
                        $to = $user_info->user_email;
                        $subject = 'You have a new message from your doctor';
                        $url = home_url('my-account/help');
                        $email_content = [
                            'heading' => 'Dear ' . get_first_name($user_id),
                            'intro' => 'You have a new messsage from the Dr.',
                            'content' => '
                                <p>Please click <a href="' . $url . '">click here</a> to view and reply to it.</p>
                                <p>
                                    Kind Regards, <br>
                                    His Clinic Customer Service
                                </p>
                            ',
                        ];
                    }

                    send_email_template($to, $subject, $email_content);

                    $avatar = get_user_avatar_url(get_user_avatar($user_id));

                    $args = array(
                        'message' => $_POST['dr_support_chat'],
                        'avatar'  => $avatar,
                    );

                    wp_send_json_success( $args );

                } else {

                    $args = array(
                        'message' => __( 'Failed to send message', 'woocommece' ),
                    );

                    wp_send_json_error( $args );

                }

            else :

                $args = array(
                    'message' => __( 'Message cannot be empty', 'woocommece' ),
                );

                wp_send_json_error( $args );


            endif;


        endif;

    endif;

    die;
}

function hc_queries_seen() {
    if ($user_id = $_POST['user_id']) {
        update_user_meta($user_id, MF_UNREAD_QUERY_KEY, false);
    }

    exit;
}
add_action('wp_ajax_nopriv_hc_queries_seen', 'hc_queries_seen');
add_action('wp_ajax_hc_queries_seen', 'hc_queries_seen');

add_action('wp_ajax_nopriv_hc_admin_update_suggested_prod', 'hc_admin_update_suggested_prod');
add_action('wp_ajax_hc_admin_update_suggested_prod', 'hc_admin_update_suggested_prod');

function hc_admin_update_suggested_prod() {

    if ( isset( $_POST['action'] ) && 'hc_admin_update_suggested_prod' === $_POST['action'] ) {

        if( isset( $_POST['user_id'] ) &&  ! empty( $_POST['user_id'] )  ) {

            if ( isset( $_POST['suggested_product_usr'] ) && ! empty( $_POST['suggested_product_usr'] ) ) :

                $update_suggested = update_user_meta( $_POST['user_id'], 'suggested_product', $_POST['suggested_product_usr'] );

                $approved = update_user_meta( $_POST['user_id'], 'approved', true );

                wp_send_json_success();

            else:

                wp_send_json_error( $data = array( 'message' => __( 'emptyv value', 'woocommerce' ) ) );

            endif;

        }

    }

    die;

}

add_action('wp_ajax_nopriv_hc_merge_sync_old_mf_users', 'hc_merge_sync_old_mf_users');
add_action('wp_ajax_hc_merge_sync_old_mf_users', 'hc_merge_sync_old_mf_users');

// Merge Old medical form users data to new medical form details.
/**
 * Merge sync old Medical Forms users function.
 *
 * @return void
 */
function hc_merge_sync_old_mf_users() {

    $args = [
        'role' => 'customer',
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => MF_KEY_NEW,
                'compare' => 'EXISTS',
            ]
        ],
        'orderby' => 'ID',
        'order' => 'DESC',
    ];

    $users = get_users( $args );

    // print_r( $users );

    if ( ! empty( $users ) && is_array( $users ) ) :

        $new_medical_form_defaults = [];

        foreach( $users as $key => $user ) :

            $old_form_data = get_user_meta( $user->ID, MF_KEY_NEW, true );

            if( empty( $old_form_data ) )
                continue;

            $old_form_data = json_decode( maybe_unserialize( $old_form_data ) );
            $old_form_data = his_clinic_object_to_array( $old_form_data );

            // print_r( $old_form_data ); die;


            $new_medical_form_defaults = [

                'personal_information' => [
                    'gender' => array(
                        'answer'   => $old_form_data['gender'], 
                        'question' => '',
                    ),
                    'date_of_birth' => array(
                        'answer'   => $old_form_data['date'], 
                        'question' => '',
                    ),
                    'height' => array(
                        'answer'   => $old_form_data['height'], 
                        'question' => '',
                    ),
                    'height_no_info' => array(
                        'answer'   => $old_form_data['heightchk'], 
                        'question' => '',
                    ),
                    'weight' => array(
                        'answer'   => $old_form_data['weight'], 
                        'question' => '',
                    ),
                    'weight_no_info' => array(
                        'answer'   => $old_form_data['weightchk'], 
                        'question' => '',
                    ),
                    'diet' => array(
                        'answer'   => $old_form_data['diet'], 
                        'question' => '',
                    ),
                ],

                'sexual_activity' => [
                    'uses' => array(
                        'answer'   => $old_form_data['uses'], 
                        'question' => '',
                    ),
                    'erection' => array(
                        'answer'   => $old_form_data['erection'], 
                        'question' => '',
                    ),
                ],

                'medical_history' => [
                    'herbs_description' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'heart_disease' => array(
                        'answer'   => $old_form_data['heart_disease'], 
                        'question' => '',
                    ),
                    'lightheadedness' => array(
                        'answer'   => $old_form_data['lightheadedness'], 
                        'question' => '',
                    ),
                    'medical_condition' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'medical_condition_description' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'medical_history' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'medical_history_desctiption' => array(
                        'answer'   => '',
                        'question' => '',
                    ),
                    'allergies' => array(
                        'answer'   => $old_form_data['allergies'], 
                        'question' => '',
                    ),
                    'allergies_description' => array(
                        'answer'   => '',
                        'question' => '',
                    ),
                    'nitrate' => array(
                        'answer'   => $old_form_data['nitrate'], 
                        'question' => '',
                    ),
                    'herbs' => array(
                        'answer'   => $old_form_data['herbs'], 
                        'question' => '',
                    ),
                    'blood_pressure_test' => array(
                        'answer'   => $old_form_data['blood_pressure_test'], 
                        'question' => '',
                    ),
                    'blood_pressure_diagnosis' => array(
                        'answer'   => $old_form_data['blood_pressure_diagnosis'], 
                        'question' => '',
                    ),
                    'cardiovascular_symptoms' => array(
                        'answer'   => $old_form_data['cardiovascular_symptoms'], 
                        'question' => '',
                    ),
                    'heart_attack_past' => array(
                        'answer'   => $old_form_data['heart_attack_past'], 
                        'question' => '',
                    ),
                    'stroke_TIA' => array(
                        'answer'   => $old_form_data['stroke_TIA'], 
                        'question' => '',
                    ),
                    'conditions_1' => array(
                        'answer'   => $old_form_data['conditions_1'], 
                        'question' => '',
                    ),
                    'conditions_2' => array(
                        'answer'   => $old_form_data['conditions_2'], 
                        'question' => '',
                    ),
                ],

            ];

            $new_medical_array['medical_form_details'] = $new_medical_form_defaults;
            
            // Save data to account
            save_medical_form_data($user->ID, $new_medical_array);

        endforeach;

        wp_send_json_success();

    endif;

    die;

}

add_action('wp_ajax_nopriv_hc_merge_sync_gravity_forms_mf_users', 'hc_merge_sync_gravity_forms_mf_users');
add_action('wp_ajax_hc_merge_sync_gravity_forms_mf_users', 'hc_merge_sync_gravity_forms_mf_users');

function hc_merge_sync_gravity_forms_mf_users() {

    $leads = RGFormsModel::get_leads( $form_id = 1 );

    // print_r( $leads );

    if ( ! empty( $leads ) && is_array( $leads ) ) :

        foreach( $leads as $l_k => $lead ) {

            $user_id = isset( $lead['created_by'] ) ? $lead['created_by'] : '';

            if( empty( $user_id ) )
                continue;

            // Default Fields.
            $fields = [
                'personal_information' => [
                    'gender' => [
                        'answer' => null,
                        'question' => '',
                    ],
                    'date' => [
                        'answer' => $lead['40'],
                        'question' => '',
                    ],
                    'height' => [
                        'answer' => $lead['1'],
                        'question' => '',
                    ],
                    'heightchk' => [
                        'answer' => null,
                        'question' => '',
                    ],
                    'weightchk' => [
                        'answer' => null,
                        'question' => '',
                    ],
                    'weight' => [
                        'answer' => $lead['2'],
                        'question' => '',
                    ],
                    'diet' => [
                        'answer' => $lead['3'],
                        'question' => '',
                    ],
                ],

                'sexual_activity' => [
                    'uses' => array(
                        'answer'   => $lead['4'], 
                        'question' => '',
                    ),
                    'erection' => array(
                        'answer'   => $lead['5'], 
                        'question' => '',
                    ),
                ],

                'medical_history' => [
                    'herbs_description' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'prescription' => [
                        'answer' => $lead['6'],
                        'question' => '',
                    ],
                    'heart_disease' => array(
                        'answer'   => $lead['7'], 
                        'question' => '',
                    ),
                    'lightheadedness' => array(
                        'answer'   => isset( $lead['20'] ) ? $lead['20'] : null, 
                        'question' => '',
                    ),
                    'previous_use_sildenafil' => array(
                        'answer' => $lead['11.1'],
                        'question' => '',
                    ),
                    'previous_use_sildenafil' => array( 
                        'answer' => $lead['11.1'],
                        'question' => '',
                    ),
                    'sildenafil_effective' => array(
                        'answer' => isset( $lead['12'] ) ? $lead['12'] : null,
                        'question' => '',
                    ),
                    'previous_use_cialis' => array(
                        'answer' => null,
                        'question' => '',
                    ),
                    'cialis_effective' => array(
                        'answer' => isset( $lead['13'] ) ? $lead['13'] : null,
                        'question' => '',
                    ),
                    'previous_use_cialis_daily'=> array(
                        'answer' => null,
                        'question' => '',
                    ),
                    'daily_cialis_effective' => array(
                        'answer' => isset( $lead['13'] ) ? $lead['13'] : null,
                        'question' => '',
                    ),
                    'medical_condition' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'medical_condition_description' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'medical_history' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'medical_history_desctiption' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'allergies' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'allergies_description' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'nitrate' => array(
                        'answer'   => $old_form_data['nitrate'], 
                        'question' => '',
                    ),
                    'herbs' => array(
                        'answer'   => '', 
                        'question' => '',
                    ),
                    'blood_pressure_test' => array(
                        'answer'   => $old_form_data['blood_pressure_test'], 
                        'question' => '',
                    ),
                    'blood_pressure_diagnosis' => array(
                        'answer'   => $old_form_data['blood_pressure_diagnosis'], 
                        'question' => '',
                    ),
                    'cardiovascular_symptoms' => array(
                        'answer'   => $old_form_data['cardiovascular_symptoms'], 
                        'question' => '',
                    ),
                    'heart_attack_past' => array(
                        'answer'   => $old_form_data['heart_attack_past'], 
                        'question' => '',
                    ),
                    'stroke_TIA' => array(
                        'answer'   => $old_form_data['stroke_TIA'], 
                        'question' => '',
                    ),
                    'conditions_1' => array(
                        'answer'   => $old_form_data['conditions_1'], 
                        'question' => '',
                    ),
                    'conditions_2' => array(
                        'answer'   => $old_form_data['conditions_2'], 
                        'question' => '',
                    ),
                ],


                'previous_use_sildenafil' => $lead['11.1'],
                'sildenafil_effective' => isset( $lead['12'] ) ? $lead['12'] : null,
                'previous_use_cialis' => null,
                'cialis_effective' => isset( $lead['13'] ) ? $lead['13'] : null,
                'previous_use_cialis_daily'=>null,
                'daily_cialis_effective'=>isset( $lead['13'] ) ? $lead['13'] : null,

                'surgeries' => isset( $lead['15'] ) ? $lead['15'] : null,
                'nitrate' => isset( $lead['17'] ) ? $lead['17'] : null,
                'blood_pressure_test' => isset( $lead['17'] ) ? $lead['17'] : null,
                'blood_pressure_diagnosis' => isset( $lead['19'] ) ? $lead['19'] : null,
                'lightheadedness' => isset( $lead['20'] ) ? $lead['20'] : null,
                'cardiovascular_symptoms' => isset( $lead['21'] ) ? $lead['21'] : null,
                'heart_attack_past' =>  isset( $lead['22'] ) ? $lead['22'] : null,
                'stroke_TIA' =>  isset( $lead['23'] ) ? $lead['23'] : null,
                'conditions_1' =>  isset( $lead['24'] ) ? $lead['24'] : null,
                'conditions_2' =>  isset( $lead['25'] ) ? $lead['25'] : null,
                'form4_description' => 'Medical Conditions: '. $lead['17'] . ' Nitrate medicines: ' . $lead['43'],
                'form1_fullname' => null,
                'form1_email' => null,
                'form1_password' => null,
                'form2_fullname' => null,
                'form2_email' => null,
                'form2_password' => null,
                'form3_fullname' => null,
                'form3_email' => null,
                'form3_password' => null,
                'heart_redirection' => null,
                'sildenafil_redirection_link' => null,
                'cialis_redirection_link' => null,
            ];

            $new_medical_encoded_array = json_encode( $fields );

            update_user_meta( $user_id, MF_KEY_NEW, $new_medical_encoded_array );

            $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => 'customer' ) );

        }

        wp_send_json_success();

    endif;

    die;

}

add_action('wp_ajax_nopriv_hc_merge_sync_order_allergy_details', 'hc_merge_sync_order_allergy_details');
add_action('wp_ajax_hc_merge_sync_order_allergy_details', 'hc_merge_sync_order_allergy_details');

// Merge Old medical form users data to new medical form details.
/**
 * Merge sync old Medical Forms users function.
 *
 * @return void
 */
function hc_merge_sync_order_allergy_details() {

    $today = getdate();

    $query = new WC_Order_Query( array(
        'limit'   => -1,
        'orderby' => 'date',
        'date_query' => array(
            array(
                'after'     => 'May 1st, 2019',
                'before'    => array(
                    'year'  => $today['year'],
                    'month' => $today['mon'],
                    'day'   => $today['mday'],
                ),
                'inclusive' => true,
            ),
        ),
        'order'   => 'DESC',
        'return'  => 'ids',
    ) );
    $orders = $query->get_orders();

    if ( ! empty( $orders ) && is_array( $orders ) ) :

        foreach ($orders as $key => $order_id) {

            $order_user_id = get_post_meta( $order_id, '_customer_user', true );

            $saved_mf_user_data       = get_user_meta( $order_user_id, MF_KEY_NEW, true );

            $saved_mf_user_data_array = json_decode( maybe_unserialize( $saved_mf_user_data ) );


            if ( isset( $saved_mf_user_data_array['form4_description'] ) ) {
                
                $allergies_check   = get_post_meta( $order_id, 'allergies_check', true );
                $allergies_details = get_post_meta( $order_id, 'allergies_details', true );

                $saved_mf_user_data_array['form4_description'] .= 'Severe allergies to food or medication?: '.$allergies_check;
                
                $saved_mf_user_data_array['form4_description'] .= ' Allergies details: '.$allergies_details;
            }

            $new_updated_mf_data = json_encode( $saved_mf_user_data_array );

            update_post_meta( $order_user_id, 'MF_KEY_NEW', $new_updated_mf_data );

        }

        wp_send_json_success();

    endif;

    die;

}