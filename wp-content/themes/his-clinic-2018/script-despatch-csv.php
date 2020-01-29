<?php
/* Template Name: Admin Despatch CSV page */


if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

function remove_commas($input) {
    $output = str_replace(',', ' ', $input);
    return $output;
};

if (!empty($_GET['order_id'])) {
    $order_ids = $_GET['order_id'];

    $csv_filename = 'hisclinic-despatch-'.current_time('d-m-Y', 0).'.csv';

    //header('Content-Type: text/csv; charset=utf-8');
    //header('Content-Disposition: attachment; filename='.$csv_filename);
    $script_despatch_csv = fopen(get_uploads_path() . '/batches/' . $csv_filename, 'w');

    $header_row = 'C_CONSIGNMENT_ID,C_POST_CHARGE_TO_ACCOUNT,C_CHARGE_CODE,C_MERCHANT_CONSIGNEE_CODE,C_CONSIGNEE_NAME,C_CONSIGNEE_BUSINESS_NAME,C_CONSIGNEE_ADDRESS_1,C_CONSIGNEE_ADDRESS_2,C_CONSIGNEE_ADDRESS_3,C_CONSIGNEE_ADDRESS_4,C_CONSIGNEE_SUBURB,C_CONSIGNEE_STATE_CODE,C_CONSIGNEE_POSTCODE,C_CONSIGNEE_COUNTRY_CODE,C_CONSIGNEE_PHONE_NUMBER,C_PHONE_PRINT_REQUIRED,C_CONSIGNEE_FAX_NUMBER,C_DELIVERY_INSTRUCTION,C_SIGNATURE_REQUIRED,C_PART_DELIVERY,C_COMMENTS,C_ADD_TO_ADDRESS_BOOK,C_CTC_AMOUNT,C_REF,C_REF_PRINT_REQUIRED,C_REF2,C_REF2_PRINT_REQUIRED,C_CHARGEBACK_ACCOUNT,C_RECURRING_CONSIGNMENT,C_RETURN_NAME,C_RETURN_ADDRESS_1,C_RETURN_ADDRESS_2,C_RETURN_ADDRESS_3,C_RETURN_ADDRESS_4,C_RETURN_SUBURB,C_RETURN_STATE_CODE,C_RETURN_POSTCODE,C_RETURN_COUNTRY_CODE,C_REDIR_COMPANY_NAME,C_REDIR_NAME,C_REDIR_ADDRESS_1,C_REDIR_ADDRESS_2,C_REDIR_ADDRESS_3,C_REDIR_ADDRESS_4,C_REDIR_SUBURB,C_REDIR_STATE_CODE,C_REDIR_POSTCODE,C_REDIR_COUNTRY_CODE,C_MANIFEST_ID,C_CONSIGNEE_EMAIL,C_EMAIL_NOTIFICATION,C_APCN,C_SURVEY,C_DELIVERY_SUBSCRIPTION,C_EMBARGO_DATE,C_SPECIFIED_DATE,C_DELIVER_DAY,C_DO_NOT_DELIVER_DAY,C_DELIVERY_WINDOW,C_CDP_LOCATION,A_ACTUAL_CUBIC_WEIGHT,A_LENGTH,A_WIDTH,A_HEIGHT,A_NUMBER_IDENTICAL_ARTS,A_CONSIGNMENT_ARTICLE_TYPE_DESCRIPTION,A_IS_DANGEROUS_GOODS,A_IS_TRANSIT_COVER_REQUIRED,A_TRANSIT_COVER_AMOUNT,G_ORIGIN_COUNTRY_CODE,G_HS_TARIFF,G_DESCRIPTION,G_PRODUCT_TYPE,G_PRODUCT_CLASSIFICATION,G_QUANTITY,G_WEIGHT,G_UNIT_VALUE,G_TOTAL_VALUE';
    fputs($script_despatch_csv, $header_row."\n");
    $secondary_header_row = 'IGNORED,OPTIONAL,MANDATORY,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,OPTIONAL,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,OPTIONAL,MANDATORY/OPTIONAL REFER TO GUIDE,OPTIONAL,OPTIONAL,MANDATORY,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,MANDATORY/OPTIONAL REFER TO GUIDE,OPTIONAL,MANDATORY,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,';
    fputs($script_despatch_csv, $secondary_header_row."\n");

    $order_ids = sort_orders_ids_by_first_name($order_ids);
    foreach ($order_ids as $order_id) {
        $order = wc_get_order($order_id);

        if ($order->get_status() === 'scripted' || $order->get_status() === 'completed') {
            $order_data = $order->get_data();
            $customer_id = $order->get_user_id();

            $customer_name = implode(' ', [$order_data['shipping']['first_name'], $order_data['shipping']['last_name']]);
            $customer_company = $order_data['shipping']['company'];
            $address_1 = $order_data['shipping']['address_1'];
            $address_2 = $order_data['shipping']['address_2'];
            $suburb = $order_data['shipping']['city'];
            $postcode = $order_data['shipping']['postcode'];
            $state = $order_data['shipping']['state'];
            $phone = $order_data['billing']['phone'];
            $email = $order_data['billing']['email'];
            $customer_note = $order->get_customer_note();

            $pharmacy_customer_id = '3'.str_pad($customer_id, 6, '0', STR_PAD_LEFT);

            $order_row = array('', '', '3I35', '', $customer_name, '', $address_1, $address_2, '', '', $suburb, $state, $postcode, 'AU', $phone, '', '', $customer_note, 'A', '', '', 'Y', '', $order_id, 'N', $pharmacy_customer_id, 'N', '', '', "Arncliffe Pharmacy", '26 Firth Street', '', '', '', 'Arncliffe', 'NSW', '2205', 'AU', '', '', '', '', '', '', '', '', '', '', '', $email, 'TRACKADV', '', '', '', '', '', '', '', '', '', '0.5', '1', '1', '1', '', '', 'N', '', '', 'AU', '', 'Consumables', '', '', '', '1', '');

            array_map('remove_commas', $order_row);

            fputcsv($script_despatch_csv, $order_row);
        }
    }

	fclose($script_despatch_csv);
	echo 'All done!';
} else {
    die();
}

?>
