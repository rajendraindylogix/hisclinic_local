<?php
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url() . '?redirect_to=' . site_url());
    die();
}

$doctor = get_role('doctor');
$doctor->add_cap('gform_full_access');
$pharmacist = get_role('pharmacist');
$pharmacist->add_cap('gform_full_access');

global $wpdb;
global $woocommerce;

if (!empty($_POST['order_actions'])) {
	$prescribing_doctor = wp_get_current_user();
	// use drneha's user id for testing
	//$prescribing_doctor = get_user_by('id', 5611);
	$order_actions = $_POST['order_actions'];
	$prescribed_orders = 0;
	$held_orders = 0;
	foreach ($order_actions as $order_id => $order_action) {
		$order = wc_get_order($order_id);
		$order_status = $order->get_status();

		if ($order_action === 'prescribe' && $order_status != 'completed' && $order_status != 'scripted') {
			foreach ($order->get_items() as $item_id => $item) {
				$product_id = $item['product_id'];
				$dosage_instructions = get_field('dosage_instructions', $product_id);
				$requires_prescription = get_field('requires_prescription', $product_id);
				wc_update_order_item_meta($item_id, 'dosage_instructions', $dosage_instructions);
				wc_update_order_item_meta($item_id, 'requires_prescription', $requires_prescription);
			}
			update_field('field_5c91e1398bf00', $prescribing_doctor->ID, $order_id);
			$scripted_time = current_time('Y-m-d H:i:s', 0);
			update_field('field_5c91e1518bf01', $scripted_time, $order_id);
			$order->update_status('wc-scripted', 'Order was scripted by '.$prescribing_doctor->display_name);
			$prescribed_orders++;
		} elseif ($order_action === 'hold') {
			$order->update_status('wc-doctorquestions', 'Order was set to Doctor Questions by '.$prescribing_doctor->display_name);
			$held_orders++;
		}
	}
	echo '<span style="color: green;"><strong>'.$prescribed_orders.' orders prescribed. '.$held_orders.' orders held.</strong></span>';
}

$args = array(
        'post_type'      => 'shop_order',
        'post_status'    => array('processing'),
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC'
);
$processing_orders = wc_get_orders($args);

$args = array(
	'post_type'      => 'shop_order',
	'post_status'    => array('wc-doctorquestions'),
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC'
);
$doctorquestions_orders = wc_get_orders($args);

$prescription_orders = array_merge($processing_orders, $doctorquestions_orders);

