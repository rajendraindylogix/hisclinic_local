<?php

// Template Name: Manual Fix - Jaywing

//jaywing_manual_fix_backup_old_data();
//jaywing_manual_fix_copy_new_data();
//jaywing_manual_fix_change_to_customer();
//jaywing_manual_fix_sync_old_medical_form();
//jaywing_manual_fix_sync_gravity_form_1();
//jaywing_manual_fix_sync_order_allergy_details();
//jaywing_manual_fix_sync_gravity_form_2();

//jaywing_manual_fix_sync_to_latest();

//jaywing_manual_fix_missing_dob();

// copy medical-form to medical-form-old
function jaywing_manual_fix_backup_old_data() {
    $args = [
        'role__in' => array('customer', 'subscriber'),
    ];

    $users = get_users( $args );

/*
    print_r(count($users));
    exit();
*/

    if ( ! empty( $users ) && is_array( $users ) ) :
		foreach( $users as $key => $user ) :
            $medical_form_data = get_user_meta($user->ID, 'medical-form', true);

			if ( $medical_form_data == '' ) {
				$medical_form_data = '{}';
			}

            if ( update_user_meta($user->ID, 'medical-form-old', $medical_form_data ) ) {
	            echo $key . ': true<br>';
            } else {
	            echo $key . ': false<br>';
            }
        endforeach;
    endif;
}

// copy medical-form-new to medical-form
function jaywing_manual_fix_copy_new_data() {
    $args = [
        'role__in' => array('customer', 'subscriber'),
    ];

    $users = get_users( $args );

/*
    print_r(count($users));
    exit();
*/

    if ( ! empty( $users ) && is_array( $users ) ) :
		foreach( $users as $key => $user ) :
            $medical_form_data = get_user_meta($user->ID, 'medical-form-new', true);

//			if ( $medical_form_data != '' ) {
	            if ( update_user_meta($user->ID, 'medical-form', $medical_form_data ) ) {
		            echo $key . ': true<br>';
	            } else {
		            echo $key . ': false<br>';
	            }
//			}
        endforeach;
    endif;
}

// change subscribers to customers
function jaywing_manual_fix_change_to_customer() {
	$args = [
        'role__in' => array('subscriber'),
    ];

    $users = get_users( $args );

/*
    print_r(count($users));
    exit();
*/

    if ( ! empty( $users ) && is_array( $users ) ) :
		foreach( $users as $key => $user ) :
            $user_id = wp_update_user( array( 'ID' => $user->ID, 'role' => 'customer' ) );
            echo $user_id . '<br>';
		endforeach;
	endif;
}

