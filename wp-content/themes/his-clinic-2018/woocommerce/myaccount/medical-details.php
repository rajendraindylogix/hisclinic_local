<?php
/**
 * Medical Details page.
 */

$user_id = get_current_user_id();
if ( isset( $_POST['medical_form_details'] ) && ! empty( $_POST['medical_form_details'] ) ) {

    if ( ( isset( $_POST['action'] ) && 'hc_dashboard_save_user_updates' === $_POST['action'] ) ) {
        $saved_medical_form_data = get_medical_form_data($user_id);

        $medical_form_details    = isset( $saved_medical_form_data['medical_form_details'] ) && ! empty( $saved_medical_form_data['medical_form_details'] ) ? $saved_medical_form_data['medical_form_details'] : array();
        $updated_details         = isset( $_POST['medical_form_details'] ) && ! empty( $_POST['medical_form_details'] ) ? $_POST['medical_form_details'] : array();

        if (!empty($updated_details)) {            
            foreach ($medical_form_details as $field_group_key => $field_group) {
                foreach ( $field_group as $field_key => $field ) {
                    if ( isset( $updated_details[$field_group_key][$field_key] ) ) {
                        $question = isset($field['question']) ? $field['question'] : '';
                        $medical_form_details[$field_group_key][$field_key] = $updated_details[$field_group_key][$field_key];
                        $medical_form_details[$field_group_key][$field_key]['question'] = $question;
                    }
                }
            }
            
            $new_medical_array['medical_form_details'] = $medical_form_details;

            // print_r( $new_medical_array );
            
            // Save data to account
            save_medical_form_data($user_id, $new_medical_array);
            
            // Flag User
            update_user_meta( $user_id, 'approved', false );

			if ( (!get_user_meta( $user_id, 'mf-personal_information-updated', true ) || !get_user_meta( $user_id, 'mf-sexual_activity-updated', true ) || !get_user_meta( $user_id, 'mf-medical_history-updated', true ) ) && !get_user_meta( $user_id, 'suggested_product', true ) ) {
				if(
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_sildenafil']['answer'] !== 'Sildenafil' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['sildenafil_effective']['answer'] !== 'Yes' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_cialis']['answer'] === 'Cialis' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['cialis_effective']['answer'] === 'Yes' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_cialis_daily']['answer'] !== 'Daily Cialis' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['daily_cialis_effective']['answer'] !== 'Yes'
				) {
					$suggested_product = $new_medical_array['medical_form_details']['redirection_link']['cialis_redirection_link'];
				} else if (
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_sildenafil']['answer'] !== 'Sildenafil' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['sildenafil_effective']['answer'] !== 'Yes' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_cialis']['answer'] !== 'Cialis' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['cialis_effective']['answer'] !== 'Yes' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_cialis_daily']['answer'] === 'Daily Cialis' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['daily_cialis_effective']['answer'] === 'Yes'
				) {
					$suggested_product = $new_medical_array['medical_form_details']['redirection_link']['daily_cialis_redirection_link'];
				} else if (
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_sildenafil']['answer'] !== 'Sildenafil' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['sildenafil_effective']['answer'] !== 'Yes' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_cialis']['answer'] === 'Cialis' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['cialis_effective']['answer'] === 'Yes' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_cialis_daily']['answer'] !== 'Daily Cialis' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['daily_cialis_effective']['answer'] !== 'Yes'
				) {
					$suggested_product = $new_medical_array['medical_form_details']['redirection_link']['cialis_redirection_link'];
				} else if (
					$new_medical_array['medical_form_details']['recommended_prescription']['previous_use_sildenafil']['answer'] === 'Sildenafil' &&
					$new_medical_array['medical_form_details']['recommended_prescription']['sildenafil_effective']['answer'] !== 'Yes'
				) {
					$suggested_product = $new_medical_array['medical_form_details']['redirection_link']['cialis_redirection_link'];
				} else {
					$suggested_product = $new_medical_array['medical_form_details']['redirection_link']['sildenafil_redirection_link'];
				}
				
				update_user_meta( $user_id, 'suggested_product', $suggested_product );
			}

			update_user_meta( $user_id, 'mf-personal_information-updated', time() );
			update_user_meta( $user_id, 'mf-medical_history-updated', time() );
			update_user_meta( $user_id, 'mf-sexual_activity-updated', time() );
            ?>

            <div class="woocommerce-notices-wrapper">
                <div class="woocommerce-message" role="alert">
                    <?php _e( 'Medical form updated', 'woocommerce' ); ?>	
                </div>
            </div>
            
            <?php
        }

    }

}

if (!$form_data = get_medical_form_data($user_id)) {
    echo __( "You have not filled in your Medical Details. Please fill in your details in the forms below and save your changes.", "woocommerce" );
}

$personal_information_updated = get_user_meta( $user_id, 'mf-personal_information-updated', true );
$sexual_activity_updated = get_user_meta( $user_id, 'mf-sexual_activity-updated', true );
$medical_history_updated = get_user_meta( $user_id, 'mf-medical_history-updated', true );

$medical_form_page = hisclinic_get_page_id_by_page_name( 'medical-form' );
$medical_form_details = isset( $form_data['medical_form_details'] ) && ! empty( $form_data['medical_form_details'] ) ? $form_data['medical_form_details'] : array();

$personal_information = isset( $medical_form_details['personal_information'] ) ? $medical_form_details['personal_information'] : array();
$sexual_activity      = isset( $medical_form_details['sexual_activity'] ) ? $medical_form_details['sexual_activity'] : array();
$medical_history      = isset( $medical_form_details['medical_history'] ) ? $medical_form_details['medical_history'] : array();

$dob_answer = isset( $personal_information['date_of_birth']['answer'] ) ? $personal_information['date_of_birth']['answer'] : '';
$valid_dob = valid_date_of_birth($user_id);
?>

<div class="medical-details-info">
	<h2>You can manage your medical details here.</h2>

    <?php
        if ( !$personal_information_updated || !$sexual_activity_updated || !$medical_history_updated || !$valid_dob) {
            echo '<p><strong>Important Information:</strong><br>We’ve made some changes to our medical questionnare to ensure that you’re prescribed the correct prescription for your needs. You are required to confirm your details and answer some new questions before you’re able to begin a new order. Please confirm the following information and <strong><u>re-save your details for each section:</u></strong></p><ul>';

            if ( !$personal_information_updated ) {
                echo '<li>Personal Information (please confirm and re-save)</li>';
            }

            if ( !$sexual_activity_updated ) {
                echo '<li>Sexual Activity  (please confirm and re-save)</li>';
            }
            
            if ( !$medical_history_updated ) {
                echo '<li>Medical History (please confirm and re-save)</li>';
            }
            
            if ( !$valid_dob ) {
                echo '<li>Date of birth (please confirm and re-save)</li>';
            }

            echo '</ul>';
        }
    ?>
</div>

<?php if (!empty($_GET['redirected'])): ?>
    <!-- Popup if someone's been redirected here because of missing information -->
    <div class="generic-popup need-to-update-popup">
        <div class="inner">
            <h2 class="h2">You need to update your medical details to order<span class="pink">.</span></h2>

            <?php 
                if ($_GET['redirected'] == 'dob') {
                    $content = "The provided date of birth is incorrect, please re-enter it and save your changes.";
                } else {
                    $content = "We’ve made some changes to our medical questionnare to ensure that you’re prescribed the correct treatment for your needs. You are required to confirm your details and answer some new questions before you’re able to begin a new order.";
                }
            ?>

            <p class="large mb20"><?php echo $content ?></p>
            
            <div class="mt20">
                <a href="#" class="btn btn-filled close-popup">Update My Details</a>
            </div>
        </div>
    </div>