//get_header();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<h1>Prescription requests</h1>
<?php
/*
if (empty($orders_waiting)) {
    echo '<h4 style="color: red; text-align: center !important;">No orders waiting to be scripted.</h4>';
    //die();
}
*/
?>
<div id="response-box"></div>
<?php
echo '<form action="'.admin_url().'admin.php?page=prescribe_page_new.php" method="post" name="multi_prescribe">';
echo '<input type="hidden" name="page" value="prescribe_page_new.php" />';
?>
<table class="striped">
    <thead>
        <th width="25%">Customer Details</th>
        <th width="25%">Questionnaire</th>
        <th width="25%">Assessment</th>
        <th width="25%">Contact</th>
    </thead>
    <tbody>
        <?php
        foreach ($prescription_orders as $order) {
			$order_id = $order->ID;
			$subscriptions = wcs_get_subscriptions_for_order($order_id);
			if (!empty($subscriptions)) {
				$is_subscription = true;
			} else {
				$is_subscription = false;
			}
            echo '<tr>';
            // customer name column
            $customer_id = $order->get_customer_id();
			$customer = new WC_Customer($customer_id);
			$form = get_medical_form_data($customer_id);
			$form_id = 'medical-forms-new';
			if (!empty($form)) {
				$questionnaire_status = '<span style="background-color: green; color: white;">MERGED MQ</span>';
			}
			/*
			if (empty($form)) {
				$form = get_user_meta($customer_id, 'medical-form-new', true);
				$form_id = 'medical-forms-new';
				$questionnaire_status = '&#10008; <span style="background-color: red; color: white;"><strong>old/unmerged MQ</strong></span>';
			}
			*/
			$form = $form['medical_form_details'];
			$question_count = count($form['medical_history']) + count($form['personal_information']) + count($form['sexual_activity']) + count($form['recommended_prescription']) + count($form['additional_details']);
			if ($question_count != 36) {
				$questionnaire_status = '<span style="background-color: red; color: white;">MISSING QUESTIONS => please check</span>';
			}

			$dob = $form['personal_information']['date_of_birth']['answer'];

            echo '<td style="text-align: center; font-weight: bold;">';
			echo '<a href="'.get_admin_url().'user-edit.php?user_id='.$customer_id.'">'.$customer->display_name.'</a><br>';
			if (substr($dob, -2) === '19' || strlen($dob) < 8) {
				echo '<span style="color: red;">DOB: '.$dob.'</span><br>';
			} elseif (strlen($dob) < 10) {
				echo '<span style="color: orange;">DOB: '.$dob.'</span><br>';
			} else {
				echo 'DOB: '.$dob.'<br>';
			}
			echo $customer->get_email().'<br>';

			$args = array(
				'customer_id' => $customer_id,
				'status' => 'completed',
				'orderby' => 'date',
				'order' => 'DESC',
			);
			$customer_orders = wc_get_orders($args);
			$order_count = count($customer_orders);
			if ($order_count > 0) {
				echo '<a style="color: green;" href="'.get_admin_url().'edit.php?post_status=all&post_type=shop_order&_customer_user='.$customer_id.'">'.$order_count.' previous completed order(s)</a>';
				$mostrecent_order = current($customer_orders);
				if (!empty($mostrecent_order->date_paid)) {
					$last_order_date = strtotime($mostrecent_order->date_paid);
					$today = (int)current_time('timestamp', 0);
					$days_ago = ($today - $last_order_date)/(60 * 60 * 24);
					if ($days_ago < 28) {
						echo '<br><span style="color: orange;">'.date('j M Y', $last_order_date).' ('.(int)$days_ago.' days ago)</span>';
					} else {
						echo '<br>'.date('j M Y', $last_order_date).' ('.(int)$days_ago.' days ago)';
					}
				}
			} else {
				echo '<i>No previous completed orders</i>';
			}
			echo '<br>Question count: '.$question_count;
            echo '</td>';

            // questionnaire column
			echo '<td style="text-align: center;">';
			$flagged = false;
			$allergies = '';
            if ($order_status === 'processing' || $order_status === 'doctorquestions') {
	            echo '<ul>';
				foreach ($form['medical_history'] as $key => $value) {
					switch ($key) {
						case 'herbs_description':
						case 'medical_condition_description':
							if ( $value['answer'] !== '' ) {
								$flagged = true;
								echo '<li>&bull; <strong>'.$value['question'].': '.$value['answer']. '</strong></li>';
							}
							break;

						case 'allergies_description':
							if ( $value['answer'] !== '' ) {
								$allergies = '<strong>' . $value['answer'] . '</strong>';
							}
							break;

						case 'heart_disease':
						case 'lightheadedness':
						case 'medical_condition':
						case 'medical_history':
						case 'allergies':
						case 'nitrate':
						case 'blood_pressure_diagnosis':
						case 'cardiovascular_symptoms':
						case 'heart_attack_past':
						case 'stroke_TIA':
						case 'conditions_1':
						case 'conditions_2':
						case 'herbs':
							if ( !in_array(strtolower($value['answer']), array('no', 'none', 'no, it was normal', 'no - i haven\'t had it checked')) ) {
								$flagged = true;
								echo '<li>&bull; <strong>'.$value['question'].': '.$value['answer']. '</strong></li>';
							}
							break;

						case 'blood_pressure_test':
							if ( !in_array(strtolower($value['answer']), array('yes', 'yes - it\'s been checked')) ) {
								$flagged = true;
								echo '<li>&bull; <strong>'.$value['question'].': '.$value['answer']. '</strong></li>';
							}
							break;

						case 'medical_history_desctiption':
							$medical_history = '';

							foreach ( $value['answer'] as $medical_value ) {
								if ( $medical_value['description'] !== '' ) {
									$flagged = true;
									$medical_history .= $medical_value['date'] . ' - ' . $medical_value['description'] . '<br>';
								}
							}

							if ( $medical_history !== '' ) {
								echo '<li>&bull; <strong>' . $value['question'] . '<br>' . $medical_history . '</strong></li>';
							}
							break;

					}
				}

				if ( isset($form['additional_details']['answer']) && $form['additional_details']['answer'] !== '' ) {
					$flagged = true;
					echo '<li>&bull; <strong>' . $form['additional_details']['question'] . ': ' . $form['additional_details']['answer'] . '</strong></li>';
				}
				echo '</ul>';
			}

			$flagged_style = ($flagged) ? 'style="background-color: red; color: white;"' : '';
			$questionnaire_message = ($flagged) ? 'PLEASE REVIEW QUESTIONNAIRE <br/>' : 'VIEW QUESTIONNAIRE <br/>';
			$view_questionnaire = '<a href="' . get_admin_url() . 'users.php?page=' . $form_id . '&user_id=' . $customer_id . '" ' . $flagged_style . '>'.$questionnaire_message.'</a>';
			echo '<strong>'.$view_questionnaire.'</strong>';

			echo $questionnaire_status;

			if ($allergies === '') {
				echo '<br><span style="color: green;"><strong>Allergies: Nil</strong></span>';
			} else {
				echo '<br><span style="background-color: red; color: white;"><strong>Allergies: '.$allergies.'</strong></span>';
			}

            echo '</td>';


            // Assessment

			$query = new WC_Product_Query( array(
		        'limit' => 10,
		        'orderby' => 'date',
		        'order' => 'DESC',
		        'return' => 'ids',
		    ) );
		    $products_all = $query->get_products();
     
            echo '<td style="text-align: center;">';
            $items = $order->get_items();
            foreach ( $items as $item ) {
			    $product_id = $item['product_id'];
			    $suggested_product = get_permalink( $product_id );
			}
            ?>
			<?php if ($order_count > 0) {
				echo "<span style='font-weight:700;'>Repeat Treatment</span>";
			} else { 
				echo "<span style='font-weight:700;'>Initial Consult</span>";
			}  ?>

			<select name="suggested_product_usr" id="suggested-product"  style="display: block" >
		        <option value=""><?php _e( 'Product', 'woocommerce' ); ?></option>
		        <?php foreach( $products_all as $ky => $product ) : ?>
		        <option <?php selected( $suggested_product, esc_url( get_permalink( $product ) ) ) ?> value="<?php echo esc_url( get_permalink( $product ) ) ?>"><?php echo esc_attr( get_the_title( $product ) ) ?></option>
		        <?php endforeach; ?>
		    </select>
		    <select name="qty" id="qty" style="display: block">
				<option value="">QTY</option>
				<?php
				for ($i=1; $i <= 20; $i++) { ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
				<?php }
				?>
			</select>

            <?php

            if ($order_status !== 'scripted') {
			echo	'<label>
					<input type="radio" class="with-gap prescribe blue" name="order_actions['.$order_id.']" value="prescribe" />
					<span><strong>Prescribe</strong></span>
					</label>
					<label>
					<input type="radio" class="with-gap hold red" name="order_actions['.$order_id.']" value="hold" />
					<span><strong>Hold</strong></span>
					</label>';
		    }
			echo '</td>';

            // echo '</td>';
            
            // action column
            echo '<td style="text-align: center;">';

            // echo $customer_id;
	?>
			
			<div id="hc-send-chat-message-backend_<?php echo $customer_id; ?>">
		        <div class="label-textarea">
		            <textarea name="dr_support_chat" id="msg-reply" placeholder="Type your message"></textarea>
		            <input type="hidden" name="action" value="hc_dr_support_send_mesage">
		            <input type="hidden" name="composer" value="admin">
		            <input type="hidden" name="user_id" value="<?php echo esc_attr( $customer_id ); ?>">
		        </div>
		        <button id="hc-send-chat-submit" class="btn btn-filled"><?php _e( 'Send Message', 'woocommerce' ); ?></button>
		    </div>
		    <a href="<?php echo get_admin_url().'/users.php?page=medical-forms-new&user_id='.$customer_id; ?>" class="btn btn-filled ">History</a>
	
			<script type="text/javascript">
				// $(document).ready(function() {
				// 	$( '#hc-send-chat-message-backend_<?php// echo $customer_id; ?>' ).on( 'submit', function(e) {
				//         e.preventDefault();
				//         data = $(this).serializeArray();
				//         // console.log ( data );
				//         $( '#hc-send-chat-submit' ).attr( 'disabled', 'disabled' ).text('Sending...');
				//         $.post(new_wp_paths.new_admin, data, function (data, textStatus, jqXHR) {
				//             // console.log(data);
				//             if (data.success) {
				//                 $( '#msg-reply' ).val('');
				//                 $('#hc-send-chat-submit').removeAttr( 'disabled' ).text('Send Message');
				//                 var template = wp.template('hc-chat-block');
				//                 // var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
				//                 $('#chat-blocks-wrap').append(template({ data: data }));
				//             } else {
				                
				//             }
				//         }, 'json');
				//     } );
				// });
			</script>

		    <?php
			echo '</td>';
            echo '</tr>';
		} ?>
    </tbody>