// sync data from old medical form
function jaywing_manual_fix_sync_old_medical_form() {
    $args = [
        'role__in' => array('customer', 'subscriber'),
    ];

    $users = get_users( $args );

/*
    print_r(count($users));
    exit();
*/

    if ( ! empty( $users ) && is_array( $users ) ) :
        foreach( $users as $key => $user ) :
        	$new_form_data = get_user_meta( $user->ID, 'medical-form', true );
            $old_form_data = get_user_meta( $user->ID, 'medical-form-old', true );

            if( empty( $old_form_data ) )
                continue;

			$new_form_data = json_decode( maybe_unserialize( $new_form_data ) );
            $new_form_data = his_clinic_object_to_array( $new_form_data );

            $old_form_data = json_decode( maybe_unserialize( $old_form_data ) );
            $old_form_data = his_clinic_object_to_array( $old_form_data );

/*
			echo '<pre>';
			print_r($new_form_data);
			print_r($old_form_data);
			echo '</pre>';
			exit();
*/

			if ( (isset($old_form_data['date-of-birth']) && !empty($old_form_data['date-of-birth'])) && ((isset($new_form_data['date']) && empty($new_form_data['date'])) || !isset($new_form_data['date'])) ) {
				$new_form_data['date'] = $old_form_data['date-of-birth'];
			}

			if ( (isset($old_form_data['gender']) && !empty($old_form_data['gender'])) && ((isset($new_form_data['gender']) && empty($new_form_data['gender'])) || !isset($new_form_data['gender'])) ) {
				$new_form_data['gender'] = $old_form_data['gender'];
			}

			if ( (isset($old_form_data['had-a-heart-attack']) && !empty($old_form_data['had-a-heart-attack'])) && ((isset($new_form_data['heart_attack_past']) && empty($new_form_data['heart_attack_past'])) || !isset($new_form_data['heart_attack_past'])) ) {
				$new_form_data['heart_attack_past'] = 'No' === $old_form_data['had-a-heart-attack'] ? 'None' : 'Yes';
			}

			if ( (isset($old_form_data['had-a-stroke-or-tia']) && !empty($old_form_data['had-a-stroke-or-tia'])) && ((isset($new_form_data['stroke_TIA']) && empty($new_form_data['stroke_TIA'])) || !isset($new_form_data['stroke_TIA'])) ) {
				$new_form_data['stroke_TIA'] = 'No' === $old_form_data['had-a-stroke-or-tia'] ? 'None' : 'Yes';
			}

			if ( (isset($old_form_data['taking-any-nitrate-medications']) && !empty($old_form_data['taking-any-nitrate-medications'])) && ((isset($new_form_data['nitrate']) && empty($new_form_data['nitrate'])) || !isset($new_form_data['nitrate'])) ) {
				$new_form_data['nitrate'] = 'No' === $old_form_data['taking-any-nitrate-medications'] ? 'None' : 'Yes';
			}

/*
			if ( (isset($old_form_data['symptoms-of-ed']) && !empty($old_form_data['symptoms-of-ed'])) && ((isset($new_form_data['symptoms-of-ed']) && empty($new_form_data['symptoms-of-ed'])) || !isset($new_form_data['symptoms-of-ed'])) ) {
				$new_form_data['symptoms-of-ed'] = $old_form_data['symptoms-of-ed'];
			}

			if ( (isset($old_form_data['advised-not-to-use']) && !empty($old_form_data['advised-not-to-use'])) && ((isset($new_form_data['advised-not-to-use']) && empty($new_form_data['advised-not-to-use'])) || !isset($new_form_data['advised-not-to-use'])) ) {
				$new_form_data['advised-not-to-use'] = $old_form_data['advised-not-to-use'];
			}

			if ( (isset($old_form_data['do-you-get-angina']) && !empty($old_form_data['do-you-get-angina'])) && ((isset($new_form_data['do-you-get-angina']) && empty($new_form_data['do-you-get-angina'])) || !isset($new_form_data['do-you-get-angina'])) ) {
				$new_form_data['do-you-get-angina'] = $old_form_data['do-you-get-angina'];
			}
*/

            $new_form_data = json_encode($new_form_data);

/*
			echo '<pre>';
			print_r($new_form_data);
			echo '</pre>';
			exit();
*/

            if ( update_user_meta($user->ID, 'medical-form', $new_form_data) ) {
	            echo $key . ': true<br>';
            } else {
	            echo $key . ': false<br>';
            }
        endforeach;
    endif;
}

