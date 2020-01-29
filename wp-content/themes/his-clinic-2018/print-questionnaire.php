<?php

/* Template Name: Admin Questionnaire page */


if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

require 'vendor/autoload.php';
use Dompdf\Dompdf;

if (!empty($_GET['order_id'])) {
    $script_pdf = new Dompdf();
    $order_ids = $_GET['order_id'];
    $batch_time = current_time('Y-m-d H:i:s', 0);

    $html = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">';

    $order_ids = sort_orders_ids_by_first_name($order_ids);
    foreach ($order_ids as $order_id) {
		$order = wc_get_order($order_id);
		$customer_id = $order->get_user_id();
		$customer = get_user_by('id', $customer_id);

		$form = get_medical_form_data($customer_id);
		$form = $form['medical_form_details'];

		$html .= '
            <div class="page_break">
				<p style="margin:0 0 5px;"><span style="font-weight: bold;font-size: 20px;">Order ID: ' . $order_id . '</span></p>
				<p style="margin:0 0 5px;"><span style="font-weight: bold;font-size: 18px;">Personal Information</span></p>
				<p style="margin:0 0 10px;"><span style="font-weight: bold;">Full legal name: </span>'.$customer->display_name.'<br>';

		foreach ($form['personal_information'] as $key => $value) {
			$html .= '<span style="font-weight: bold;">'.$value['question'].': </span>'.$value['answer']. '<br>';
		}

		$html .= '</p><p style="margin:0 0 5px;"><span style="font-weight: bold;font-size: 18px;">Sexual Activity</span></p><p style="margin:0 0 10px;">';

		foreach ($form['sexual_activity'] as $key => $value) {
			$html .= '<span style="font-weight: bold;">'.$value['question'].': </span>'.$value['answer']. '<br>';
		}

		$html .= '</p><p style="margin:0 0 5px;"><span style="font-weight: bold;font-size: 18px;">Medical History</span></p><p style="margin:0 0 10px;">';

		foreach ($form['medical_history'] as $key => $value) {
			if ($key === 'medical_history_desctiption') {
				$medical_history = '';

				foreach ( $value['answer'] as $medical_value ) {
					if ( $medical_value['description'] !== '' ) {
						$medical_history .= $medical_value['date'] . ' - ' . $medical_value['description'] . '<br>';
					}
				}

				if ( $medical_history !== '' ) {
					$html .= '<span style="font-weight: bold;">' . $value['question'] . '</span><br>' . $medical_history;
				}
			} else {
				$html .= '<span style="font-weight: bold;">'.$value['question'].': </span>'.$value['answer']. '<br>';
			}
		}

		foreach ($form['recommended_prescription'] as $key => $value) {
			$html .= '<span style="font-weight: bold;">'.$value['question'].': </span>'.$value['answer']. '<br>';
		}

		$html .= '</p>';

		if ( isset($form['additional_details']['answer']) && $form['additional_details']['answer'] !== '' ) {
			$html .= '<p style="margin:0 0 5px;"><span style="font-weight: bold;font-size: 18px;">Additional Details</span></p><p style="margin:0 0 10px;">';
			$html .= '<span style="font-weight: bold;">' . $form['additional_details']['question'] . ': </span>' . $form['additional_details']['answer'] . '<br>';
			$html .= '</p>';
		}
		$html .= '</div>';
	}

	$html .=
		'<style>
			body {
				font-family: Arial;
			}
			.page_break {
				page-break-after: always;
			}
		</style>';

	$script_pdf->load_html($html);
    $script_pdf->render();

	if ( isset($_GET['download']) ) {
		$pdf_filename = 'hisclinic-mq-order-' . $order_ids[0] . '.pdf';
		$script_pdf->stream($pdf_filename);
	} else {
	    $pdf_filename = 'hisclinic-mqs-'.date('d-m-Y', strtotime($batch_time)).'.pdf';
		file_put_contents(get_uploads_path() . '/batches/' . $pdf_filename, $script_pdf->output());
		echo 'All done!';
	}
}
?>
