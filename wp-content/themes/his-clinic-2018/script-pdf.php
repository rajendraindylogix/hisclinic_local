<?php

/* Template Name: Admin prescribe page */


if (!is_user_logged_in()) {
	wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

require 'vendor/autoload.php';
use Dompdf\Dompdf;

if ( !empty($_GET['download_order_id']) ) {
	$script_pdf = new Dompdf();

	$order_id = $_GET['download_order_id'];

	$html = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">';

	$order = wc_get_order($order_id);

	if ($order->get_status() === 'scripted' || $order->get_status() === 'completed') {
		$order_data = $order->get_data();
		$customer_id = $order->get_customer_id();
		$prescribing_doctor_id = get_post_meta($order_id, 'prescribing_doctor', true);
		$prescribing_doctor = get_user_by('id', $prescribing_doctor_id);
		$acf_doctor_id = 'user_' . $prescribing_doctor_id;
		$doctor_address = get_field('doctor_address', $acf_doctor_id);
		$doctor_address2 = get_field('doctor_suburb', $acf_doctor_id) . ' ' . get_field('doctor_state', $acf_doctor_id) . ' ' . get_field('doctor_postcode', $acf_doctor_id);
		$doctor_phone = get_field('doctor_phone', $acf_doctor_id);
		$doctor_prescriberno = get_field('doctor_prescriberno', $acf_doctor_id);
		$signature_image = get_field('signature', $acf_doctor_id);
		$signature_data = $signature_image ? base64_encode(file_get_contents($signature_image)) : '';
		$doctor_signature = 'data:image/png;base64,'.$signature_data;
		$scripted_time = get_field('scripted_time', $order_id);
		$scripted_time = date('j F Y', strtotime($scripted_time));
		$customer_name = get_full_name($customer_id);
		$billing_name = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];			
		$billing_address = $order_data['billing']['address_1'];
		$billing_address2 = $order_data['billing']['address_2'];
		$billing_address3 = $order_data['billing']['city'] . ' ' . $order_data['billing']['state'] . ' ' . $order_data['billing']['postcode'];
		$billing_phone = $order_data['billing']['phone'];
		$billing_fax = isset($order_data['billing']['fax']) ? $order_data['billing']['fax'] : '';
		$billing_email = $order_data['billing']['email'];
		($order_data['shipping']['first_name']) ? $shipping_name = $order_data['shipping']['first_name'].' '.$order_data['shipping']['last_name'] : $shipping_name = $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'];
		$shipping_address = $order_data['shipping']['address_1'];
		$shipping_address2 = $order_data['shipping']['address_2'];
		$shipping_address3 = $order_data['shipping']['city'] . ' ' . $order_data['shipping']['state'] . ' ' . $order_data['shipping']['postcode'];
		$shipping_phone = (isset($order_data['shipping']['phone']) && $order_data['shipping']['phone'] !== '') ? $order_data['shipping']['phone'] : $billing_phone;
		$shipping_fax = (isset($order_data['shipping']['fax']) && $order_data['shipping']['fax'] !== '') ? $order_data['shipping']['fax'] : $billing_fax;
		$shipping_email = (isset($order_data['shipping']['email']) && $order_data['shipping']['email'] !== '') ? $order_data['shipping']['email'] : $billing_email;

		$form = get_medical_form_data($customer_id);
		$dob = isset($form['medical_form_details']['personal_information']['date_of_birth']['answer']) ? $form['medical_form_details']['personal_information']['date_of_birth']['answer'] : '';

		$html .= '
		<div class="page_break">
			<table id="header_table">
				<tbody>
					<tr id="script_header">
						<td>'
							.$prescribing_doctor->display_name.'<br />'
							.$doctor_address.'<br />'
							.$doctor_address2.'<br />'
							.$doctor_phone.'<br />'
							.$doctor_prescriberno.'<br />'
						.'</td>
						<td id="script_details">
							Script ID: '.$order_id.'<br />
							Issued under clause 35 of the poisons Reg 2008<br />
							<br />'
							.$scripted_time
						.'</td>
					</tr>
					<tr id="customer_headings">
						<td>Patient details</td>
						<td>Shipping information</td>
					</tr>
					<tr>
						<td>
							<span style="font-weight: bold;">' . strtoupper($customer_name) . ' (DOB: '.$dob.')</span><br />'
							.$billing_address.'<br />'
							.($billing_address2 ? $billing_address2.'<br />' : '')
							.$billing_address3.'<br />
							Australia<br />
							Phone: '.$billing_phone;
							if (!empty($billing_fax)) {
								$html .= '&emsp;Fax: ' . $billing_fax;
							}
							$html .= '<br />';
							$html .=
							'Email: '.$billing_email.'<br />
						</td>
						<td>'
							.$shipping_name.'<br />'
							.$shipping_address.'<br />'
							.($shipping_address2 ? $shipping_address2.'<br />' : '')
							.$shipping_address3.'<br />
							Australia<br />';
							$html .=
							'Phone: '.$shipping_phone;
							if (!empty($shipping_fax)) {
								$html .= '&emsp;Fax: '.$shipping_fax;
							}
							$html .= '<br />
							Email: '.$shipping_email.'<br />
						</td>
					</tr>
				</tbody>
			</table>';
			$html .=
			'<table id="script_table">
				<thead id="script_columns">
					<tr>
						<th>Product</th>
						<th>Qty</th>
						<th>Repeat interval</th>
						<th>Instructions</th>
					</tr>
				</thead>
				<tbody>';
					foreach ($order->get_items() as $item_id => $item) {
						$html .= '<tr>';
						$item_name = $item->get_name();
						if (strpos($item_name, 'Sildenafil') !== false) {
							$item_name = 'Sildenafil T 100mg';
						} elseif (strpos($item_name, 'Daily Cialis') !== false) {
							$item_name = 'Daily Cialis T 5mg';	
						} elseif (strpos($item_name, 'Cialis') !== false) {
							$item_name = 'Cialis T 20mg';
						}
						$pack_size = wc_get_order_item_meta($item_id, 'pa_pack-size');
						$html .= '<td style="font-weight: bold;">'.$item_name.'<br />x '.$pack_size.' tablets</td>';
						$html .= '<td>'.$item->get_quantity().'</td>';
						$html .= '<td>No repeats</td>';
						$html .= '<td>'.wc_get_order_item_meta($item_id, 'dosage_instructions').'</td>';
						$html .= '</tr>';
					}
				$html .=
				'</tbody>
			</table>
			<br /><br />
			<table id="signature_table">
				<tbody>
					<tr>
						<td width="70%"></td>
						<td id="doctor_signature">
						<img src="'.$doctor_signature.'" height="80px" width="300px" />
						</td>
					</tr>
					<tr>
						<td width="70%"></td>
						<td id="doctor_details">'
							.$prescribing_doctor->display_name.'<br />'
							.get_field('doctor_credentials', $acf_doctor_id)
						.'</td>
					</tr>
				</tbody>
			</table>
		</div>';
		$html .=
		'<style>
		.page_break {
			page-break-after: always;
		}
		#signature_table tr {
			border: none;
		}
		#script_header {
			background-color: grey;
			color: white !important;
		}
		#script_details {
			text-align: right;
		}
		#customer_headings {
			background-color: lightgrey;
			font-weight: bold;
		}
		#script_columns {
			background-color: lightgrey;
		}
		#doctor_signature {
			border: 1px solid black;
			height: 75px;
		}
		#doctor_details {
			font-weight: bold;
			text-align: right;
		}
		</style>';
	}

	$script_pdf->load_html($html);
	$script_pdf->render();
	$pdf_filename = 'hisclinic-script-order-' . $order_id . '.pdf';
	$script_pdf->stream($pdf_filename);