// sync data from GF ID 1
function jaywing_manual_fix_sync_gravity_form_1() {
    $leads = RGFormsModel::get_leads(1, 0, 'ASC', '', 0, 9999);
/*
    print_r($leads);
    exit();
*/

    if ( ! empty( $leads ) && is_array( $leads ) ) :

        foreach( $leads as $l_k => $lead ) {
            $lead_user_id = isset( $lead['created_by'] ) ? $lead['created_by'] : '';

            if( empty($lead_user_id) && $user = get_user_by('email', $lead['1']) ) {
				$lead_user_id = $user->ID;
            }

			if ( empty($lead_user_id) )
				continue;

			$form_data = get_user_meta( $lead_user_id, 'medical-form', true );

            if( empty( $form_data ) ) {
				$form_data = [
	                'gender' => null,
	                'date' => $lead['40'],
	                'height' => $lead['1'],
	                'heightchk' => null,
	                'weightchk' => null,
	                'weight' => $lead['2'],
	                'diet' => $lead['3'],
	                'uses' => $lead['4'],
	                'erection' => $lead['5'],
	                'prescription' => $lead['6'],
	                'heart_disease' => $lead['7'],
	                'previous_use_sildenafil' => $lead['11.1'],
	                'sildenafil_effective' => isset( $lead['12'] ) ? $lead['12'] : null,
	                'previous_use_cialis' => $lead['11.2'],
	                'cialis_effective' => isset( $lead['13'] ) ? $lead['13'] : null,
	                'previous_use_cialis_daily' => $lead['11.3'],
	                'daily_cialis_effective'=>isset( $lead['14'] ) ? $lead['14'] : null,
	                'surgeries' => null,
	                'nitrate' => isset( $lead['17'] ) ? $lead['17'] : null,
	                'blood_pressure_test' => isset( $lead['18'] ) ? $lead['18'] : null,
	                'blood_pressure_diagnosis' => isset( $lead['19'] ) ? $lead['19'] : null,
	                'lightheadedness' => isset( $lead['20'] ) ? $lead['20'] : null,
	                'cardiovascular_symptoms' => isset( $lead['21'] ) ? $lead['21'] : null,
	                'heart_attack_past' =>  isset( $lead['22'] ) ? $lead['22'] : null,
	                'stroke_TIA' =>  isset( $lead['23'] ) ? $lead['23'] : null,
	                'conditions_1' =>  isset( $lead['24'] ) ? $lead['24'] : null,
	                'conditions_2' =>  isset( $lead['25'] ) ? $lead['25'] : null,
	                'form4_description' => 'Medications: ' . $lead['43'],
	                'medical_condition' => isset( $lead['15'] ) ? $lead['15'] : null,
					'medical_condition_description' => isset( $lead['16'] ) ? $lead['16'] : null,
					'medical_history' => isset( $lead['44'] ) ? $lead['44'] : null,
					'medical_history_desctiption' => isset( $lead['45'] ) ? $lead['45'] : null,
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
			} else {
				$form_data = json_decode( maybe_unserialize( $form_data ) );
				$form_data = his_clinic_object_to_array( $form_data );

/*
				echo '<pre>';
				print_r($form_data);
				echo '</pre>';
*/

				$form_data['date'] = $lead['40'];
				$form_data['height'] = $lead['1'];
				$form_data['weight'] = $lead['2'];
				$form_data['diet'] = $lead['3'];
				$form_data['uses'] = $lead['4'];
				$form_data['erection'] = $lead['5'];
				$form_data['prescription'] = $lead['6'];
				$form_data['heart_disease'] = $lead['7'];
				$form_data['previous_use_sildenafil'] = $lead['11.1'];
				$form_data['sildenafil_effective'] = isset( $lead['12'] ) ? $lead['12'] : null;
				$form_data['previous_use_cialis'] = $lead['11.2'];
				$form_data['cialis_effective'] = isset( $lead['13'] ) ? $lead['13'] : null;
				$form_data['previous_use_cialis_daily'] = $lead['11.3'];
				$form_data['daily_cialis_effective'] = isset( $lead['14'] ) ? $lead['14'] : null;
				$form_data['nitrate'] = isset( $lead['17'] ) ? $lead['17'] : null;
				$form_data['blood_pressure_test'] = isset( $lead['18'] ) ? $lead['18'] : null;
				$form_data['blood_pressure_diagnosis'] = isset( $lead['19'] ) ? $lead['19'] : null;
				$form_data['lightheadedness'] = isset( $lead['20'] ) ? $lead['20'] : null;
				$form_data['cardiovascular_symptoms'] = isset( $lead['21'] ) ? $lead['21'] : null;
				$form_data['heart_attack_past'] =  isset( $lead['22'] ) ? $lead['22'] : null;
				$form_data['stroke_TIA'] =  isset( $lead['23'] ) ? $lead['23'] : null;
				$form_data['conditions_1'] =  isset( $lead['24'] ) ? $lead['24'] : null;
				$form_data['conditions_2'] =  isset( $lead['25'] ) ? $lead['25'] : null;
				$form_data['form4_description'] = 'Medications: ' . $lead['43'];
				$form_data['medical_condition'] = isset( $lead['15'] ) ? $lead['15'] : null;
				$form_data['medical_condition_description'] = isset( $lead['16'] ) ? $lead['16'] : null;
				$form_data['medical_history'] = isset( $lead['44'] ) ? $lead['44'] : null;
				$form_data['medical_history_desctiption'] = isset( $lead['45'] ) ? $lead['45'] : null;
	
				$form_data['heightchk'] = null;
				$form_data['weightchk'] = null;
				$form_data['form1_fullname'] = null;
				$form_data['form1_email'] = null;
				$form_data['form1_password'] = null;
				$form_data['form2_fullname'] = null;
				$form_data['form2_email'] = null;
				$form_data['form2_password'] = null;
				$form_data['form3_fullname'] = null;
				$form_data['form3_email'] = null;
				$form_data['form3_password'] = null;
				$form_data['heart_redirection'] = null;
				$form_data['sildenafil_redirection_link'] = null;
				$form_data['cialis_redirection_link'] = null;
			}

/*
			echo '<pre>';
			print_r($form_data);
			echo '</pre>';
			exit();
*/

			$form_data = json_encode($form_data);

            if ( update_user_meta($lead_user_id, 'medical-form', $form_data) ) {
	            echo $lead_user_id . ': true<br>';
            } else {
	            echo $lead_user_id . ': false<br>';
            }
        }
    endif;
}

// sync data from GF ID 2
function jaywing_manual_fix_sync_gravity_form_2() {
    $leads = RGFormsModel::get_leads(2, 0, 'ASC', '', 0, 9999);

    if ( ! empty( $leads ) && is_array( $leads ) ) :

        foreach( $leads as $l_k => $lead ) {
            $lead_user_id = isset( $lead['created_by'] ) ? $lead['created_by'] : '';

            if( empty($lead_user_id) && $user = get_user_by('email', $lead['1']) ) {
				$lead_user_id = $user->ID;
            }

			if ( empty($lead_user_id) )
				continue;

			$form_data = get_user_meta($lead_user_id, 'medical-form', true);

            if( !empty( $form_data ) ) {
				$form_data = json_decode( maybe_unserialize( $form_data ) );
				$form_data = his_clinic_object_to_array( $form_data );

/*
				echo $lead_user_id . ' ' . $lead['1'] . '<br>';
				echo '<pre>';
				print_r($lead);
				print_r($form_data);
				echo '</pre>';
*/

				if ( isset($lead['3']) && $lead['3'] != '' ) {
					$form_data['date'] = $lead['3'];
				}
				$form_data['allergies'] = $lead['7'];
				$form_data['allergies_description'] = $lead['8'];
				$form_data['form4_description'] = 'Medications: ' . $lead['6'];
				$form_data['medical_condition'] = isset( $lead['4'] ) ? $lead['4'] : null;
				$form_data['medical_condition_description'] = isset( $lead['5'] ) ? $lead['5'] : null;
				$form_data['medical_history'] = isset( $lead['9'] ) ? $lead['9'] : null;
				$form_data['medical_history_desctiption'] = isset( $lead['10'] ) ? $lead['10'] : null;

/*
				echo '<pre>';
				print_r($form_data);
				echo '</pre>';
				exit();
*/

				$form_data = json_encode($form_data);

	            if ( update_user_meta($lead_user_id, 'medical-form', $form_data) ) {
		            echo $lead_user_id . ': true<br>';
	            } else {
		            echo $lead_user_id . ': false<br>';
	            }
			}		
        }
    endif;
}

// sync allergy details from orders
function jaywing_manual_fix_sync_order_allergy_details() {
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
        'order'   => 'ASC',
        'return'  => 'ids',
    ) );
    $orders = $query->get_orders();