</table>
<?php
echo '<div class="fixed-action-btn"><button class="btn-large red" style="border-radius: 20px;">Prescribe</button></div>';
//echo '<button type="submit" style="float: right;" class="btn-large waves-effect waves-light orange">Prescribe selected</button>';
echo '</form>';
?>

<style>
th {
    text-align: center;
}
input[type="radio"].with-gap.prescribe:checked:before {
	background-color: #1E90FF;
}
input[type="radio"].with-gap.prescribe:checked+span:after, input[type="radio"].with-gap.prescribe:checked+span:before, input[type="radio"].with-gap.prescribe:checked+span:after {
	border: 2px solid #1E90FF;
}
input[type="radio"].with-gap.prescribe:checked+span:after, input[type="radio"].with-gap.prescribe:checked+span:after {
	background-color: #1E90FF;
}
input[type="radio"].with-gap.prescribe:checked+span:after, input[type="radio"].with-gap.prescribe:checked+span:before, input[type="radio"].with-gap.prescribe:checked+span:after {
    border: 2px solid #1E90FF;
}
input[type="radio"].with-gap.hold:checked:before {
	background-color: red;
}
input[type="radio"].with-gap.hold:checked+span:after, input[type="radio"].with-gap.hold:checked+span:before, input[type="radio"].with-gap.hold:checked+span:after {
	border: 2px solid red;
}
input[type="radio"].with-gap.hold:checked+span:after, input[type="radio"].with-gap.hold:checked+span:after {
	background-color: red;
}
input[type="radio"].with-gap.hold:checked+span:after, input[type="radio"].with-gap.hold:checked+span:before, input[type="radio"].with-gap.hold:checked+span:after {
    border: 2px solid red;
}

</style>
<script>
jQuery(document).ready(function(){
	jQuery('.fixed-action-btn').floatingActionButton();
});
</script>
<!--
<script>
$('form').submit(function() {
    event.preventDefault();
    alert();
    var form = $(this);
    var formData = form.serialize();

    $.ajax({
        type: 'GET',
        url: form.attr('action'),
        data: formData
    })
    .done(function(response) {
        $('#response-box').removeClass('error');
        $('#response-box').addClass('success');
        $('#response-box').text(response);
    })
    .fail(function(response) {
        $('#response-box').removeClass('success');
        $('#response-box').addClass('error');
        $('#response-box').text(data.responseText);
    });
});
</script>
-->
