<?php

/* Template Name: Admin Email Batch page */


if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

if (!empty($_GET['batch_number'])) {
	$batch_number = $_GET['batch_number'];
	$batch_time = get_post_meta($batch_number, 'batch_time', true);
	add_filter('wp_mail_content_type', 'set_html_content_type');
	function set_html_content_type() {
		return 'text/html';
	}
	ob_start();

	echo '
	<h1>Batch #'.$batch_number.' - '.$batch_time.'</h1>
	<table style="border: 1px solid black;">
	<thead>
	<th style="border: 1px solid black;">Patient name</th>
	<th style="border: 1px solid black;">Medications</th>
	<th style="border: 1px solid black;">Quantity</th>
	<th style="border: 1px solid black;">Pack size</th>
	</thead>
	<tbody>';

	$order_ids = get_post_meta($batch_number, 'orders', true);
    $order_ids = sort_orders_ids_by_first_name($order_ids);
	foreach ($order_ids as $order_id) {
		echo '<tr>';
		$order = wc_get_order($order_id);
		$customer_id = $order->get_customer_id();
		$customer = get_user_by('id', $customer_id);
		$customer_name = $customer->display_name;
		$customer_name = ucwords(strtolower($customer_name));
		echo '<td style="border: 1px solid black;">'.$customer_name.'</td>';
		echo '<td style="border: 1px solid black;">';
		$items = $order->get_items();
		$item = current($items);
		$item_name = $item['name'];
		$item_quantity = $item['quantity'];
		$item_id = $item->get_id();
		$pack_size = (int)wc_get_order_item_meta($item_id, 'pa_pack-size');
		$product_name = $item_name;//.' (pack size: '.$pack_size.')';
		if (strpos($item_name, 'Sildenafil') !== false) {
			$product_name = 'Sildenafil T 100mg';
		} elseif (strpos($item_name, 'Daily Cialis') !== false) {
			$product_name= 'Daily Cialis T 5mg';    
		} elseif (strpos($item_name, 'Cialis') !== false) {
			$product_name= 'Cialis T 20mg';
		}
		for ($i = 1; $i <= $item_quantity; $i++) {
			$total[] = $product_name . ' (Pack size: ' . $pack_size . ')';
		}
		echo $product_name;
		echo '</td>';
		echo '<td style="border: 1px solid black; text-align: center;">'.$item_quantity.' x</td>';
		echo '<td style="border: 1px solid black; text-align: center;">'.$pack_size.'</td>';
		echo '</tr>';
	}
	echo '
	</tbody>
	</table>
	<br />
	<br />
	<table style="border: 1px solid black;">
	<thead>
	<th style="border: 1px solid black;">Medication</th>
	<th style="border: 1px solid black;">Total quantity</th>
	</thead>
	<tbody>';
	$total_count = array_count_values($total);
	foreach ($total_count as $product => $quantity) {
		echo '<tr>';
		echo '<td style="border: 1px solid black;">'.$product.'</td>';
		echo '<td style="border: 1px solid black; text-align: center;">'.$quantity.'</td>';
		echo '</tr>';
	}
	echo '
	</tbody>
	</table>
	';
	$headers[] = 'From: HC Admin <wordpress@hisclinic.com.au>';
	$files = glob(get_uploads_path() . '/batches/*.*');
	foreach ($files as $key => $filename) {
		$attachments[] = $filename;
	}

	$body = ob_get_clean();

	echo $body;

	$mail = wp_mail(
		'info@hisclinic.com.au',
		'His Clinic Scripts, Batch #'.$batch_number.' - '.$batch_time,
		$body,
		$headers,
		$attachments
	);

	//error_log($mail);
	remove_filter('wp_mail_content_type', 'set_html_content_type');

	if ( $mail ) {
		$files = glob(get_uploads_path() . '/batches/*');
		foreach ($files as $file) {
			unlink($file);
		}
		echo '<p>Email sent and files deleted!</p>';
	}
}

?>