/*
	print_r($orders);
	exit();
*/

    if ( ! empty( $orders ) && is_array( $orders ) ) :

        foreach ($orders as $key => $order_id) {
			// Get an instance of the WC_Order object
            $order = wc_get_order( $order_id );

            // Get the user ID from WC_Order methods
            $order_user_id = $order->get_user_id(); // or $order->get_customer_id();

            // $order_user_id = get_post_meta( $order_id, '_customer_user', true );

            $form_data = get_user_meta($order_user_id, 'medical-form', true);
            $form_data = json_decode(maybe_unserialize($form_data));
            $form_data = his_clinic_object_to_array($form_data);

            $allergies_check = get_post_meta($order_id, 'allergies_check', true);
            $allergies_details = get_post_meta($order_id, 'allergies_details', true);

            if ($allergies_check != '') :
                $form_data['allergies'] = $allergies_check;
                $form_data['allergies_description'] = $allergies_details;

				$form_data = json_encode($form_data);

/*
				print_r($form_data);
				exit();
*/

				if ( update_user_meta($order_user_id, 'medical-form', $form_data) ) {
		            echo $order_user_id . ': true<br>';
				} else {
	            	echo $order_user_id . ': false<br>';
				}
			endif;
        }
    endif;
}

function jaywing_manual_fix_sync_to_latest() {
	$args = [
		'role__in' => array('customer', 'subscriber'),
		'orderby' => 'ID',
		'order' => 'DESC',
    ];

	$users = get_users( $args );

/*
    print_r(count($users));
    exit();
*/

    if ( ! empty( $users ) && is_array( $users ) ) :
        foreach( $users as $key => $user ) :
        	$new_form_data = [];
            $old_form_data = get_user_meta( $user->ID, 'medical-form', true );

            if( empty( $old_form_data ) )
                continue;

            $old_form_data = json_decode( maybe_unserialize( $old_form_data ) );
            $old_form_data = his_clinic_object_to_array( $old_form_data );

/*
			echo '<pre>';
			print_r($old_form_data);
			echo '</pre>';
*/

			$new_form_data = [
				'medical_form_details' => [
					'medical_history' => [
						'heart_disease' => [
							'answer' => isset($old_form_data['heart_disease']) ? $old_form_data['heart_disease'] : '',
							'question' => 'Do you have, or have you ever had, Heart Disease?',
						],
						'lightheadedness' => [
							'answer' => isset($old_form_data['lightheadedness']) ? $old_form_data['lightheadedness'] : '',
							'question' => 'Do you frequently experience lightheadedness?',
                        ],
						'medical_condition' => [
							'answer' => isset($old_form_data['medical_condition']) ? $old_form_data['medical_condition'] : '',
							'question' => 'Do you have any past or ongoing medical conditions?',
						],
						'medical_condition_description' => [
							'answer' => isset($old_form_data['medical_condition_description']) ? $old_form_data['medical_condition_description'] : '',
							'question' => 'Please provide as much detail as possible about your ongoing medical conditions:',
						],
						'medical_history' => [
							'answer' => isset($old_form_data['medical_history']) ? $old_form_data['medical_history'] : '',
							'question' => 'Have you had a history of hospital admission or surgeries?',
						],
						'medical_history_desctiption' => [
							'answer' => [
								['a'] => [
									'date' => '',
									'description' => isset($old_form_data['medical_history_desctiption']) ? $old_form_data['medical_history_desctiption'] : '',
								],
								['b'] => [
									'date' => '',
									'description' => isset($old_form_data['surgeries']) ? $old_form_data['surgeries'] : '',
								],
							],
							'question' => 'Please provide details about your history of hospital admission or surgery:',
						],
						'allergies' => [
							'answer' => isset($old_form_data['allergies']) ? $old_form_data['allergies'] : '',
							'question' => 'Do you have any allergies?',
						],
						'allergies_description' => [
							'answer' => isset($old_form_data['allergies_description']) ? $old_form_data['allergies_description'] : '',
							'question' => 'Please provide as much detail as possible about your allergies:',
						],
						'nitrate' => [
							'answer' => isset($old_form_data['nitrate']) ? $old_form_data['nitrate'] : '',
							'question' => 'Are you currently taking any Nitrate medications (GTN patch, Mononitrates, etc)?',
						],
						'herbs' => [
							'answer' => '',
							'question' => 'Are you taking any other medications, herbs or supplements?',
						],
						'herbs_description' => [
							'answer' => '',
							'question' => 'Please provide details of your medication, herbs or supplements:',
						],
						'blood_pressure_test' => [
                            'answer' => isset($old_form_data['blood_pressure_test']) ? $old_form_data['blood_pressure_test'] : '',
							'question' => 'You need to have your blood Pressure (BP) checked within the last 12 months to receive treatment.',
						],
						'blood_pressure_diagnosis' => [
							'answer' => isset($old_form_data['blood_pressure_diagnosis']) ? $old_form_data['blood_pressure_diagnosis'] : '',
							'question' => 'When your blood pressure was taken were you diagnosed with:',
						],
						'cardiovascular_symptoms' => [
							'answer' => isset($old_form_data['cardiovascular_symptoms']) ? $old_form_data['cardiovascular_symptoms'] : '',
							'question' => 'Do you have any of the following cardiovascular symptoms?',
						],
						'heart_attack_past' => [
							'answer' => isset($old_form_data['heart_attack_past']) ? $old_form_data['heart_attack_past'] : '',
							'question' => 'Have you had a heart attack in the last 6 months?',
						],
						'stroke_TIA' => [
							'answer' => isset($old_form_data['stroke_TIA']) ? $old_form_data['stroke_TIA'] : '',
							'question' => 'Have you ever had a stroke or TIA?',
						],
						'conditions_1' => [
							'answer' => isset($old_form_data['conditions_1']) ? $old_form_data['conditions_1'] : '',
							'question' => 'Do you have now, or have you ever had, any of the following conditions?',
						],
						'conditions_2' => [
							'answer' => isset($old_form_data['conditions_2']) ? $old_form_data['conditions_2'] : '',
							'question' => 'Do you have any of the following conditions?',
						],
					],
					'personal_information' => [
						'gender' => [
							'answer' => isset($old_form_data['gender']) ? $old_form_data['gender'] : '',
							'question' => 'What is your sex?',
						],
						'date_of_birth' => [
							'answer' => isset($old_form_data['date']) ? $old_form_data['date'] : '',
							'question' => 'What is your date of birth?',
						],
						'height' => [
							'answer' => isset($old_form_data['height']) ? $old_form_data['height'] : '',
							'question' => 'Height (Centimeters)',
						],
						'height_no_info' => [
							'answer' => isset($old_form_data['heightchk']) ? $old_form_data['heightchk'] : '',
							'question' => 'Height (Centimeters)',
						],
						'weight' => [
							'answer' => isset($old_form_data['weight']) ? $old_form_data['weight'] : '',
							'question' => 'Weight (Kilograms)',
						],
						'weight_no_info' => [
							'answer' => isset($old_form_data['weightchk']) ? $old_form_data['weightchk'] : '',
							'question' => 'Weight (Kilograms)',
						],
						'diet' => [
							'answer' => isset($old_form_data['diet']) ? $old_form_data['diet'] : '',
							'question' => 'What is your main diet?',
						],
					],
					'sexual_activity' => [
						'uses' => [
							'answer' => isset($old_form_data['uses']) ? $old_form_data['uses'] : '',
							'question' => 'If prescribed, how often do you anticipate using this treatment for sexual activity?',
						],
						'erection' => [
							'answer' => isset($old_form_data['erection']) ? $old_form_data['erection'] : '',
							'question' => 'Do you ever have a problem getting or maintaining an erection?',
						],
					],
					'recommended_prescription' => [
						'prescription' => [
							'answer' => isset($old_form_data['prescription']) ? $old_form_data['prescription'] : '',
							'question' => 'Have you ever been prescribed or approved by a doctor to take Sildenafil or Cialis?',
						],
						'previous_use_sildenafil' => [
							'answer' => isset($old_form_data['previous_use_sildenafil']) ? $old_form_data['previous_use_sildenafil'] : '',
							'question' => 'Have you previously used any of the following products?',
						],
						'previous_use_cialis' => [
							'answer' => isset($old_form_data['previous_use_cialis']) ? $old_form_data['previous_use_cialis'] : '',
							'question' => 'Have you previously used any of the following products?',
						],
						'previous_use_cialis_daily' => [
							'answer' => isset($old_form_data['previous_use_cialis_daily']) ? $old_form_data['previous_use_cialis_daily'] : '',
							'question' => 'Have you previously used any of the following products?',
						],
						'sildenafil_effective' => [
							'answer' => isset($old_form_data['sildenafil_effective']) ? $old_form_data['sildenafil_effective'] : '',
							'question' => 'Was Sildenafil effective?',
						],
						'cialis_effective' => [
							'answer' => isset($old_form_data['cialis_effective']) ? $old_form_data['cialis_effective'] : '',
							'question' => 'Was Cialis effective?',
						],
						'daily_cialis_effective' => [
							'answer' => isset($old_form_data['daily_cialis_effective']) ? $old_form_data['daily_cialis_effective'] : '',
							'question' => 'Was Daily Cialis effective?',
						],
					],
					'redirection_link' => [
						'sildenafil_redirection_link' => 'https://www.hisclinic.com/product/sildenafil/',
						'cialis_redirection_link' => 'https://www.hisclinic.com/product/cialis/',
						'daily_cialis_redirection_link' => 'https://www.hisclinic.com/product/daily-cialis/',
					],
					'additional_details' => [
						'answer' => isset($old_form_data['form4_description']) ? $old_form_data['form4_description'] : '',
						'question' => 'Your medical assessment needs further review',
					],
				],
				'mf_fullname' => '',
				'mf_email' => '',
				'mf_password' => '',
				'action' => 'medical_form_sync',
			];

/*
			echo '<pre>';
			print_r($new_form_data);
			echo '</pre>';
			exit();
*/

            $new_form_data = json_encode($new_form_data);

            if ( update_user_meta($user->ID, 'medicalform-new', $new_form_data) ) {
	            echo $key . ': true<br>';
            } else {
	            echo $key . ': false<br>';
            }
        endforeach;
    endif;
}