<?php endif ?>

        <div id="hc-account-dashbrd-md" class="accordions">    
            <div class="form-errors" id="account-medical-form-errors">
                <span class="pink">ERROR 😱:</span> <br>
                It looks like there are some errors! Please check these questions and re-save your details:<br>
                <ul class="fields"></ul>
            </div>

            <form method="POST" action="<?php echo home_url('my-account/medical-details') ?>" id="account-medical-form">
                <input type="hidden" name="action" value="hc_dashboard_save_user_updates">

                <div class="accordion">
                    <div class="title"><?php _e( 'Personal Information', 'woocommerce' ); ?></div>
                    <div class="box">
                        <!--================================
                                        Fourth Info Step
                            ================================-->
                        <?php 
                            $step_4_height_label = get_field('step_4_height_label', $medical_form_page);
                            $step_4_height_label = isset($step_4_height_label) ? sanitize_text_field($step_4_height_label) : 'Height (Centimeters)';
                            $step_4_dont_know_text = get_field('step_4_dont_know_text', $medical_form_page);
                            $step_4_dont_know_text = isset($step_4_dont_know_text) ? sanitize_text_field($step_4_dont_know_text) : 'I don\'t know';
                            $step_4_weight_label = get_field('step_4_weight_label', $medical_form_page);
                            $step_4_weight_label = isset($step_4_weight_label) ? sanitize_text_field($step_4_weight_label) : 'Weight (Kilograms)';
                            $step_4_weight_dont_know_text = get_field('step_4_weight_dont_know_text', $medical_form_page);
                            $step_4_weight_dont_know_text = isset($step_4_weight_dont_know_text) ? sanitize_text_field($step_4_weight_dont_know_text) : 'I don\'t know';
                        ?>
                            <div class="mf-step__hw">
                                <h4 class="form-title"><?php _e( 'Your Date of Birth', 'woocommerce' ); ?></h4>
                                
                                <?php if ($valid_dob): ?>
                                    <span><?php echo sprintf( 'Your date of birth is: <strong>%1$s</strong>', $dob_answer ); ?></span><br>
                                    <span><?php _e( 'Please contact support if there is an error with your date of birth details.', 'woocommerce' ); ?></span>
                                <?php else: ?>
                                    <div class="mf-height">
                                        <div class="mf-field" data-question="Date of birth">
                                            <div class="animate-input">
                                                <input type="tel" name="medical_form_details[personal_information][date_of_birth][answer]" id="date_of_birth" class="mask-date" placeholder="dd/mm/yyyy" minlength="10" maxlength="10" required>
                                                <label class="field-label" for="date_of_birth">Your date of birth seems to be incorrect, please re enter it.</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif ?>
                            </div>

                                <div class="mf-step" mf-step="4">
                                    <div class="mf-step__item mf-step__hw">
                                        <div class="animate-inputs">
                                            <div class="mf-height"> 

                                                <div class="mf-field" data-question="<?php _e($step_4_height_label); ?>">
                                                    <div class="animate-input">
                                                        <input min="70" max="250" type="number" value="<?php echo esc_attr( $personal_information['height']['answer'] ); ?>" name="medical_form_details[personal_information][height][answer]" id="height">
                                                        <label class="field-label" for="height"><?php _e($step_4_height_label); ?></label>
                                                    </div>
                                                </div>

                                                <div class="mf-field" data-question="<?php _e($step_4_dont_know_text); ?>">
                                                    <div class="chk-btn-wrap">
                                                        <input type="checkbox"  <?php echo isset( $personal_information['height_no_info']['answer'] ) && $personal_information['height_no_info']['answer'] === $step_4_dont_know_text ? 'checked="checked"' : ''; ?> name="medical_form_details[personal_information][height_no_info][answer]" value="<?php _e($step_4_dont_know_text); ?>" id="heightchk">
                                                        <label class="field-label" for="heightchk"><?php _e($step_4_dont_know_text); ?></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mf-weight">
                                                <div class="mf-field" data-question="<?php _e($step_4_weight_label); ?>">
                                                    <div class="animate-input">
                                                        <input min="40" max="250" type="number"  value="<?php echo esc_attr( $personal_information['weight']['answer'] ); ?>" name="medical_form_details[personal_information][weight][answer]" id="weight">
                                                        <label class="field-label" for="weight"><?php _e($step_4_weight_label); ?></label>
                                                    </div>
                                                </div>
                                                <div class="mf-field" data-question="<?php _e($step_4_weight_dont_know_text); ?>">
                                                    <div class="chk-btn-wrap">
                                                        <input type="checkbox" <?php echo isset( $personal_information['weight_no_info']['answer'] ) &&  $personal_information['weight_no_info']['answer'] === $step_4_weight_dont_know_text ? 'checked="checked"' : ''; ?> name="medical_form_details[personal_information][weight_no_info][answer]" value="<?php _e($step_4_weight_dont_know_text); ?>" id="weightchk">
                                                        <label class="field-label" for="weightchk"><?php _e($step_4_weight_dont_know_text); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!--================================
                                                Fifth Info Step
                                    ================================-->
                                <div class="mf-step" mf-step="5">
                                    <div class="mf-step__item mf-step__diet">
                                        <div class="mf-field" data-question="Your Main Diet">
                                            <label id="medical_form_details[personal_information][diet][answer]-error" class="error" for="medical_form_details[personal_information][diet][answer]"></label>
                                            <div class="text-center">
                                                <h2><?php _e('Your Main Diet', 'woocommerce'); ?></h2>
                                            </div>

                                            <?php 
                                    
                                            if ( have_rows( 'step_5_options', $medical_form_page ) ) : ?>
                                                <div class="img-radio">
                                                    <?php
                                                    $counter = 0;
                                                    while ( have_rows( 'step_5_options', $medical_form_page ) ) : the_row();
                                                        $step_5_option_image = get_sub_field( 'step_5_option_image', $medical_form_page );
                                                        $step_5_option_text  = get_sub_field( 'step_5_option_text', $medical_form_page );
                                                        ?>
                                                        <div class="img-radio-item">
                                                            <input <?php echo isset( $personal_information['diet']['answer'] ) && $personal_information['diet']['answer'] === $step_5_option_text ? 'checked="checked"' : ''; ?> type="radio" name="medical_form_details[personal_information][diet][answer]" value="<?php _e($step_5_option_text); ?>" id="food-<?php _e($counter); ?>">
                                                            <label for="food-<?php _e($counter); ?>"> <img class="svg" src="<?php _e($step_5_option_image); ?>" /><span><?php _e($step_5_option_text); ?></span></label>
                                                        </div>
                                                        <?php
                                                        $counter++;
                                                    endwhile;
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                        <?php
                        ?>
                    </div>
                </div>
                <div class="accordion">
                    <div class="title"><?php _e( 'Sexual Activity', 'woocommerce' ); ?></div>
                    <div class="box">
                        <!--================================
                        Fiftha Info Step
                        ================================-->
                        <div class="mf-step" mf-step="5a">
                            <div class="mf-step__item mf-step__maintain">
                                <div class="mf-field" data-question="<?php _e( 'How often do you anticipate using this treatment per month?', 'woocommerce' ); ?>">
                                    <label id="medical_form_details[sexual_activity][uses][answer]-error" class="error" for="medical_form_details[sexual_activity][uses][answer]"></label>
                                    <div class="text-center">
                                        <h2><?php _e( 'How often do you anticipate using this treatment per month?', 'woocommerce' ); ?></h2>
                                    </div>
                                    <?php if (have_rows('step_26_options', $medical_form_page)) : ?>
                                        <div class="radio-btn-wrap">
                                            <?php
                                            $counter = 0;
                                            while (have_rows('step_26_options', $medical_form_page)) : the_row();
                                                $step_26_option = get_sub_field('step_26_option', $medical_form_page);

                                                ?>
                                                <div class="radio-btn">
                                                    <input type="radio" <?php isset( $sexual_activity['uses']['answer'] ) ? checked( $sexual_activity['uses']['answer'], $step_26_option ) : ''; ?> name="medical_form_details[sexual_activity][uses][answer]" value="<?php _e($step_26_option); ?>" id="uses-<?php _e($counter); ?>">
                                                    <label for="uses-<?php _e($counter); ?>"><?php _e($step_26_option); ?></label>
                                                </div>
                                                <?php
                                                $counter++;
                                            endwhile;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!--================================
                            Sixth Info Step
                        ================================-->
                        <div class="mf-step" mf-step="6">
                            <div class="mf-step__item mf-step__maintain">
                                <div class="mf-field" data-question="<?php _e( 'How often do you have a problem getting or maintaining an erection?', 'woocommerce' ); ?>">
                                    <label id="medical_form_details[sexual_activity][erection][answer]-error" class="error" for="medical_form_details[sexual_activity][erection][answer]"></label>
                                    <div class="text-center">
                                        <h2><?php _e( 'How often do you have a problem getting or maintaining an erection?', 'woocommerce' ); ?></h2>
                                    </div>

                                    <?php if (have_rows('step_6_options', $medical_form_page)) : ?>
                                        <div class="radio-btn-wrap">
                                            <?php
                                            $counter = 0;
                                            while (have_rows('step_6_options', $medical_form_page)) : the_row();
                                                $step_6_options_text = get_sub_field('step_6_options_text', $medical_form_page);

                                                ?>
                                                <div class="radio-btn">
                                                    <input type="radio" <?php isset( $sexual_activity['erection']['answer'] ) ? checked( $sexual_activity['erection']['answer'], $step_6_options_text ) : ''; ?> name="medical_form_details[sexual_activity][erection][answer]" value="<?php _e($step_6_options_text); ?>" id="erection-<?php _e($counter); ?>">
                                                    <label for="erection-<?php _e($counter); ?>"><?php _e($step_6_options_text); ?></label>
                                                </div>
                                                <?php
                                                $counter++;
                                            endwhile;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                    $step_8_question = get_field('step_8_question', $medical_form_page);
                    $step_8_question = isset($step_8_question) ? sanitize_text_field($step_8_question) : 'Do you have, or have you ever had, Heart Disease?';
                    $step_8_yes_text = get_field('step_8_yes_text', $medical_form_page);
                    $step_8_yes_text = isset($step_8_yes_text) ? sanitize_text_field($step_8_yes_text) : 'Yes';
                    $step_8_no_text  = get_field('step_8_no_text', $medical_form_page);
                    $step_8_no_text  = isset($step_8_no_text) ? sanitize_text_field($step_8_no_text) : 'No';


            ?>
                <div class="accordion">
                    <div class="title"><?php _e( 'Medical History', 'woocommerce' ); ?></div>
                    <div class="box">
                        <!-- Heart disease question -->
                        <div class="mf-step" mf-step="7.1">
                            <div class="mf-step__item mf-step__yesno">
                                <div class="mf-field" data-question="<?php _e($step_8_question); ?>">
                                    <label id="medical_form_details[medical_history][heart_disease][answer]-error" class="error" for="medical_form_details[medical_history][heart_disease][answer]"></label>
                                    <div class="text-center">
                                        <h2 class="field-label"><?php _e($step_8_question); ?></h2>
                                    </div>

                                    <div class="radio-btn-wrap">
                                        <div class="radio-btn">
                                            <input type="radio" <?php checked( strtolower($medical_history['heart_disease']['answer']), 'yes' ); ?> name="medical_form_details[medical_history][heart_disease][answer]" value="Yes" id="yes7_1">
                                            <label for="yes7_1" goto-step="7.3"><?php _e($step_8_yes_text); ?></label>
                                        </div>
                                        <div class="radio-btn">
                                            <input type="radio" <?php checked( in_array(strtolower($medical_history['heart_disease']['answer']), array('no', 'none')), true ); ?> name="medical_form_details[medical_history][heart_disease][answer]" value="No" id="no7_1">
                                            <label for="no7_1" goto-step="8"><?php _e($step_8_no_text); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Heart disease question -->

                    <!--================================
                        Ninth Info Step
                        ================================-->
                        <div class="mf-step" mf-step="9">
                            <div class="mf-step__item mf-step__yesno">
                                <div class="mf-field" data-question="<?php _e( 'Do you have any past or ongoing medical conditions?', 'woocommerce' ); ?>">
                                    <label id="medical_form_details[medical_history][medical_condition][answer]-error" class="error" for="medical_form_details[medical_history][medical_condition][answer]"></label>
                                    <div class="text-center">
                                        <h2><?php _e( 'Do you have any past or ongoing medical conditions?', 'woocommerce' ); ?></h2>
                                    </div>
                                    <?php 
                                        $step_12_question = get_field('step_12_question', $medical_form_page);
                                        $step_12_question = isset($step_12_question) ? sanitize_text_field($step_12_question) : 'Do you have any health conditions or a history of prior surgeries?';
                                        $step_12_yes_text = get_field('step_12_yes_text', $medical_form_page);
                                        $step_12_yes_text = isset($step_12_yes_text) ? sanitize_text_field($step_12_yes_text) : 'Yes';
                                        $step_12_no_text  = get_field('step_12_no_text', $medical_form_page);
                                        $step_12_no_text  = isset($step_12_no_text) ? sanitize_text_field($step_12_no_text) : 'Yes';
                                    ?>

                                    <div class="radio-btn-wrap">
                                        <div class="radio-btn">
                                            <input class="yes" <?php checked( strtolower($medical_history['medical_condition']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][medical_condition][answer]" value="Yes" id="yes9">
                                            <label class="toggle-below-yes" for="yes9"><?php _e($step_12_yes_text); ?></label>
                                        </div>
                                        <div class="radio-btn">
                                            <input class="no" <?php checked( in_array(strtolower($medical_history['medical_condition']['answer']), array('no', 'none')), true ); ?> data-skip="10" type="radio" name="medical_form_details[medical_history][medical_condition][answer]" value="None" id="no9">
                                            <label class="toggle-below-no" for="no9"><?php _e($step_12_no_text); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--================================
                            Ninth Sub Steps
                        ================================-->
                        <div class="mf-step animate-textarea-wrap textarea-toggle-step" mf-step="9.a" <?php echo 'yes' === strtolower($medical_history['medical_condition']['answer']) ? 'style="display:flex;"' : 'style="display:none;"'; ?>>
                        
                        <?php 

                        // var_dump( $medical_history['medical_condition']['answer'] );

                            $step_12_a_question = get_field('step_12_a_question', $medical_form_page);
                            $step_12_a_question = isset($step_12_a_question) ? sanitize_text_field($step_12_a_question) : 'Please provide as much detail as possible about your ongoing medical conditions:';
                            $step_12_a_label = get_field('step_12_a_label', $medical_form_page);
                            $step_12_a_label = isset($step_12_a_label) ? sanitize_text_field($step_12_a_label) : 'Ongoing Medical Conditions';
                            $step_12_a_placeholder_text = get_field('step_12_a_placeholder_text', $medical_form_page);
                            $step_12_a_placeholder_text = isset($step_12_a_placeholder_text) ? sanitize_text_field($step_12_a_placeholder_text) : 'Please provide details';
                            $step_12_a_button_text = get_field('step_12_a_button_text', $medical_form_page);
                            $step_12_a_button_text = isset($step_12_a_button_text) ? sanitize_text_field($step_12_a_button_text) : 'Next';

                        ?>
                            <div class="mf-step__item mf-step__yesno">
                                <div class="mf-field" data-question="<?php _e( 'Do you have any past or ongoing medical conditions? - Details', 'woocommerce' ); ?>">

                                    <div class="animate-inputs animate-textarea">
                                        <div class="animate-input">
                                            <textarea class="validate-for-next" type="text" name="medical_form_details[medical_history][medical_condition_description][answer]" id="medical-conditions" placeholder="<?php _e($step_12_a_placeholder_text); ?>"><?php echo isset( $medical_history['medical_condition_description']['answer'] ) ? $medical_history['medical_condition_description']['answer'] : ''; ?></textarea>
                                            <!-- <label for="medical-conditions"><?php _e($step_12_a_label); ?></label> -->
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="mf-step end-step-validate" mf-step="9.b">
                            <div class="mf-step__item mf-step__yesno">
                                <div class="mf-field" data-question="<?php _e('Do you have a history of hospital admission or surgeries?', 'woocommerce'); ?>">
                                    <label id="medical_form_details[medical_history][medical_history][answer]-error" class="error" for="medical_form_details[medical_history][medical_history][answer]"></label>
                                    <div class="text-center">
                                        <h2><?php _e('Do you have a history of hospital admission or surgeries?', 'woocommerce'); ?></h2>
                                    </div>

                                    <?php 
                                        $step_12_b_question = get_field('step_12_b_question', $medical_form_page);
                                        $step_12_b_question = isset($step_12_b_question) ? sanitize_text_field($step_12_b_question) : 'Please provide details about your history of hospital admission or surgery:';
                                        $step_12_b_yes_text = get_field('step_12_b_yes_text', $medical_form_page);
                                        $step_12_b_yes_text = isset($step_12_b_yes_text) ? sanitize_text_field($step_12_b_yes_text) : 'Yes';
                                        $step_12_b_no_text = get_field('step_12_b_no_text', $medical_form_page);
                                        $step_12_b_no_text = isset($step_12_b_no_text) ? sanitize_text_field($step_12_b_no_text) : 'No';
                                    ?>

                                    <div class="radio-btn-wrap">
                                        <div class="radio-btn">
                                            <input class="yes" <?php checked( strtolower($medical_history['medical_history']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][medical_history][answer]" value="Yes" id="yes9b">
                                            <label  class="toggle-below-yes" for="yes9b"><?php _e($step_12_b_yes_text); ?></label>
                                        </div>
                                        <div class="radio-btn">
                                            <input class="no" <?php checked( in_array(strtolower($medical_history['medical_history']['answer']), array('no', 'none')), true ); ?> data-skip="10" type="radio" name="medical_form_details[medical_history][medical_history][answer]" value="None" id="no9b">
                                            <label  class="toggle-below-no" for="no9b" goto-step="9.d"><?php _e($step_12_b_no_text); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mf-step textarea-toggle-step medical-history-description" mf-step="9.c" <?php echo 'yes' === strtolower($medical_history['medical_history']['answer']) ? 'style="display:flex;"' : 'style="display:none;"'; ?>>

                            <?php 

                                $past_admission_question = get_field('past_admission_question', $medical_form_page);
                                $past_admission_question = isset($past_admission_question) ? sanitize_text_field($past_admission_question) : 'Please provide details about your history of hospital admission or surgery:';
                                $step_12_b_label = get_field('step_12_b_label', $medical_form_page);
                                $step_12_b_label = isset($step_12_b_label) ? sanitize_text_field($step_12_b_label) : 'Date of hospital admission or surgery';
                                $step_12_b_description = get_field('step_12_b_description', $medical_form_page);
                                $step_12_b_description = isset($step_12_b_description) ? sanitize_text_field($step_12_b_description) : 'Details about your hospital admission or surgery';
                            
                            ?>

                            <div class="mf-step__item mf-step__yesno">
                                <div class="mf-field" data-question="<?php _e('Do you have a history of hospital admission or surgeries?', 'woocommerce'); ?>">

                                    <?php if ( ! empty( $medical_history['medical_history_desctiption']['answer'] ) ) :
                                        
                                        foreach( $medical_history['medical_history_desctiption']['answer'] as $key => $answer ) :    
                                            $date = isset( $medical_history['medical_history_desctiption']['answer'][$key]['date'] ) ? esc_attr( $medical_history['medical_history_desctiption']['answer'][$key]['date'] ) : '';
                                            
                                            if (strlen($date) == 10) {
                                                $parts = explode('/', $date);
                                                
                                                if (count($parts)) {
                                                    $date = $parts[count($parts) - 1];
                                                }
                                            }
                                    ?>

                                            <div class="repeat-block">
                                                <div class="animate-inputs animate-textarea">
                                                    <div class="animate-input">
                                                        <!-- <input type="date" name="date" id="dob"> -->
                                                        <input type="tel" value="<?php echo $date ?>" name="medical_form_details[medical_history][medical_history_desctiption][answer][<?php echo esc_attr( $key ); ?>][date]" id="date9.c" placeholder="YYYY" maxlength="4">
                                                        <!-- <label for="date9.c"><?php _e($step_12_b_label); ?></label> -->
                                                    </div>
                                                    <div class="animate-input">
                                                        <textarea type="text" name="medical_form_details[medical_history][medical_history_desctiption][answer][<?php echo esc_attr( $key ); ?>][description]" id="textarea9.c" placeholder="Please provide details"><?php echo isset( $medical_history['medical_history_desctiption']['answer'][$key]['description'] ) ? esc_attr( $medical_history['medical_history_desctiption']['answer'][$key]['description'] ) : ''; ?></textarea>
                                                        <!-- <label for="textarea9.c"><?php _e($step_12_b_description); ?></label> -->
                                                    </div>
                                                </div>
                                                <div class="remove-item"> <span>REMOVE</span> </div>
                                            </div>

                                    <?php 
                                        endforeach;
                                    else : ?>

                                        <div class="repeat-block">
                                            <div class="animate-inputs animate-textarea">
                                                <div class="animate-input">
                                                    <!-- <input type="date" name="date" id="dob"> -->
                                                    <input type="tel" name="medical_form_details[medical_history][medical_history_desctiption][answer][a][date]" id="date9.c" placeholder="YYYY" maxlength="4">
                                                    <!-- <label for="date9.c"><?php _e($step_12_b_label); ?></label> -->
                                                </div>
                                                <div class="animate-input">
                                                    <textarea type="text" name="medical_form_details[medical_history][medical_history_desctiption][answer][a][description]" id="textarea9.c" placeholder="Please provide details"></textarea>
                                                    <!-- <label for="textarea9.c"><?php _e($step_12_b_description); ?></label> -->
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <div id="add-more">
                                        <span>ADD MORE</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="mf-step end-step-validate" mf-step="9.d">
                            <div class="mf-step__item mf-step__yesno">
                                <?php 
                                    $step_12_c_question = get_field('step_12_c_question', $medical_form_page);
                                    $step_12_c_question = isset($step_12_c_question) ? sanitize_text_field($step_12_c_question) : 'Do you have any allergies?';
                                    $step_12_c_yes_text = get_field('step_12_c_yes_text', $medical_form_page);
                                    $step_12_c_yes_text = isset($step_12_c_yes_text) ? sanitize_text_field($step_12_c_yes_text) : 'Yes';
                                    $step_12_c_no_text = get_field('step_12_c_no_text', $medical_form_page);
                                    $step_12_c_no_text = isset($step_12_c_no_text) ? sanitize_text_field($step_12_c_no_text) : 'No';
                                ?>

                                <div class="mf-field" data-question="<?php _e($step_12_c_question); ?>">
                                    <label id="medical_form_details[medical_history][allergies][answer]-error" class="error" for="medical_form_details[medical_history][allergies][answer]"></label>
                                    <div class="text-center">
                                        <h2 class="field-label"><?php _e($step_12_c_question); ?></h2>
                                    </div>

                                    <div class="radio-btn-wrap">
                                        <div class="radio-btn">
                                            <input <?php checked( strtolower($medical_history['allergies']['answer']), 'yes' ); ?> class="yes" type="radio" name="medical_form_details[medical_history][allergies][answer]" value="Yes" id="yes9d">
                                            <label  class="toggle-below-yes" for="yes9d"><?php _e($step_12_c_yes_text); ?></label>
                                        </div>
                                        <div class="radio-btn">
                                            <input <?php checked( in_array(strtolower($medical_history['allergies']['answer']), array('no', 'none')), true ); ?> class="no" data-skip="10" type="radio" name="medical_form_details[medical_history][allergies][answer]" value="None" id="no9d">
                                            <label  class="toggle-below-no" for="no9d" goto-step="9.1"><?php _e($step_12_c_no_text); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mf-step textarea-toggle-step" mf-step="9.e" <?php echo 'yes' === strtolower($medical_history['allergies']['answer']) ? 'style="display:flex;"' : 'style="display:none;"'; ?>>

                            <?php 

                                $allergies_description_text = get_field('allergies_description_text', $medical_form_page);
                                $allergies_description_text = isset($allergies_description_text) ? sanitize_text_field($allergies_description_text) : 'Please provide as much detail as possible about your allergies:';
                                $allergies_label = get_field('allergies_label', $medical_form_page);
                                $allergies_label = isset($allergies_label) ? sanitize_text_field($allergies_label) : 'Your allergies';
                                $allergies_next_button_text = get_field('allergies_next_button_text', $medical_form_page);
                                $allergies_next_button_text = isset($allergies_next_button_text) ? sanitize_text_field($allergies_next_button_text) : 'Next';
                            
                            ?>

                            <div class="mf-step__item mf-step__yesno">
                                <div class="mf-field" data-question="<?php _e($step_12_c_question); ?>">
                                    <div class="animate-inputs animate-textarea">
                                        <div class="animate-input">
                                            <textarea class="validate-for-next" type="text" name="medical_form_details[medical_history][allergies_description][answer]" id="allergies" placeholder="Please provide details"><?php echo esc_html( $medical_history['allergies_description']['answer'] ) ?></textarea>
                                            <!-- <label for="allergies"><?php _e($allergies_label); ?></label> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!--================================
                                            Ninth.1 Info Step
                                ================================-->
                            <div class="mf-step" mf-step="9.1">
                                <div class="mf-step__item mf-step__yesno">
                                <?php 

                                    $step_13_question = get_field('step_13_question', $medical_form_page);
                                    $step_13_question = isset($step_13_question) ? sanitize_text_field($step_13_question) : 'Are you currently taking any Nitrate medications (GTN patch, Mononitrates, etc)?';
                                    $step_13_yes_text = get_field('step_13_yes_text', $medical_form_page);
                                    $step_13_yes_text = isset($step_13_yes_text) ? sanitize_text_field($step_13_yes_text) : 'Yes';
                                    $step_13_no_text  = get_field('step_13_no_text', $medical_form_page);
                                    $step_13_no_text  = isset($step_13_no_text) ? sanitize_text_field($step_13_no_text) : 'No';
                                    
                                ?>
                                    <div class="mf-field" data-question="<?php _e($step_13_question); ?>">
                                        <label id="medical_form_details[medical_history][nitrate][answer]-error" class="error" for="medical_form_details[medical_history][nitrate][answer]"></label>
                                        <div class="text-center">
                                            <h2 class="field-label"><?php _e($step_13_question); ?></h2>
                                        </div>

                                        <div class="radio-btn-wrap">
                                            <div class="radio-btn">
                                                <input <?php checked( strtolower($medical_history['nitrate']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][nitrate][answer]" value="Yes" id="yes9a">
                                                <label for="yes9a" goto-step="11.1"><?php _e($step_13_yes_text); ?></label>
                                            </div>
                                            <div class="radio-btn">
                                                <input <?php checked( in_array(strtolower($medical_history['nitrate']['answer']), array('no', 'none')), true ); ?> type="radio" name="medical_form_details[medical_history][nitrate][answer]" value="None" id="no9a">
                                                <label for="no9a"><?php _e($step_13_no_text); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--================================
                                        Ninth.2 Info Step
                            ================================-->
                            <div class="mf-step animate-textarea-wrap" mf-step="9.2">
                                <div class="mf-step__item mf-step__yesno">
                                <?php 
                                    $herbal_supplements_question = get_field('herbal_supplements_question', $medical_form_page);
                                    $herbal_supplements_question = isset($herbal_supplements_question) ? sanitize_text_field($herbal_supplements_question) : 'Are you taking any other medications, herbs or supplements?';
                                    $herbal_supplements_yes_text = get_field('herbal_supplements_yes_text', $medical_form_page);
                                    $herbal_supplements_yes_text = isset($herbal_supplements_yes_text) ? sanitize_text_field($herbal_supplements_yes_text) : 'Yes';
                                    $herbal_supplements_no_text = get_field('herbal_supplements_no_text', $medical_form_page);
                                    $herbal_supplements_no_text = isset($herbal_supplements_no_text) ? sanitize_text_field($herbal_supplements_no_text) : 'No';
                                ?>
                                    <div class="mf-field" data-question="<?php _e($herbal_supplements_question); ?>">
                                        <label id="medical_form_details[medical_history][herbs][answer]-error" class="error" for="medical_form_details[medical_history][herbs][answer]"></label>
                                        <div class="text-center">
                                            <h2 class="field-label"><?php _e($herbal_supplements_question); ?></h2>
                                        </div>

                                        <div class="radio-btn-wrap">
                                            <div class="radio-btn">
                                                <input <?php checked( strtolower($medical_history['herbs']['answer']), 'yes' ); ?> class="yes" type="radio" name="medical_form_details[medical_history][herbs][answer]" value="Yes" id="yes9_22">
                                                <label class="toggle-below-yes" for="yes9_22" goto-step="9.2a"><?php _e($herbal_supplements_yes_text); ?></label>
                                            </div>
                                            <div class="radio-btn">
                                                <input <?php checked( in_array(strtolower($medical_history['herbs']['answer']), array('no', 'none')), true ); ?> class="no" type="radio" name="medical_form_details[medical_history][herbs][answer]" value="None" 3 id="no9_23">
                                                <label class="toggle-below-no" for="no9_23"><?php _e($herbal_supplements_no_text); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mf-step animate-textarea-wrap textarea-toggle-step" mf-step="9.2a" <?php echo 'yes' === strtolower($medical_history['herbs']['answer']) ? 'style="display:flex;"' : 'style="display:none;"'; ?>>                                
                                <?php 
                                    $herbal_supplements_description = get_field('herbal_supplements_description', $medical_form_page);
                                    $herbal_supplements_description = isset($herbal_supplements_description) ? sanitize_text_field($herbal_supplements_description) : 'Please provide details of your medication, herbs or supplements:';
                                    $herbal_supplements_label_text = get_field('herbal_supplements_label_text', $medical_form_page);
                                    $herbal_supplements_label_text = isset($herbal_supplements_label_text) ? sanitize_text_field($herbal_supplements_label_text) : 'Medication Details';
                                ?>

                                <div class="mf-step__item mf-step__yesno">
                                    <div class="mf-field" data-question="<?php _e($herbal_supplements_description); ?>">
                                        <!-- <div class="text-center">
                                            <h2 class="field-label"><?php _e($herbal_supplements_description); ?></h2>
                                        </div> -->

                                        <div class="animate-inputs animate-textarea">
                                            <div class="animate-input">
                                                <textarea class="validate-for-next" type="text" name="medical_form_details[medical_history][herbs_description][answer]" id="herbal_suppliments" placeholder="Please provide details"><?php echo esc_html( $medical_history['herbs_description']['answer'] ); ?></textarea>
                                                <!-- <label for="herbal_suppliments"><?php _e($herbal_supplements_label_text); ?></label> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <!--================================
                        Ninth.3 Info Step
                    ================================-->
                    <div class="mf-step" mf-step="9.3">
                        <div class="mf-step__item mf-step__yesno">
                            <?php 
                            
                                $step_21_question = get_field('step_21_question', $medical_form_page);
                                $step_21_question = isset($step_21_question) ? sanitize_text_field($step_21_question) : 'You need to have your blood Pressure (BP) checked within the last 12 months to recieve treatment.';
                                $step_21_yes_text = get_field('step_21_yes_text', $medical_form_page);
                                $step_21_yes_text = isset($step_21_yes_text) ? sanitize_text_field($step_21_yes_text) : 'Yes - It\'s been checked';
                                $step_21_no_text = get_field('step_21_no_text', $medical_form_page);
                                $step_21_no_text = isset($step_21_no_text) ? sanitize_text_field($step_21_no_text) : 'No - I haven\'t had it checked';
                            
                            ?>
                            
                            <div class="mf-field" data-question="<?php _e('Your blood pressure (BP) has been checked in the last 12 months.', 'woocommerce' ); ?>">
                                <label id="medical_form_details[medical_history][blood_pressure_test][answer]-error" class="error" for="medical_form_details[medical_history][blood_pressure_test][answer]"></label>
                                <div class="text-center">
                                    <h2><?php _e('Your blood pressure (BP) has been checked in the last 12 months.', 'woocommerce' ); ?></h2>
                                </div>

                                <div class="radio-btn-wrap">
                                    <div class="radio-btn">
                                        <input <?php checked( in_array(strtolower($medical_history['blood_pressure_test']['answer']), array('yes', 'yes - it\'s been checked')), true ); ?> type="radio" name="medical_form_details[medical_history][blood_pressure_test][answer]" value="Yes" id="yes9c">
                                        <label class="toggle-below-yes" for="yes9c"><?php _e($step_21_yes_text); ?></label>
                                    </div>
                                    <div class="radio-btn">
                                        <input <?php checked( in_array(strtolower($medical_history['blood_pressure_test']['answer']), array('no', 'none', 'no - i haven\'t had it checked')), true ); ?> type="radio" name="medical_form_details[medical_history][blood_pressure_test][answer]" value="No" id="no9c">
                                        <label class="toggle-below-no" goto-step="11.2" for="no9c"><?php _e($step_21_no_text); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

					<!--================================
						Ninth.4 Info Step
					================================-->
					<?php
						$step_22_question = get_field('step_22_question');
						$step_22_question = isset($step_22_question) ? sanitize_text_field($step_22_question) : 'When your blood pressure was taken were you diagnosed with?';
						$step_22_hypertension_text = get_field('step_22_hypertension_text');
						$step_22_hypertension_text = isset($step_22_hypertension_text) ? sanitize_text_field($step_22_hypertension_text) : 'Hypertension (high blood pressure)';
						$step_22_hypotension_text = get_field('step_22_hypotension_text');
						$step_22_hypotension_text = isset($step_22_hypotension_text) ? sanitize_text_field($step_22_hypotension_text) : 'Hypotension (low blood pressure)';
						$step_22_button_text = get_field('step_22_button_text');
						$step_22_button_text = isset($step_22_button_text) ? sanitize_text_field($step_22_button_text) : 'No, it was normal';
						$step_22_button_alt_text = get_field('step_22_button_alt_text');
						$step_22_button_alt_text = isset($step_22_button_alt_text) ? sanitize_text_field($step_22_button_alt_text) : 'Continue';
                    ?>
                    
					<div class="mf-step" mf-step="9.4" <?php echo in_array(strtolower($medical_history['blood_pressure_test']['answer']), array('yes', 'yes - it\'s been checked')) ? 'style="display:flex;"' : 'style="display:none;"'; ?>>
					    <div class="mf-step__item mf-step__chkbox mf-step__stop">
                            <div class="mf-field" data-question="<?php _e($step_22_question); ?>">
                                <label id="medical_form_details[medical_history][blood_pressure_diagnosis][answer]-error" class="error" for="medical_form_details[medical_history][blood_pressure_diagnosis][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_22_question); ?></h2>
                                </div>
                        
                                <div class="radio-btn-wrap">
                                    <div class="radio-btn radio-btn--full">
                                        <input <?php isset( $medical_history['blood_pressure_diagnosis'][0]['answer'] ) ? checked( $medical_history['blood_pressure_diagnosis'][0]['answer'], $step_22_hypertension_text ) : ''; ?> type="radio" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="<?php _e($step_22_hypertension_text); ?>" id="step9.4">
                                        <label for="step9.4"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_22_hypertension_text); ?></label>
                                    </div>
                                    <div class="radio-btn radio-btn--full">
                                        <input <?php isset( $medical_history['blood_pressure_diagnosis'][1]['answer'] ) ? checked( $medical_history['blood_pressure_diagnosis'][1]['answer'], $step_22_hypotension_text ) : ''; ?> type="radio" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="<?php _e($step_22_hypotension_text); ?>" id="step9.42">
                                        <label for="step9.42"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_22_hypotension_text); ?></label>
                                    </div>
                                    <div class="radio-btn radio-btn--full">
                                        <input <?php isset( $medical_history['blood_pressure_diagnosis']['answer'] ) ? checked( in_array(strtolower($medical_history['blood_pressure_diagnosis']['answer']), array('no', 'none', 'no, it was normal')), true ) : ''; ?> type="radio" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="None" id="step9.43">
                                        <label for="step9.43"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_22_button_text); ?></label>
                                    </div>
    					        </div>
					        </div>
					    </div>
					</div>

                    <!-- hypertension/ lightheadedness -->
                    <div class="mf-step" mf-step="10a.1">
                        <div class="mf-step__item mf-step__yesno">
                            <?php 
                                $step_23_question = get_field('step_23_question', $medical_form_page);
                                $step_23_question = isset($step_23_question) ? sanitize_text_field($step_23_question) : 'Do you frequently experience lightheadedness?';
                                $step_23_yes_text = get_field('step_23_yes_text', $medical_form_page);
                                $step_23_yes_text = isset($step_23_yes_text) ? sanitize_text_field($step_23_yes_text) : 'Yes';
                                $step_23_no_text  = get_field('step_23_no_text', $medical_form_page);
                                $step_23_no_text  = isset($step_23_no_text) ? sanitize_text_field($step_23_no_text) : 'No ';                            
                            ?>
                            
                            <div class="mf-field" data-question="<?php _e($step_23_question); ?>">
                                <label id="medical_form_details[medical_history][lightheadedness][answer]-error" class="error" for="medical_form_details[medical_history][lightheadedness][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_23_question); ?></h2>
                                </div>

                                <div class="radio-btn-wrap">
                                    <div class="radio-btn">
                                        <input <?php checked( strtolower($medical_history['lightheadedness']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="Yes" id="yes10aa">
                                        <label for="yes10aa" goto-step="11.1"><?php _e($step_23_yes_text); ?></label>
                                    </div>
                                    <div class="radio-btn">
                                        <input <?php checked( in_array(strtolower($medical_history['lightheadedness']['answer']), array('no', 'none')), true ); ?> type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="None" id="no10aa">
                                        <label for="no10aa" class="no" goto-step="10a.2"><?php _e($step_23_no_text); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!--================================
                                    Tenth Info Step
                        ================================-->
                    <div class="mf-step" mf-step="10">
                        <div class="mf-step__item mf-step__chkbox mf-step__stop">
                            <?php 

                                $step_14_question = get_field('step_14_question', $medical_form_page);
                                $step_14_question = isset($step_14_question) ? sanitize_text_field($step_14_question) : 'No';
                                $step_14_button_text = get_field('step_14_button_text', $medical_form_page);
                                $step_14_button_text = isset($step_14_button_text) ? sanitize_text_field($step_14_button_text) : 'No';

                                $step_14_button_alt_text = get_field('step_14_button_alt_text', $medical_form_page);
                                $step_14_button_alt_text = isset($step_14_button_alt_text) ? sanitize_text_field($step_14_button_alt_text) : 'No';
                            
                            ?>
                            
                            <div class="mf-field" data-question="<?php _e($step_14_question); ?>">
                                <label id="medical_form_details[medical_history][cardiovascular_symptoms][answer]-error" class="error" for="medical_form_details[medical_history][cardiovascular_symptoms][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_14_question); ?></h2>
                                </div>

                                <?php if (have_rows('step_14_options', $medical_form_page)) : ?>
                                    <div class="radio-btn-wrap">
                                        <?php
                                        $counter = 0;
                                        while (have_rows('step_14_options', $medical_form_page)) : the_row();
                                            $step_14_option_text = get_sub_field('step_14_option_text', $medical_form_page);

                                            ?>
                                            <div class="radio-btn radio-btn--full">
                                                <input <?php isset( $medical_history['cardiovascular_symptoms'][$counter]['answer'] ) ? checked( $medical_history['cardiovascular_symptoms'][$counter]['answer'], $step_14_option_text ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][cardiovascular_symptoms][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_14_option_text); ?>" id="symptoms-<?php _e($counter); ?>" class="cardiovascular-symptoms-group">
                                                <label for="symptoms-<?php _e($counter); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_14_option_text); ?></label>
                                            </div>
                                            <?php
                                            $counter++;
                                        endwhile;
                                        ?>
                                        <div class="radio-btn radio-btn--full">
                                            <input <?php isset( $medical_history['cardiovascular_symptoms']['answer'] ) ? checked( in_array(strtolower($medical_history['cardiovascular_symptoms']['answer']), array('no', 'none')), true ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][cardiovascular_symptoms][answer]" value="None" id="symptoms-<?php _e($counter + 1 ); ?>" class="cardiovascular-symptoms-group">
                                            <label for="symptoms-<?php _e($counter + 1 ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_14_button_text); ?></label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>


                    <!--================================
                        Eleventh Info Step
                        ================================-->
                    <div class="mf-step" mf-step="11">
                        <div class="mf-step__item mf-step__yesno">
                            <?php                             
                                $step_15_question = get_field('step_15_question', $medical_form_page);
                                $step_15_question = isset($step_15_question) ? sanitize_text_field($step_15_question) : 'Have you had a heart attack in the last 6 months?';
                                $step_15_yes_text = get_field('step_15_yes_text', $medical_form_page);
                                $step_15_yes_text = isset($step_15_yes_text) ? sanitize_text_field($step_15_yes_text) : 'Yes';
                                $step_15_no_text = get_field('step_15_no_text', $medical_form_page);
                                $step_15_no_text = isset($step_15_no_text) ? sanitize_text_field($step_15_no_text) : 'No';                            
                            ?>

                            <div class="mf-field" data-question="<?php _e($step_15_question); ?>">
                                <label id="medical_form_details[medical_history][heart_attack_past][answer]-error" class="error" for="medical_form_details[medical_history][heart_attack_past][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_15_question); ?></h2>
                                </div>

                                <div class="radio-btn-wrap">
                                    <div class="radio-btn">
                                        <input <?php checked( strtolower($medical_history['heart_attack_past']['answer']), 'yes' ); ?>  type="radio" name="medical_form_details[medical_history][heart_attack_past][answer]" value="Yes" id="yes11">
                                        <label for="yes11" goto-step="11.1"><?php _e($step_15_yes_text); ?></label>
                                    </div>
                                    <div class="radio-btn">
                                        <input <?php checked( in_array(strtolower($medical_history['heart_attack_past']['answer']), array('no', 'none')), true ); ?>  type="radio" name="medical_form_details[medical_history][heart_attack_past][answer]" value="None" id="no11">
                                        <label for="no11" ><?php _e($step_15_no_text); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--================================
                                    Twelvth Info Step
                        ================================-->
                    <div class="mf-step" mf-step="12">
                        <div class="mf-step__item mf-step__yesno">
                            <?php                             
                                $step_16_question = get_field('step_16_question', $medical_form_page);
                                $step_16_question = isset($step_16_question) ? sanitize_text_field($step_16_question) : 'Have you ever had a stroke or TIA?';
                                $step_16_yes_text = get_field('step_16_yes_text', $medical_form_page);
                                $step_16_yes_text = isset($step_16_yes_text) ? sanitize_text_field($step_16_yes_text) : 'Yes';
                                $step_16_no_text = get_field('step_16_no_text', $medical_form_page);
                                $step_16_no_text = isset($step_16_no_text) ? sanitize_text_field($step_16_no_text) : 'No';                            
                            ?>
                            
                            <div class="mf-field" data-question="<?php _e($step_16_question); ?>">
                                <label id="medical_form_details[medical_history][stroke_TIA][answer]-error" class="error" for="medical_form_details[medical_history][stroke_TIA][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_16_question); ?></h2>
                                </div>

                                <div class="radio-btn-wrap">
                                    <div class="radio-btn">
                                        <input <?php checked( strtolower($medical_history['stroke_TIA']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][stroke_TIA][answer]" value="Yes" id="yes12">
                                        <label for="yes12" goto-step="11.1"><?php _e($step_16_yes_text); ?></label>
                                    </div>
                                    <div class="radio-btn">
                                        <input <?php checked( in_array(strtolower($medical_history['stroke_TIA']['answer']), array('no', 'none')), true ); ?> type="radio" name="medical_form_details[medical_history][stroke_TIA][answer]" value="None" id="no12">
                                        <label for="no12" ><?php _e($step_16_no_text); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--================================
                                    Thirteenth Info Step
                        ================================-->
                    <?php                                    
                        $step_17_question = get_field('step_17_question', $medical_form_page);
                        $step_17_question = isset($step_17_question) ? sanitize_text_field($step_17_question) : 'Do you have now, or have you ever had, any of the following conditions?';
                        $step_17_button_text = get_field('step_17_button_text', $medical_form_page);
                        $step_17_button_text = isset($step_17_button_text) ? sanitize_text_field($step_17_button_text) : 'None apply';
                        $step_17_button_alt_text = get_field('step_17_button_alt_text', $medical_form_page);
                        $step_17_button_alt_text = isset($step_17_button_alt_text) ? sanitize_text_field($step_17_button_alt_text) : 'Continue';
                    ?>
                    <div class="mf-step" mf-step="13">
                        <div class="mf-step__item mf-step__chkbox mf-step__stop">
                            <div class="mf-field" data-question="<?php _e($step_17_question); ?>">
                                <label id="medical_form_details[medical_history][conditions_1][answer]-error" class="error" for="medical_form_details[medical_history][conditions_1][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_17_question); ?></h2>
                                </div>

                                <?php if (have_rows('step_17_options', $medical_form_page)) : ?>
                                    <div class="radio-btn-wrap">
                                        <?php
                                        $counter = 0;
                                        while (have_rows('step_17_options', $medical_form_page)) : the_row();
                                            $step_17_options_text = get_sub_field('step_17_options_text', $medical_form_page);
                                            $step_17_option_length = get_sub_field('step_17_option_length', $medical_form_page);
                                            ?>
                                            <div class="radio-btn radio-btn--full<?php // _e($step_17_option_length); ?>">
                                                <input <?php isset( $medical_history['conditions_1'][$counter]['answer'] ) ? checked( $medical_history['conditions_1'][$counter]['answer'], $step_17_options_text ) : ''; ?>  type="checkbox" name="medical_form_details[medical_history][conditions_1][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_17_options_text); ?>" id="condition1-<?php _e($counter); ?>" class="conditions-1-group">
                                                <label for="condition1-<?php _e($counter); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_17_options_text); ?></label>
                                            </div>
                                            <?php
                                            $counter++;
                                        endwhile;
                                        ?>
                                        <div class="radio-btn radio-btn--full<?php // _e($step_17_option_length); ?>">
                                                <input  <?php isset( $medical_history['conditions_1']['answer'] ) ? checked( in_array(strtolower($medical_history['conditions_1']['answer']), array('no', 'none')), true ) : ''; ?>  type="checkbox" name="medical_form_details[medical_history][conditions_1][answer]" value="None" id="condition1-<?php _e($counter + 1 ); ?>" class="conditions-1-group">
                                                <label for="condition1-<?php _e($counter + 1 ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_17_button_text); ?></label>
                                            </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>


                    <!--================================
                                Fourteenth Info Step
                    ================================-->
                    <div class="mf-step" mf-step="14">
                        <div class="mf-step__item mf-step__chkbox mf-step__stop">
                            <?php 
                                $step_18_question = get_field('step_18_question', $medical_form_page);
                                $step_18_question = isset($step_18_question) ? sanitize_text_field($step_18_question) : 'Do you have now, or have you ever had, any of the following conditions?';
                                $step_18_button_text = get_field('step_18_button_text', $medical_form_page);
                                $step_18_button_text = isset($step_18_button_text) ? sanitize_text_field($step_18_button_text) : 'None apply';                            
                            ?>

                            <div class="mf-field" data-question="<?php _e($step_18_question); ?>">
                                <label id="medical_form_details[medical_history][conditions_2][answer]-error" class="error" for="medical_form_details[medical_history][conditions_2][answer]"></label>
                                <div class="text-center">
                                    <h2 class="field-label"><?php _e($step_18_question); ?></h2>
                                </div>
                                <?php if (have_rows('step_18_options', $medical_form_page)) : ?>
                                    <div class="radio-btn-wrap">
                                        <?php
                                        $counter = 0;
                                        while (have_rows('step_18_options', $medical_form_page)) : the_row();
                                            $step_18_options_text = get_sub_field('step_18_options_text', $medical_form_page);
                                            $step_18_option_length = get_sub_field('step_18_option_length', $medical_form_page);
                                            ?>
                                            <div class="radio-btn radio-btn--full<?php // _e($step_18_option_length); ?>">
                                                <input <?php isset( $medical_history['conditions_2'][$counter]['answer'] ) ? checked( $medical_history['conditions_2'][$counter]['answer'], $step_18_options_text ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][conditions_2][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_18_options_text); ?>" id="condition2-<?php _e($counter); ?>" class="conditions-2-group">
                                                <label for="condition2-<?php _e($counter); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_18_options_text); ?></label>
                                            </div>
                                            <?php
                                            $counter++;
                                        endwhile;
                                        ?>
                                        <div class="radio-btn radio-btn--full<?php //_e($step_18_option_length); ?>">
                                                <input  <?php isset( $medical_history['conditions_2']['answer'] ) ? checked( in_array(strtolower($medical_history['conditions_2']['answer']), array('no', 'none')), true ) : ''; ?>  type="checkbox" name="medical_form_details[medical_history][conditions_2][answer]" value="None" id="condition2-<?php _e($counter + 1 ); ?>" class="conditions-2-group">
                                                <label for="condition2-<?php _e($counter + 1 ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" class="svg replaced-svg">
    <g id="Group_1" data-name="Group 1" transform="translate(-240 -431)">
        <g id="Bg_Copy_9" data-name="Bg Copy 9" transform="translate(240 431)" fill="#fff" stroke="#d5d5d5" stroke-miterlimit="10" stroke-width="1">
        <rect width="32" height="32" rx="4" stroke="none"></rect>
        <rect x="0.5" y="0.5" width="31" height="31" rx="3.5" fill="none"></rect>
        </g>
        <path id="Path" d="M16.716.394,6.783,10.563l-4.4-4.5a1.357,1.357,0,0,0-1.973,0,1.435,1.435,0,0,0,0,2.02L5.78,13.575A1.351,1.351,0,0,0,6.749,14a1.3,1.3,0,0,0,.969-.425L16.39,4.7l2.195-2.247a1.435,1.435,0,0,0,0-2.02A1.265,1.265,0,0,0,16.716.394Z" transform="translate(248 440)" fill="#fa1c41"></path>
    </g>
    </svg><?php _e($step_18_button_text); ?></label>
                                        </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="save-btn">
            <button type="submit" class="hc-dashboard-save-user-updates btn btn-filled">
                <span class="text">Save Changes</span>
            </button>
        </div>
        </form>
    </div>
