<?php

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

if (empty($_GET['order_id'])) {
    $args = array(
        'limit'     => -1,
        'orderby'   => 'date',
        'order'     => 'DESC',
        'status'    => 'scripted',
        'meta_key'  => 'batch_time',
        'meta_compare' => 'NOT EXISTS',
    );
    $orders_waiting = wc_get_orders($args);

    // todo use a WP_List_table instead
    /*
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
    }
    */
    echo '<form action="'.home_url().'/script-pdf" method="get">';
    echo '<br/><span style="font-size: 16px; font-weight: bold;">'.count($orders_waiting).' orders waiting: </span>&emsp;';
    echo '<button type="submit" class="button" style="background-color: red; color: white;">Generate PDFs for scripted orders</button><br /><br />';
    echo
    '<table class="widefat fixed">
        <thead style="font-weight: bold !important;">
            <th>Order ID</th>
            <th>Customer name</th>
			<th>Medications</th>
			<th>Medical conditions</th>
			<th>Allergies</th>
			<th>Prescribing doctor</th>
        </thead>
        <tbody>';
        foreach ($orders_waiting as $order) {
            echo '<tr>';
            echo '<td>';
            $order_id = $order->ID;
			echo '<input type="hidden" name="order_id[]" value="'.$order_id.'" />';
			echo '<a href="'.admin_url().'post.php?post='.$order_id.'&action=edit"><strong>Order #'.$order_id.'</strong></a>';
			echo '<br /><strong>'.date('j F Y H:i', strtotime($order->order_date)).'</strong>';
			echo '</td>';
			$customer_id = $order->get_customer_id();
			$customer = get_user_by('id', $customer_id);
			echo '<td><a href="'.admin_url().'edit.php?post_status=all&post_type=shop_order&_customer_user='.$customer_id.'">';
			$customer_name = $customer->display_name;
			echo '<strong>'.$customer_name.'</strong></a>';

			$shipping_name = $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
			if (strtolower($shipping_name) !== strtolower($customer_name)) {
				echo '<br /><span style="color: orange; font-weight: bold;">Shipping name: </span>'.$shipping_name;
			}
			//testing medical questionnaire answers
			$form = get_medical_form_data($customer_id);
			$form = $form['medical_form_details'];
			$dob = $form['personal_information']['date_of_birth']['answer'];

			$medications = '';
			$medical_conditions = '';
			$allergies = 'Nil';

			foreach ($form['medical_history'] as $key => $value) {
				switch ($key) {
					case 'herbs_description':
						if ( $value['answer'] !== '' ) {
							$medications .= '<li>'.$value['question'].': <strong>'.$value['answer']. '</strong></li>';
						}
						break;
					case 'nitrate':
						if ( !in_array(strtolower($value['answer']), array('no', 'none')) ) {
							$medications .= '<li>'.$value['question'].': <strong>'.$value['answer']. '</strong></li>';
						}
						break;

					case 'allergies_description':
						if ( $value['answer'] !== '' ) {
							$allergies = '<strong>' . $value['answer'] . '</strong>';
						}
						break;

					case 'medical_condition_description':
						if ( $value['answer'] !== '' ) {
							$medical_conditions .= '<li>'.$value['question'].': <strong>'.$value['answer']. '</strong></li>';
						}
						break;

					case 'heart_disease':
					case 'lightheadedness':
					case 'medical_condition':
					case 'medical_history':
					case 'blood_pressure_diagnosis':
					case 'cardiovascular_symptoms':
					case 'heart_attack_past':
					case 'stroke_TIA':
					case 'conditions_1':
					case 'conditions_2':
						if ( !in_array(strtolower($value['answer']), array('no', 'none')) ) {
							$medical_conditions .= '<li>'.$value['question'].': <strong>'.$value['answer']. '</strong></li>';
						}
						break;

					case 'blood_pressure_test':
							if ( !in_array(strtolower($value['answer']), array('yes')) ) {
								$medical_conditions .= '<li>'.$value['question'].': <strong>'.$value['answer']. '</strong></li>';
							}
							break;

					case 'medical_history_desctiption':
						$medical_history = '';

						foreach ( $value['answer'] as $medical_value ) {
							if ( $medical_value['description'] !== '' ) {
								$medical_history .= $medical_value['date'] . ' - ' . $medical_value['description'] . '<br>';
							}
						}

						if ( $medical_history !== '' ) {
							$medical_conditions .= '<li>' . $value['question'] . '<br><strong>' . $medical_history . '</strong></li>';
						}
						break;

				}
			}

			if ( isset($form['additional_details']['answer']) && $form['additional_details']['answer'] !== '' ) {
				$medical_conditions .= '<li>' . $form['additional_details']['question'] . ': <strong>' . $form['additional_details']['answer'] . '</strong></li>';
			}

			if ( $medications === '' ) {
				$medications = 'None';
			}
			if ( $medical_conditions === '' ) {
				$medical_conditions = 'None';
			}

			echo '<br /><strong>DOB: </strong>' . $dob . '</td>';
			echo '<td><ul>' . $medications . '</ul></td>';
			echo '<td><ul>' . $medical_conditions . '</ul></td>';
			echo '<td>' . $allergies . '</td>';
            echo '<td>Dr Neha Parvatreddy</td>';
			echo '</tr>';
        }
    echo '</tbody></table>';
    echo '</form>';
}

?>