function jaywing_manual_fix_missing_dob() {
	$args = [
		'role__in' => array('customer', 'subscriber'),
		'orderby' => 'ID',
		'order' => 'DESC',
    ];

	$users = get_users( $args );

/*
    print_r(count($users));
    exit();
*/

    if ( ! empty( $users ) && is_array( $users ) ) :
        foreach( $users as $key => $user ) :
        	$new_form_data = get_user_meta( $user->ID, 'medicalform-new', true );
            $old_form_data = get_user_meta( $user->ID, 'medical-form', true );

            if( empty( $old_form_data ) )
                continue;

			$new_form_data = json_decode( maybe_unserialize( $new_form_data ) );
            $new_form_data = his_clinic_object_to_array( $new_form_data );

            $old_form_data = json_decode( maybe_unserialize( $old_form_data ) );
            $old_form_data = his_clinic_object_to_array( $old_form_data );

			if ( !isset($new_form_data['medical_form_details']['personal_information']['date_of_birth']['answer']) || $new_form_data['medical_form_details']['personal_information']['date_of_birth']['answer'] == '' || !isset($new_form_data['medical_form_details']['personal_information']['gender']['answer']) || $new_form_data['medical_form_details']['personal_information']['gender']['answer'] == '' ) {
				echo 'User ID: ' . $user->ID . '<br>';
				echo 'date_of_birth: ' . $new_form_data['medical_form_details']['personal_information']['date_of_birth']['answer'] . '<br>';
				echo 'date: ' . $old_form_data['date'] . '<br>';
				echo 'gender: ' . $new_form_data['medical_form_details']['personal_information']['gender']['answer'] . '<br><br>';
			}
        endforeach;
    endif;
}