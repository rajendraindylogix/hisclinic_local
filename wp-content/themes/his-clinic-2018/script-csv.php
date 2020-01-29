<?php

/* Template Name: Admin CSV page */


if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

if (!empty($_GET['order_id'])) {
    $order_ids = $_GET['order_id'];
    $last_order = end($order_ids);

    $linebreak_row = array('', '', '', '', '', '', '', '', '', '', '', '');

    $batch_time = current_time('m/d/Y H:i', 0);
    $csv_filename = 'hisclinic-'.date('d-m-Y', strtotime($batch_time)).'.csv';

    //header('Content-Type: text/csv; charset=utf-8');
    //header('Content-Disposition: attachment; filename='.$csv_filename);
    $script_csv = fopen(get_uploads_path() . '/batches/' . $csv_filename, 'w');

    $heading_row = array('H', $batch_time, '', '', '', '', '', '', '', '', '', '');
    fputcsv($script_csv, $heading_row);

    $order_ids = sort_orders_ids_by_first_name($order_ids);
    foreach ($order_ids as $order_id) {
        $order = wc_get_order($order_id);

        if ($order->get_status() === 'scripted' || $order->get_status() === 'completed') {
            $order_data = $order->get_data();

            $prescribing_doctor_id = get_post_meta($order_id, 'prescribing_doctor', true);
            $prescribing_doctor = get_user_by('id', $prescribing_doctor_id);
            $acf_doctor_id = 'user_' . $prescribing_doctor_id;
            $doctor_prescriberno = get_field('doctor_prescriberno', $acf_doctor_id);
            $doctor_prescriberno = substr($doctor_prescriberno, -7);
            $scripted_time = get_post_meta($order_id, 'scripted_time', true);
            $scripted_time = date('Y-m-d', strtotime($scripted_time));

            $customer_id = $order->get_user_id();
            $pharmacy_customer_id = '3'.str_pad($customer_id, 6, '0', STR_PAD_LEFT);

            $first_name = get_first_name($customer_id);
            $last_name = get_last_name($customer_id);

			if ( $last_name == '' ) {
			    $parts = explode(' ', $first_name);
				$first_name = array_shift($parts);
				$last_name = array_pop($parts);
				$middle_name = trim(implode(' ', $parts));

				if ( $middle_name ) {
					$first_name .= ' ' . $middle_name;
				}
			}

            $address_1 = $order_data['shipping']['address_1'];
            $suburb = $order_data['shipping']['city'];
            $postcode = $order_data['shipping']['postcode'];
            $state = $order_data['shipping']['state'];

            $customer_row = array('P', $pharmacy_customer_id, $last_name, $first_name, $address_1, '', $suburb, $postcode, $state, $doctor_prescriberno, 'CN99998', $scripted_time);
            fputcsv($script_csv, $customer_row);

            $items = $order->get_items();
            $item_no = 1;
            foreach ($items as $item) {
                $item_id = $item->get_id();
                $item_name = get_field('pharmacy_name', $item->get_product_id());
                
                error_log(print_r($item, true));
                
				$pack_size = (int)wc_get_order_item_meta($item_id, 'pa_pack-size');
                $item_quantity = (int)$item['quantity'] * $pack_size;
                $dosage_instructions = wc_get_order_item_meta($item_id, 'dosage_instructions');
                $item_script_id = $order_id;
                $item_script_id = str_pad($order_id, 8, '0', STR_PAD_LEFT);
                $item_script_id = '3'.$item_script_id.'-HC-'.$item_no;
                $item_row = array('S', $item_name, $item_quantity, '', '', $item_script_id, $dosage_instructions, '', '', '', '', '');
                fputcsv($script_csv, $item_row);

                if ($order_id !== $last_order) {
                    fputcsv($script_csv, $linebreak_row);
                }

                $item_no++;
            }
        }
    }

    $csv_eof = array('F','','','','','','','','','','','');
    fputcsv($script_csv, $csv_eof);

	fclose($script_csv);
	echo 'All done!';
} else {
    die();
}

?>
