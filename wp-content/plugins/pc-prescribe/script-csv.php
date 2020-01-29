<?php

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
}

global $wpdb;
global $woocommerce;

if (empty($_GET['order_id'])) {
    $args = array(
        'numberposts'     => -1,
        'orderby'   => 'date',
        'order'     => 'DESC',
        'post_type' => 'batch',
    );
    $batches = get_posts($args);

    // todo use a WP_List_table instead
    /*
    if (!class_exists('WP_List_Table')) {
        require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
    }
    */
    echo '<br /><span style="font-size: 16px; font-weight: bold;">Existing script batches:<br /><br />';
    echo
    '<table class="widefat fixed">
        <thead>
            <th>Batch number</th>
            <th>Batch time</th>
            <th>Number of orders</th>
            <th>Details</th>
            <th>Action</th>
        </thead>
        <tbody>';
        foreach ($batches as $batch) {
            echo '<tr>';
            $batch_number = $batch->ID;
            echo '<td>'.$batch_number.'</td>';
            $batch_time = $batch->post_date;
            $batch_time = date('Y-m-d H:i', strtotime($batch_time));
            echo '<td>'.$batch_time.'</td>';
            echo '<td>';
            $order_count = get_post_meta($batch_number, 'order_count', true);
            echo '<a href="'.home_url().'/batch-content?batch_number='.$batch_number.'">Order count: '.$order_count.'</a>';
            echo '</td>';
            $batch_content = $batch->post_content;
            echo '<td><div class="collapsible button"><a href="#">Expand</a><div class="collapsible content">'.$batch_content.'</div></div></td>';
            echo '<td>';
            echo '<form action="'.home_url().'/script-csv" method="get">';
            $order_ids = get_post_meta($batch_number, 'orders', true);
            foreach ($order_ids as $order_id) {
                echo '<input type="hidden" name="order_id[]" value="'.$order_id.'" />';
            }
            echo '<button type="submit" class="button elementor-button" style="background-color: #1E90FF; color: white;">Generate CSV</button>';
            echo '</form>';
            echo '<form action="'.home_url().'/script-despatch-csv" method="get">';
            $order_ids = get_post_meta($batch_number, 'orders', true);
            foreach ($order_ids as $order_id) {
                echo '<input type="hidden" name="order_id[]" value="'.$order_id.'" />';
            }
            echo '<button type="submit" class="button elementor-button" style="background-color: #F08080; color: white;">Generate despatch CSV</button>';
            echo '</form>';
            echo '<form action="'.home_url().'/script-pdf" method="get">';
                $order_ids = get_post_meta($batch_number, 'orders', true);
                foreach ($order_ids as $order_id) {
                    echo '<input type="hidden" name="order_id[]" value="'.$order_id.'" />';
                }
            echo '<button type="submit" class="button elementor-button" style="background-color: #9932CC; color: white;">View PDF</button>';
			echo '</form>';
			echo '<form action="'.home_url().'/q" method="get">';
            $order_ids = get_post_meta($batch_number, 'orders', true);
            foreach ($order_ids as $order_id) {
                echo '<input type="hidden" name="order_id[]" value="'.$order_id.'" />';
            }
            echo '<button type="submit" class="button elementor-button" style="background-color: #1E90FF; color: white;">Print MQs</button>';
			echo '</form>';
			echo '<form action="'.home_url().'/email-batch" method="get">';
			echo '<input type="hidden" name="batch_number" value="'.$batch_number.'" />';
			echo '<button type="submit" class="button elementor-button" style="background-color: green; color: white;">Email batch</button>';
			echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    echo '</tbody></table>';
    echo '</form>';
    echo
    "<style>
    .collapsible.content {
        display: none;
    }
    .collapsible.content.active {
        display: block;
    }
    </style>
    <script>
        $('.collapsible.button').on('click', function() {
            $(this).children().toggleClass('active');
        });
    </script>";
}

?>