/*
	file_put_contents(get_uploads_path() . '/batches/' . $pdf_filename, $script_pdf->output());
	echo 'All done!';
*/
} elseif (empty($_GET['order_id'])) {
	$args = array(
		'limit'	 => -1,
		'orderby'   => 'date',
		'order'	 => 'DESC',
		'status'	=> array('scripted', 'completed'),
		'meta_key'  => 'batch_time',
		'meta_compare' => 'NOT EXISTS',
	);
	$orders_waiting = wc_get_orders($args);
	$order_count = count($orders_waiting);

	if ($order_count !== 0) {
		echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">';
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>';

		echo '<div class="container">';
		echo '<form action="'.home_url().'/script-pdf" method="get">';
		echo '<br/><span style="font-size: 16px; font-weight: bold;">'.$order_count.' orders waiting: </span>&emsp;';
		echo '<button type="submit" class="btn-large waves-effect waves-light red">Generate PDFs for scripted orders<i class="material-icons right">send</i></button><br /><br />';
		echo
		'<table class="striped">
			<thead>
				<th>Order ID</th>
				<th>Customer name</th>
				<th>Order time</th>
			</thead>
			<tbody>';
			foreach ($orders_waiting as $order) {
				echo '<tr>';
				echo '<td>';
				$order_id = $order->ID;
				echo '<input type="hidden" name="order_id[]" value="'.$order_id.'" /><strong>Order #'.$order_id;
				echo '</strong></td>';
				echo '<td><strong>' . get_full_name($order->get_user_id()) . '</strong></td>';
				echo '<td><strong>'.date('j F Y H:i', strtotime($order->order_date)).'</strong></td>';
				//echo $prescribing_doctor;
				echo '</tr>';
			}
		echo '</tbody></table>';
		echo '</form>';
		echo '</div>';
	} else {
		echo '<br/><span style="font-size: 16px; font-weight: bold;">0 orders waiting. </span>';
	}
} else {
	$script_pdf = new Dompdf();
	$order_ids = $_GET['order_id'];

	if (is_array($order_ids)) {
		$order_count = count($order_ids);
	} else {
		$order_ids = [$order_ids];
		$order_count = 1;
	}

	$batch_time = current_time('Y-m-d H:i:s', 0);
	$batch_heading = 'Order count: '.$order_count.'<br/>';
	$batch_details = array(
		'post_title' => 'Batch '.$batch_time,
		'post_content' => $batch_heading,
		'post_author' => get_current_user_id(),
		'post_date' => $batch_time,
		'post_status' => 'publish',
		'post_type' => 'batch',
	);
	$batch_number = wp_insert_post($batch_details);

	$html = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">';

	$batch_orders = [];

	$order_ids = sort_orders_ids_by_first_name($order_ids);
	foreach ($order_ids as $order_id) {
		error_log($order_id.' generating pdf...');
		$order = wc_get_order($order_id);

		if ($order->get_status() === 'scripted' || $order->get_status() === 'completed') {
			$order_data = $order->get_data();
			$customer_id = $order->get_customer_id();
			//$prescribing_doctor_id = $order->get_meta('prescribing_doctor');
			//$prescribing_doctor_id = get_field('prescribing_doctor', $order_id);
			$prescribing_doctor_id = get_post_meta($order_id, 'prescribing_doctor', true);
			error_log($prescribing_doctor_id);
			$prescribing_doctor = get_user_by('id', $prescribing_doctor_id);
			$acf_doctor_id = 'user_' . $prescribing_doctor_id;
			$doctor_address = get_field('doctor_address', $acf_doctor_id);
			$doctor_address2 = get_field('doctor_suburb', $acf_doctor_id) . ' ' . get_field('doctor_state', $acf_doctor_id) . ' ' . get_field('doctor_postcode', $acf_doctor_id);
			$doctor_phone = get_field('doctor_phone', $acf_doctor_id);
			$doctor_prescriberno = get_field('doctor_prescriberno', $acf_doctor_id);
			$signature_image = get_field('signature', $acf_doctor_id);
			$signature_data = $signature_image ? base64_encode(file_get_contents($signature_image)) : '';
			$doctor_signature = 'data:image/png;base64,'.$signature_data;
			$scripted_time = get_field('scripted_time', $order_id);
			$scripted_time = date('j F Y', strtotime($scripted_time));
			$customer_name = get_full_name($customer_id);
			$billing_name = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];			
			$billing_address = $order_data['billing']['address_1'];
			$billing_address2 = $order_data['billing']['address_2'];
			$billing_address3 = $order_data['billing']['city'] . ' ' . $order_data['billing']['state'] . ' ' . $order_data['billing']['postcode'];
			$billing_phone = $order_data['billing']['phone'];
			$billing_fax = isset($order_data['billing']['fax']) ? $order_data['billing']['fax'] : '';
			$billing_email = $order_data['billing']['email'];
			($order_data['shipping']['first_name']) ? $shipping_name = $order_data['shipping']['first_name'].' '.$order_data['shipping']['last_name'] : $shipping_name = $order_data['billing']['first_name'].' '.$order_data['billing']['last_name'];
			$shipping_address = $order_data['shipping']['address_1'];
			$shipping_address2 = $order_data['shipping']['address_2'];
			$shipping_address3 = $order_data['shipping']['city'] . ' ' . $order_data['shipping']['state'] . ' ' . $order_data['shipping']['postcode'];
			$shipping_phone = (isset($order_data['shipping']['phone']) && $order_data['shipping']['phone'] !== '') ? $order_data['shipping']['phone'] : $billing_phone;
			$shipping_fax = (isset($order_data['shipping']['fax']) && $order_data['shipping']['fax'] !== '') ? $order_data['shipping']['fax'] : $billing_fax;
			$shipping_email = (isset($order_data['shipping']['email']) && $order_data['shipping']['email'] !== '') ? $order_data['shipping']['email'] : $billing_email;

			$form = get_medical_form_data($customer_id);
			$dob = isset($form['medical_form_details']['personal_information']['date_of_birth']['answer']) ? $form['medical_form_details']['personal_information']['date_of_birth']['answer'] : '';
			$html .= '
			<div class="page_break">
				<table id="header_table">
					<tbody>
						<tr id="script_header">
							<td>'
								.$prescribing_doctor->display_name.'<br />'
								.$doctor_address.'<br />'
								.$doctor_address2.'<br />'
								.$doctor_phone.'<br />'
								.$doctor_prescriberno.'<br />'
							.'</td>
							<td id="script_details">
								Script ID: '.$order_id.'<br />
								Issued under clause 35 of the poisons Reg 2008<br />
								<br />'
								.$scripted_time
							.'</td>
						</tr>
						<tr id="customer_headings">
							<td>Patient details</td>
							<td>Shipping information</td>
						</tr>
						<tr>
							<td>
								<span style="font-weight: bold;">' . strtoupper($customer_name) . ' (DOB: '.$dob.')</span><br />'
								.$billing_address.'<br />'
								.($billing_address2 ? $billing_address2.'<br />' : '')
								.$billing_address3.'<br />
								Australia<br />
								Phone: '.$billing_phone;
								if (!empty($billing_fax)) {
									$html .= '&emsp;Fax: ' . $billing_fax;
								}
								$html .= '<br />';
								$html .=
								'Email: '.$billing_email.'<br />
							</td>
							<td>'
								.$shipping_name.'<br />'
								.$shipping_address.'<br />'
								.($shipping_address2 ? $shipping_address2.'<br />' : '')
								.$shipping_address3.'<br />
								Australia<br />';
								$html .=
								'Phone: '.$shipping_phone;
								if (!empty($shipping_fax)) {
									$html .= '&emsp;Fax: '.$shipping_fax;
								}
								$html .= '<br />
								Email: '.$shipping_email.'<br />
							</td>
						</tr>
					</tbody>
				</table>';
				$html .=
				'<table id="script_table">
					<thead id="script_columns">
						<tr>
							<th>Product</th>
							<th>Qty</th>
							<th>Repeat interval</th>
							<th>Instructions</th>
						</tr>
					</thead>
					<tbody>';
						foreach ($order->get_items() as $item_id => $item) {
							$html .= '<tr>';
							$item_name = $item->get_name();
							if (strpos($item_name, 'Sildenafil') !== false) {
								$item_name = 'Sildenafil T 100mg';
							} elseif (strpos($item_name, 'Daily Cialis') !== false) {
								$item_name = 'Daily Cialis T 5mg';	
							} elseif (strpos($item_name, 'Cialis') !== false) {
								$item_name = 'Cialis T 20mg';
							}
							$pack_size = wc_get_order_item_meta($item_id, 'pa_pack-size');
							$html .= '<td style="font-weight: bold;">'.$item_name.'<br />x '.$pack_size.' tablets</td>';
							$html .= '<td>'.$item->get_quantity().'</td>';
							$html .= '<td>No repeats</td>';
							$html .= '<td>'.wc_get_order_item_meta($item_id, 'dosage_instructions').'</td>';
							$html .= '</tr>';
						}
					$html .=
					'</tbody>
				</table>
				<br /><br />
				<table id="signature_table">
					<tbody>
						<tr>
							<td width="70%"></td>
							<td id="doctor_signature">
							<img src="'.$doctor_signature.'" height="80px" width="300px" />
							</td>
						</tr>
						<tr>
							<td width="70%"></td>
							<td id="doctor_details">'
								.$prescribing_doctor->display_name.'<br />'
								.get_field('doctor_credentials', $acf_doctor_id)
							.'</td>
						</tr>
					</tbody>
				</table>
			</div>';
			$html .=
			'<style>
			.page_break {
				page-break-after: always;
			}
			#signature_table tr {
				border: none;
			}
			#script_header {
				background-color: grey;
				color: white !important;
			}
			#script_details {
				text-align: right;
			}
			#customer_headings {
				background-color: lightgrey;
				font-weight: bold;
			}
			#script_columns {
				background-color: lightgrey;
			}
			#doctor_signature {
				border: 1px solid black;
				height: 75px;
			}
			#doctor_details {
				font-weight: bold;
				text-align: right;
			}
			</style>';

			$existing_batch_number = get_post_meta($order_id, 'batch_number', true);
			//if ($order_status === 'scripted' && $existing_batch_number === '') {
			if ($existing_batch_number === '') {
				update_post_meta($order_id, 'batch_number', $batch_number);
				update_post_meta($order_id, 'batch_time', $batch_time);

				$batch_post = get_post($batch_number);
				$batch_content = $batch_post->post_content;
				$order_time = $order->order_date;
				$order_time = date('j F Y H:i', strtotime($order_time));
				$order_info = 'Order #'.$order_id.'&emsp;'.$billing_name.'&ensp;'.$order_time.'<br/>';
				$batch_content .= $order_info;
				wp_update_post(array(
					'ID' => $batch_number,
					'post_content' => $batch_content
				));
				$batch_orders[] = $order_id;

				//$order->update_status('wc-emailed', 'Script has been generated - ');
			} elseif ($existing_batch_number !== '') {
				$batch_time = get_post_meta($order_id, 'batch_time', true);
				$order_count--;
			}
			error_log($order_id.' pdf generated in batch #'.$batch_number.' @ '.$batch_time);
			
			// Completing Order
			$order->update_status('completed');
		} else {
			$order_count--;
			error_log($order_id.' not scripted status.');
		}
	}

	if ($order_count > 0) {
		update_post_meta($batch_number, 'batch_time', $batch_time);
		update_post_meta($batch_number, 'order_count', $order_count);
		update_post_meta($batch_number, 'orders', $batch_orders);
		$first_order = wc_get_order(end($batch_orders));
		$first_order_time = date('Y-m-d H:i:s', strtotime($first_order->order_date));
		$last_order = wc_get_order(reset($batch_orders));
		$last_order_time = date('Y-m-d H:i:s', strtotime($last_order->order_date));
		$time_frame = $first_order_time.' => '.$last_order_time;
		update_post_meta($batch_number, 'time_frame', $time_frame);
	}

	$script_pdf->load_html($html);
	$script_pdf->render();
	$pdf_filename = 'hisclinic-scripts-'.date('d-m-Y', strtotime($batch_time)).'.pdf';
	//$script_pdf->stream($pdf_filename);
	file_put_contents(get_uploads_path() . '/batches/' . $pdf_filename, $script_pdf->output());
	echo 'All done!';
}
?>
