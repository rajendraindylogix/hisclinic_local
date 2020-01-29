<?php defined('ABSPATH') or die('No script kiddies please!');
/**
 * Template Name: Medical Form
 *
 * @package WordPress
 */

get_header();

/*Get ACF Fields and set default values*/
$step_1_title = get_field('step_1_title');
$step_1_title = isset($step_1_title) ? sanitize_text_field($step_1_title) : 'Hi! Welcome to your online pharmacy<span>.</span>';
$step_1_left_content = get_field('step_1_left_content');
$step_1_left_content = isset($step_1_left_content) ? ($step_1_left_content) : '<p>Most men will experience erection problems from time to time. But It’s not something to worry about! The good news: we can help. </p>
<p>Before browsing Erectile Dysfunction treatments, we need you to answer a few quick questions. This will help us know what treatments are right for you.</p>';
$step_1_button_text = get_field('step_1_button_text');
$step_1_button_text = isset($step_1_button_text) ? sanitize_text_field($step_1_button_text) : 'Get Started';

$step_2_question = get_field('step_2_question');
$step_2_question = isset($step_2_question) ? sanitize_text_field($step_2_question) : 'What is your gender?';
$step_2_male_image = get_field('step_2_male_image');
$step_2_male_image = isset($step_2_male_image) ? sanitize_text_field($step_2_male_image) : get_stylesheet_directory_uri() . '/assets/img/noun_boy_2374482_1f1f1f.svg';
$step_2_male_text = get_field('step_2_male_text');
$step_2_male_text = isset($step_2_male_text) ? sanitize_text_field($step_2_male_text) : 'Male';
$step_2_female_image = get_field('step_2_female_image');
$step_2_female_image = isset($step_2_female_image) ? sanitize_text_field($step_2_female_image) : get_stylesheet_directory_uri() . '/assets/img/noun_girl_2374468_1f1f1f.svg';
$step_2_female_text = get_field('step_2_female_text');
$step_2_female_text = isset($step_2_female_text) ? sanitize_text_field($step_2_female_text) : 'Female';

$step_2a_title = get_field('step_2a_title');
$step_2a_title = isset($step_2a_title) ? sanitize_text_field($step_2a_title) : 'We’re sorry, you don’t qualify.';
$step_2a_description = get_field('step_2a_description');
$step_2a_description = isset($step_2a_description) ? ($step_2a_description) : '<p>Our prescription products are designed for male’s only, and you’ve mentioned that you’re female.</p>
                    <p>If you have a male partner and they’re struggling with ED, why not open up the conversation and invite them to complete our online medical form.
                    </p>';
$step_2a_button_text = get_field('step_2a_button_text');
$step_2a_button_text = isset($step_2a_button_text) ? sanitize_text_field($step_2a_button_text) : 'About His Clinic';
$step_2a_button_link = get_field('step_2a_button_link');
$step_2a_button_link = isset($step_2a_button_link) ? sanitize_text_field($step_2a_button_link) : get_site_url() . '/about-us/';

$step_3_question = get_field('step_3_question');
$step_3_question = isset($step_3_question) ? sanitize_text_field($step_3_question) : 'What is your date of birth?';
$step_3_label = get_field('step_3_label');
$step_3_label = isset($step_3_label) ? sanitize_text_field($step_3_label) : 'Date of Birth (dd/mm/yy)';
$step_3_button_text = get_field('step_3_button_text');
$step_3_button_text = isset($step_3_button_text) ? sanitize_text_field($step_3_button_text) : 'Validate Age';

$step_3a_question = get_field('step_3a_question');
$step_3a_question = isset($step_3a_question) ? sanitize_text_field($step_3a_question) : 'We’re sorry, you don’t qualify.';
$step_3a_description = get_field('step_3a_description');
$step_3a_description = isset($step_3a_description) ? ($step_3a_description) : '<p>You’ve mentioned that you’re under 18 years old. This means that you don’t qualify to view or purchase our prescription products.</p>
<p>There may be alternative solutions for you. Check out our treatments page to view other solutions.</p>';
$step_3b_description = isset($step_3b_description) ? ($step_3b_description) : '<p>You’ve mentioned that you’re over 75 years old. This means that you don’t qualify to view or purchase our prescription products.</p>
<p>There may be alternative solutions for you. Check out our treatments page to view other solutions.</p>';
$step_3a_button_text = get_field('step_3a_button_text');
$step_3a_button_text = isset($step_3a_button_text) ? sanitize_text_field($step_3a_button_text) : 'Treatments';
$step_3a_button_link = get_field('step_3a_button_link');
$step_3a_button_link = isset($step_3a_button_link) ? sanitize_text_field($step_3a_button_link) : get_site_url() . '/mens-health-blog-erectile-dysfunction/a-simple-guide-to-discussing-erectile-dysfunction-with-your-partner/';

$step_4_question = get_field('step_4_question');
$step_4_question = isset($step_4_question) ? sanitize_text_field($step_4_question) : 'What is your height and weight?';
$step_4_height_label = get_field('step_4_height_label');
$step_4_height_label = isset($step_4_height_label) ? sanitize_text_field($step_4_height_label) : 'Height (Centimeters)';
$step_4_dont_know_text = get_field('step_4_dont_know_text');
$step_4_dont_know_text = isset($step_4_dont_know_text) ? sanitize_text_field($step_4_dont_know_text) : 'I don\'t know';
$step_4_weight_label = get_field('step_4_weight_label');
$step_4_weight_label = isset($step_4_weight_label) ? sanitize_text_field($step_4_weight_label) : 'Weight (Kilograms)';
$step_4_weight_dont_know_text = get_field('step_4_weight_dont_know_text');
$step_4_weight_dont_know_text = isset($step_4_weight_dont_know_text) ? sanitize_text_field($step_4_weight_dont_know_text) : 'I don\'t know';
$step_4_button_text = get_field('step_4_button_text');
$step_4_button_text = isset($step_4_button_text) ? sanitize_text_field($step_4_button_text) : 'Continue';

$step_5_question = get_field('step_5_question');
$step_5_question = isset($step_5_question) ? sanitize_text_field($step_5_question) : 'What is your main diet?';

$step_6_question = get_field('step_6_question');
$step_6_question = isset($step_6_question) ? sanitize_text_field($step_6_question) : 'Do you ever have a problem getting or maintaining an erection?';

$step_7_question = get_field('step_7_question');
$step_7_question = isset($step_7_question) ? sanitize_text_field($step_7_question) : 'Have you ever been prescribed or approved by a doctor to take Sildenafil or Cialis?';
$step_7_yes_text = get_field('step_7_yes_text');
$step_7_yes_text = isset($step_7_yes_text) ? sanitize_text_field($step_7_yes_text) : 'Yes';
$step_7_no_text = get_field('step_7_no_text');
$step_7_no_text = isset($step_7_no_text) ? sanitize_text_field($step_7_no_text) : 'No';

$step_7_info_tool_tip = get_field('step_7_info_tool_tip');
$step_7_info_tool_tip = isset($step_7_info_tool_tip) ? sanitize_text_field($step_7_info_tool_tip) : 'If you haven’t previously been approved by a doctor to use these medications, you will need to have a short phone call with one of our male doctors.';
$step_7_sildenafil_tool_tip = get_field('step_7_sildenafil_tool_tip');
$step_7_sildenafil_tool_tip = isset($step_7_sildenafil_tool_tip) ? sanitize_text_field($step_7_sildenafil_tool_tip) : 'Generic Viagra (lasts 4 hours)';
$step_7_cialis_tool_tip = get_field('step_7_cialis_tool_tip');
$step_7_cialis_tool_tip = isset($step_7_cialis_tool_tip) ? sanitize_text_field($step_7_cialis_tool_tip) : 'Generic Viagra (lasts 4 hours)';

$step_8_question = get_field('step_8_question');
$step_8_question = isset($step_8_question) ? sanitize_text_field($step_8_question) : 'Do you have, or have you ever had, Heart Disease?';
$step_8_yes_text = get_field('step_8_yes_text');
$step_8_yes_text = isset($step_8_yes_text) ? sanitize_text_field($step_8_yes_text) : 'Yes';
$step_8_no_text = get_field('step_8_no_text');
$step_8_no_text = isset($step_8_no_text) ? sanitize_text_field($step_8_no_text) : 'No';

$step_9_title = get_field('step_9_title');
$step_9_title = isset($step_9_title) ? sanitize_text_field($step_9_title) : 'Your medical assessment needs further review';
$step_9_description = get_field('step_9_description');
$step_9_description = isset($step_9_description) ? ($step_9_description) : '<p>We’re currently reviewing your medical form due to some of your answers. Your health is our number one priority, so until our doctors have given your the all clear you will be able to browse the products, but will be unable to purchase.</p>
                            <p>Please create an account to view products.</p>';
$step_9_form_name_label = get_field('step_9_form_name_label');
$step_9_form_name_label = isset($step_9_form_name_label) ? sanitize_text_field($step_9_form_name_label) : 'Full Name';
$step_9_form_email_label = get_field('step_9_form_email_label');
$step_9_form_email_label = isset($step_9_form_email_label) ? sanitize_text_field($step_9_form_email_label) : 'Email';
$step_9_password_label = get_field('step_9_password_label');
$step_9_password_label = isset($step_9_password_label) ? sanitize_text_field($step_9_password_label) : 'Password';
$step_9_button_text = get_field('step_9_button_text');
$step_9_button_text = isset($step_9_button_text) ? sanitize_text_field($step_9_button_text) : 'Browse Products';
$step_9_button_link = get_field('step_9_button_link');
$step_9_button_link = isset($step_9_button_link) ? sanitize_text_field($step_9_button_link) : '#';

$step_10_question = get_field('step_10_question');
$step_10_question = isset($step_10_question) ? sanitize_text_field($step_10_question) : 'Have you previously used any of the following products?';
$step_10_sildenafil_text = get_field('step_10_sildenafil_text');
$step_10_sildenafil_text = isset($step_10_sildenafil_text) ? sanitize_text_field($step_10_sildenafil_text) : 'Sildenafil (generic Viagra, lasts 4 hours)';
$step_10_cialis_text = get_field('step_10_cialis_text');
$step_10_cialis_text = isset($step_10_cialis_text) ? sanitize_text_field($step_10_cialis_text) : 'Cialis (lasts 36 hours)';
$step_10_daily_cialis_text = get_field('step_10_daily_cialis_text');
$step_10_daily_cialis_text = isset($step_10_daily_cialis_text) ? sanitize_text_field($step_10_daily_cialis_text) : 'Daily Cialis (taken daily so you’re always ready)';
$step_10_button_text = get_field('step_10_button_text');
$step_10_button_text = isset($step_10_button_text) ? sanitize_text_field($step_10_button_text) : 'None';
$step_10_button_alt_text = get_field('step_10_button_alt_text');
$step_10_button_alt_text = isset($step_10_button_alt_text) ? sanitize_text_field($step_10_button_alt_text) : 'Continue';

$step_11_a_question = get_field('step_11_a_question');
$step_11_a_question = isset($step_11_a_question) ? sanitize_text_field($step_11_a_question) : 'Was Sildenafil effective?';
$step_11_a_yes_text = get_field('step_11_a_yes_text');
$step_11_a_yes_text = isset($step_11_a_yes_text) ? sanitize_text_field($step_11_a_yes_text) : 'Yes';
$step_11_a_no_text = get_field('step_11_a_no_text');
$step_11_a_no_text = isset($step_11_a_no_text) ? sanitize_text_field($step_11_a_no_text) : 'No';

$step_11_b_question = get_field('step_11_b_question');
$step_11_b_question = isset($step_11_b_question) ? sanitize_text_field($step_11_b_question) : 'Was Cialis effective?';
$step_11_b_yes_text = get_field('step_11_b_yes_text');
$step_11_b_yes_text = isset($step_11_b_yes_text) ? sanitize_text_field($step_11_b_yes_text) : 'Yes';
$step_11_b_no_text = get_field('step_11_b_no_text');
$step_11_b_no_text = isset($step_11_b_no_text) ? sanitize_text_field($step_11_b_no_text) : 'No';

$step_11_c_question = get_field('step_11_c_question');
$step_11_c_question = isset($step_11_c_question) ? sanitize_text_field($step_11_c_question) : 'Was Daily Cialis effective?';
$step_11_c_yes_text = get_field('step_11_c_yes_text');
$step_11_c_yes_text = isset($step_11_c_yes_text) ? sanitize_text_field($step_11_c_yes_text) : 'Yes';
$step_11_c_no_text = get_field('step_11_c_no_text');
$step_11_c_no_text = isset($step_11_c_no_text) ? sanitize_text_field($step_11_c_no_text) : 'No';

$step_12_question = get_field('step_12_question');
$step_12_question = isset($step_12_question) ? sanitize_text_field($step_12_question) : 'Do you have any health conditions or a history of prior surgeries?';
$step_12_yes_text = get_field('step_12_yes_text');
$step_12_yes_text = isset($step_12_yes_text) ? sanitize_text_field($step_12_yes_text) : 'Yes';
$step_12_no_text = get_field('step_12_no_text');
$step_12_no_text = isset($step_12_no_text) ? sanitize_text_field($step_12_no_text) : 'Yes';

$step_12_a_question = get_field('step_12_a_question');
$step_12_a_question = isset($step_12_a_question) ? sanitize_text_field($step_12_a_question) : 'Please provide as much detail as possible about your ongoing medical conditions:';
$step_12_a_label = get_field('step_12_a_label');
$step_12_a_label = isset($step_12_a_label) ? sanitize_text_field($step_12_a_label) : 'Ongoing Medical Conditions';
$step_12_a_placeholder_text = get_field('step_12_a_placeholder_text');
$step_12_a_placeholder_text = isset($step_12_a_placeholder_text) ? sanitize_text_field($step_12_a_placeholder_text) : 'Please provide details';
$step_12_a_button_text = get_field('step_12_a_button_text');
$step_12_a_button_text = isset($step_12_a_button_text) ? sanitize_text_field($step_12_a_button_text) : 'Next';

$step_12_b_question = get_field('step_12_b_question');
$step_12_b_question = isset($step_12_b_question) ? sanitize_text_field($step_12_b_question) : 'Please provide details about your history of hospital admission or surgery:';
$step_12_b_yes_text = get_field('step_12_b_yes_text');
$step_12_b_yes_text = isset($step_12_b_yes_text) ? sanitize_text_field($step_12_b_yes_text) : 'Yes';
$step_12_b_no_text = get_field('step_12_b_no_text');
$step_12_b_no_text = isset($step_12_b_no_text) ? sanitize_text_field($step_12_b_no_text) : 'No';

$past_admission_question = get_field('past_admission_question');
$past_admission_question = isset($past_admission_question) ? sanitize_text_field($past_admission_question) : 'Please provide details about your history of hospital admission or surgery:';
$step_12_b_label = get_field('step_12_b_label');
$step_12_b_label = isset($step_12_b_label) ? sanitize_text_field($step_12_b_label) : 'Date of hospital admission or surgery';
$step_12_b_description = get_field('step_12_b_description');
$step_12_b_description = isset($step_12_b_description) ? sanitize_text_field($step_12_b_description) : 'Details about your hospital admission or surgery';

$step_12_c_question = get_field('step_12_c_question');
$step_12_c_question = isset($step_12_c_question) ? sanitize_text_field($step_12_c_question) : 'Do you have any allergies?';
$step_12_c_yes_text = get_field('step_12_c_yes_text');
$step_12_c_yes_text = isset($step_12_c_yes_text) ? sanitize_text_field($step_12_c_yes_text) : 'Yes';
$step_12_c_no_text = get_field('step_12_c_no_text');
$step_12_c_no_text = isset($step_12_c_no_text) ? sanitize_text_field($step_12_c_no_text) : 'No';

$allergies_description_text = get_field('allergies_description_text');
$allergies_description_text = isset($allergies_description_text) ? sanitize_text_field($allergies_description_text) : 'Please provide as much detail as possible about your allergies:';
$allergies_label = get_field('allergies_label');
$allergies_label = isset($allergies_label) ? sanitize_text_field($allergies_label) : 'Your allergies';
$allergies_next_button_text = get_field('allergies_next_button_text');
$allergies_next_button_text = isset($allergies_next_button_text) ? sanitize_text_field($allergies_next_button_text) : 'Next';

$herbal_supplements_question = get_field('herbal_supplements_question');
$herbal_supplements_question = isset($herbal_supplements_question) ? sanitize_text_field($herbal_supplements_question) : 'Are you taking any other medications, herbs or supplements?';
$herbal_supplements_yes_text = get_field('herbal_supplements_yes_text');
$herbal_supplements_yes_text = isset($herbal_supplements_yes_text) ? sanitize_text_field($herbal_supplements_yes_text) : 'Yes';
$herbal_supplements_no_text = get_field('herbal_supplements_no_text');
$herbal_supplements_no_text = isset($herbal_supplements_no_text) ? sanitize_text_field($herbal_supplements_no_text) : 'No';

$herbal_supplements_description = get_field('herbal_supplements_description');
$herbal_supplements_description = isset($herbal_supplements_description) ? sanitize_text_field($herbal_supplements_description) : 'Please provide details of your medication, herbs or supplements:';
$herbal_supplements_label_text = get_field('herbal_supplements_label_text');
$herbal_supplements_label_text = isset($herbal_supplements_label_text) ? sanitize_text_field($herbal_supplements_label_text) : 'Medication Details';

$step_13_question = get_field('step_13_question');
$step_13_question = isset($step_13_question) ? sanitize_text_field($step_13_question) : 'Are you currently taking any Nitrate medications (GTN patch, Mononitrates, etc)?';
$step_13_yes_text = get_field('step_13_yes_text');
$step_13_yes_text = isset($step_13_yes_text) ? sanitize_text_field($step_13_yes_text) : 'Yes';
$step_13_no_text = get_field('step_13_no_text');
$step_13_no_text = isset($step_13_no_text) ? sanitize_text_field($step_13_no_text) : 'No';

$step_14_question = get_field('step_14_question');
$step_14_question = isset($step_14_question) ? sanitize_text_field($step_14_question) : 'No';
$step_14_button_text = get_field('step_14_button_text');
$step_14_button_text = isset($step_14_button_text) ? sanitize_text_field($step_14_button_text) : 'No';

$step_14_button_alt_text = get_field('step_14_button_alt_text');
$step_14_button_alt_text = isset($step_14_button_alt_text) ? sanitize_text_field($step_14_button_alt_text) : 'No';

$step_15_question = get_field('step_15_question');
$step_15_question = isset($step_15_question) ? sanitize_text_field($step_15_question) : 'Have you had a heart attack in the last 6 months?';
$step_15_yes_text = get_field('step_15_yes_text');
$step_15_yes_text = isset($step_15_yes_text) ? sanitize_text_field($step_15_yes_text) : 'Yes';
$step_15_no_text = get_field('step_15_no_text');
$step_15_no_text = isset($step_15_no_text) ? sanitize_text_field($step_15_no_text) : 'No';

$step_16_question = get_field('step_16_question');
$step_16_question = isset($step_16_question) ? sanitize_text_field($step_16_question) : 'Have you ever had a stroke or TIA?';
$step_16_yes_text = get_field('step_16_yes_text');
$step_16_yes_text = isset($step_16_yes_text) ? sanitize_text_field($step_16_yes_text) : 'Yes';
$step_16_no_text = get_field('step_16_no_text');
$step_16_no_text = isset($step_16_no_text) ? sanitize_text_field($step_16_no_text) : 'No';

$step_17_question = get_field('step_17_question');
$step_17_question = isset($step_17_question) ? sanitize_text_field($step_17_question) : 'Do you have now, or have you ever had, any of the following conditions?';
$step_17_button_text = get_field('step_17_button_text');
$step_17_button_text = isset($step_17_button_text) ? sanitize_text_field($step_17_button_text) : 'None apply';
$step_17_button_alt_text = get_field('step_17_button_alt_text');
$step_17_button_alt_text = isset($step_17_button_alt_text) ? sanitize_text_field($step_17_button_alt_text) : 'Continue';

$step_18_question = get_field('step_18_question');
$step_18_question = isset($step_18_question) ? sanitize_text_field($step_18_question) : 'Do you have now, or have you ever had, any of the following conditions?';
$step_18_button_text = get_field('step_18_button_text');
$step_18_button_text = isset($step_18_button_text) ? sanitize_text_field($step_18_button_text) : 'None apply';

// added Jaywing 09/07/2019
$step_18_button_alt_text = get_field('step_18_button_alt_text');
$step_18_button_alt_text = isset($step_18_button_alt_text)? sanitize_text_field($step_18_button_alt_text) :'Continue';
// ends added Jaywing 09/07/2019

$step_19_title = get_field('step_19_title');
$step_19_title = isset($step_19_title) ? sanitize_text_field($step_19_title) : 'Our doctors have prescribed the right treatment for you';
$step_19_description = get_field('step_19_description');
$step_19_description = isset($step_19_description) ? ($step_19_description) : '<p>Based on your medical history and individual needs, our doctors have provided a personalised treatment</p><p>Please complete your account to view your prescription.</p>';
$step_19_name_label = get_field('step_19_name_label');
$step_19_name_label = isset($step_19_name_label) ? sanitize_text_field($step_19_name_label) : 'Full Name';
$step_19_last_name_label = get_field('step_19_last_name_label');
$step_19_last_name_label = isset($step_19_last_name_label) ? sanitize_text_field($step_19_last_name_label) : 'Last Name';
$step_19_email_label = get_field('step_19_email_label');
$step_19_email_label = isset($step_19_email_label) ? sanitize_text_field($step_19_email_label) : 'Email';
$step_19_password_label = get_field('step_19_password_label');
$step_19_password_label = isset($step_19_password_label) ? sanitize_text_field($step_19_password_label) : 'Password';
$step_19_button_label = get_field('step_19_button_label');
$step_19_button_label = isset($step_19_button_label) ? sanitize_text_field($step_19_button_label) : 'Complete account to order';

$step_20_title = get_field('step_20_title');
$step_20_title = isset($step_20_title) ? sanitize_text_field($step_20_title) : 'We’re sorry, you don’t qualify.';
$step_20_description = get_field('step_20_description');
$step_20_description = isset($step_20_description) ? ($step_20_description) : '<p>You’ve indicated that you have health conditions that make you ineligible to purchase our prescription products.</p> <br/>
<p>There may be alternative solutions for you. Check out our treatments page to view other solutions.</p>';
$step_20_button_text = get_field('step_20_button_text');
$step_20_button_text = isset($step_20_button_text) ? sanitize_text_field($step_20_button_text) : 'Treatments';
$step_20_button_link = get_field('step_20_button_link');
$step_20_button_link = isset($step_20_button_link) ? sanitize_text_field($step_20_button_link) : '#';

$step_21_question = get_field('step_21_question');
$step_21_question = isset($step_21_question) ? sanitize_text_field($step_21_question) : 'You need to have your blood Pressure (BP) checked within the last 12 months to recieve treatment.';
$step_21_yes_text = get_field('step_21_yes_text');
$step_21_yes_text = isset($step_21_yes_text) ? sanitize_text_field($step_21_yes_text) : 'Yes - It\'s been checked';
$step_21_no_text = get_field('step_21_no_text');
$step_21_no_text = isset($step_21_no_text) ? sanitize_text_field($step_21_no_text) : 'No - I haven\'t had it checked';

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

$step_23_question = get_field('step_23_question');
$step_23_question = isset($step_23_question) ? sanitize_text_field($step_23_question) : 'Do you frequently experience lightheadedness?';
$step_23_yes_text = get_field('step_23_yes_text');
$step_23_yes_text = isset($step_23_yes_text) ? sanitize_text_field($step_23_yes_text) : 'Yes';
$step_23_no_text = get_field('step_23_no_text');
$step_23_no_text = isset($step_23_no_text) ? sanitize_text_field($step_23_no_text) : 'No ';

$step_24_title = get_field('step_24_title');
$step_24_title = isset($step_24_title) ? sanitize_text_field($step_24_title) : 'You\'re almost there!';
$step_24_description = get_field('step_24_description');
$step_24_description = isset($step_24_description) ? ($step_24_description) : '<p>Thanks for filling in your details!</p> <br/>
<p>Please create your account so that our doctors can get back to you after they\'ve reviewed your details.</p>';
$step_24_name_label = get_field('step_24_name_label');
$step_24_name_label = isset($step_24_name_label) ? sanitize_text_field($step_24_name_label) : 'Full Name';
$step_24_email_label = get_field('step_24_email_label');
$step_24_email_label = isset($step_24_email_label) ? sanitize_text_field($step_24_email_label) : 'Email';
$step_24_password_label = get_field('step_24_password_label');
$step_24_password_label = isset($step_24_password_label) ? sanitize_text_field($step_24_password_label) : 'Password';
$step_24_button_text = get_field('step_24_button_text');
$step_24_button_text = isset($step_24_button_text) ? sanitize_text_field($step_24_button_text) : 'Create Account';

$step_25_title = get_field('step_25_title');
$step_25_title = isset($step_25_title) ? sanitize_text_field($step_25_title) : 'Your medical assessment needs further review';
$step_25_description = get_field('step_25_description');
$step_25_description = isset($step_25_description) ? ($step_25_description) : '<p>Based on your answers you have unspecified health conditions or a history of prior surgeries.</p> <br/>
<p>Please input the details about these circumstances so that we can ensure our products are safe for you to use. You\'ll be able to purchase our products after your medical information has been reviewed.</p>';
$step_25_medical_detail_label = get_field('step_25_medical_detail_label');
$step_25_medical_detail_label = isset($step_25_medical_detail_label) ? sanitize_text_field($step_25_medical_detail_label) : 'Medical Details';
$step_25_finish_text = get_field('step_25_finish_text');
$step_25_finish_text = isset($step_25_finish_text) ? sanitize_text_field($step_25_finish_text) : 'Finish';

$step_26_question = get_field('step_26_question');
$step_26_question = isset($step_26_question) ? sanitize_text_field($step_26_question) : 'If prescribed, how often do you anticipate using this treatment for sexual activity?';

$step_27_question = get_field('step_27_question');
$step_27_question = isset($step_27_question) ? sanitize_text_field($step_27_question) : 'Do you have any of the following cardiovascular symptoms?';
$step_27_button_text = get_field('step_27_button_text');
$step_27_button_text = isset($step_27_button_text) ? sanitize_text_field($step_27_button_text) : 'None apply';
$step_27_button_alt_text = get_field('step_27_button_alt_text');
$step_27_button_alt_text = isset($step_27_button_alt_text) ? sanitize_text_field($step_27_button_alt_text) : 'Continue';

$step_28_title = get_field('step_28_title');
$step_28_title = isset($step_28_title) ? sanitize_text_field($step_28_title) : 'We’re sorry, you don’t qualify.';
$step_28_description = get_field('step_28_description');
$step_28_description = isset($step_28_description) ? ($step_28_description) : '<p>Oh no! You haven’t had your blood pressure checked within the last 12 months.</p>
<p>You can have your blood pressure checked for free by your local doctor. After that, we\'ll be able to help.</p>';
$step_28_button_text = get_field('step_28_button_text');
$step_28_button_text = isset($step_28_button_text) ? sanitize_text_field($step_28_button_text) : 'Home';
$step_28_button_link = get_field('step_28_button_link');
$step_28_button_link = isset($step_28_button_link) ? sanitize_text_field($step_28_button_link) : '#';

$sildenafil_redirection_link = get_field('sildenafil_redirection_link');
$sildenafil_redirection_link = isset($sildenafil_redirection_link) ? sanitize_text_field($sildenafil_redirection_link) : '#';

$cialis_redirection_link = get_field('cialis_redirection_link');
$cialis_redirection_link = isset($cialis_redirection_link) ? sanitize_text_field($cialis_redirection_link) : '#';

$daily_cialis_redirection_link = get_field('daily_cialis_redirection_link');
$daily_cialis_redirection_link = isset($daily_cialis_redirection_link) ? sanitize_text_field($daily_cialis_redirection_link) : '#';

$page_redirect_link = get_field('page_redirect_link');


$pop_up_question = array();
$pop_up_question['step_19_title'] = $step_19_title;
$pop_up_question['step_19_description'] = $step_19_description;
$pop_up_question['step_19_name_label'] = $step_19_name_label;
$pop_up_question['step_19_last_name_label'] = $step_19_last_name_label;
$pop_up_question['step_19_email_label'] = $step_19_email_label;
$pop_up_question['step_19_password_label'] = $step_19_password_label;
$pop_up_question['step_19_button_label'] = $step_19_button_label;

update_option('pop_up_questions', $pop_up_question);
$pop_up_question_chk = get_option('pop_up_questions');
if (!empty($pop_up_question_chk)) {
    update_option('pop_up_questions', $pop_up_question);
}

/*Store questions to the Database for internal page*/
$questions = array();

$questions['gender'] = $step_2_question;
$questions['date'] = $step_3_question;
$questions['height'] = $step_4_height_label;
$questions['heightchk'] = $step_4_height_label;
$questions['weightchk'] = $step_4_weight_label;
$questions['weight'] = $step_4_weight_label;
$questions['diet'] = $step_5_question;
$questions['uses'] = $step_26_question;
$questions['erection'] = $step_6_question;
$questions['prescription'] = $step_7_question;
$questions['heart_disease'] = $step_8_question;
$questions['previous_use_sildenafil'] = $step_10_question;
$questions['sildenafil_effective'] = $step_11_a_question;
$questions['previous_use_cialis'] = $step_10_question;
$questions['cialis_effective'] = $step_11_b_question;
$questions['previous_use_cialis_daily'] = $step_10_question;
$questions['daily_cialis_effective'] = $step_11_c_question;
$questions['surgeries'] = $step_12_question;
$questions['nitrate'] = $step_13_question;
$questions['blood_pressure_test'] = $step_21_question;
$questions['blood_pressure_diagnosis'] = $step_22_question;
$questions['lightheadedness'] = $step_23_question;
$questions['cardiovascular_symptoms'] = $step_14_question;
$questions['heart_attack_past'] = $step_15_question;
$questions['stroke_TIA'] = $step_16_question;
$questions['conditions_1'] = $step_17_question;
$questions['conditions_2'] = $step_18_question;
$questions['form4_description'] = $step_25_medical_detail_label;

update_option('medical_form_questions', $questions);
$questions_chk = get_option('medical_form_questions');
if (!empty($questions_chk)) {
    update_option('medical_form_questions', $questions);
}

/*Form HTML begins*/
?>
<div id="mf-app">
    <div class="container">
        <form action="#" id="new-medical-form">
            <!--================================
                Exception Templates Starts
            =================================-->

            <div class="mf-step" mf-step="9.2a" style="display:none;">
                <div class="mf-step__item mf-step__yesno">
                    <div class="text-center">
                        <h2><?php _e($herbal_supplements_description); ?></h2>
                    </div>

                    <div class="animate-inputs animate-textarea">
                        <div class="animate-input">
                            <textarea class="validate-for-next" type="text" required name="medical_form_details[medical_history][herbs_description][answer]" id="herbal_suppliments" placeholder="Please provide details"></textarea>
                            <label for="herbal_suppliments"><?php _e($herbal_supplements_label_text); ?></label>
                        </div>
                        <input type="hidden" name="medical_form_details[medical_history][herbs_description][question]" value="<?php _e($herbal_supplements_description); ?>" />
                    </div>

                    <div class="text-center">
                        <a href="#" class="btn filled mf-stop disabled" goto-step="9.3">Next</a>
                    </div>

                </div>
                <div class="mf-progress">
                    <a href="#" class="mf-stop hide-products" goto-step="9.2">
                        < Back</a> <div class="mf-progress__bar">
                            <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
    </div>

    <div class="mf-step" mf-step="7.1" style="display:none;">
        <div class="mf-step__item mf-step__yesno">
            <div class="text-center">
                <h2><?php _e($step_8_question); ?></h2>
            </div>

            <div class="radio-btn-wrap">
                <div class="radio-btn">
                    <input type="radio" name="medical_form_details[medical_history][heart_disease][answer]" value="Yes" id="yes7.1">
                    <label for="yes7.1" class="mf-stop" goto-step="7.3"><?php _e($step_8_yes_text); ?></label>
                </div>
                <div class="radio-btn">
                    <input type="radio" name="medical_form_details[medical_history][heart_disease][answer]" value="No" id="no7.1">
                    <label for="no7.1" class="mf-stop" goto-step="7"><?php _e($step_8_no_text); ?></label>
                </div>
            </div>
            <input type="hidden" name="medical_form_details[medical_history][heart_disease][question]" value="<?php _e($step_8_question); ?>" />
        </div>
        <div class="mf-progress">
            <a href="#" class="mf-stop" goto-step="6">
                < Back</a> <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
        </div>
    </div>
</div>

<!-- heart disease disqualify -->
<div class="mf-step" mf-step="7.3" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_9_title); ?></h2>
        </div>

        <div class="mf-content mf-content__sorry">
            <?php _e($step_9_description); ?>
        </div>

        <div class="text-center">
            <a href="<?php _e($step_9_button_link); ?>" class="btn filled"><?php _e($step_9_button_text); ?></a>
        </div>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="7.1">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:82%;"></div>
    </div>
    </div>
</div>

<!-- hypertension/ lightheadedness -->
<div class="mf-step" mf-step="10a.1" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_23_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="Yes" id="yes10aa">
                <label for="yes10aa" class="mf-stop" goto-step="11.1"><?php _e($step_23_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="Yes" id="no10aa">
                <label for="no10aa" class="mf-stop no" goto-step="10a.2"><?php _e($step_23_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][lightheadedness][question]" value="<?php _e($step_23_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="10a">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:82%;"></div>
    </div>
    </div>
</div>

<!--================================
    Common Templates Starts
=================================-->
<div class="mf-step" mf-step="2.1" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_2a_title); ?></h2>
        </div>

        <div class="mf-content mf-content__sorry">
            <?php _e($step_2a_description); ?>
        </div>

        <div class="text-center">
            <a href="<?php _e($step_2a_button_link); ?>" class="btn filled"><?php _e($step_2a_button_text); ?></a>
        </div>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="2"> < Back</a> 
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:10%;"></div>
        </div>
    </div>
</div>
<!-- less than 18 -->
<div class="mf-step" mf-step="3.1" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_3a_question); ?></h2>
        </div>

        <div class="mf-content mf-content__sorry">
            <?php _e($step_3a_description); ?>
        </div>

        <div class="text-center">
            <a href="<?php _e($step_3a_button_link); ?>" class="btn filled"><?php _e($step_3a_button_text); ?></a>
        </div>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="3"> < Back</a> 
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:10%;"></div>
        </div>
    </div>
</div>
<!-- greater than 75 -->
<div class="mf-step" mf-step="3.2" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_3a_question); ?></h2>
        </div>

        <div class="mf-content mf-content__sorry">
            <?php _e($step_3b_description); ?>
        </div>

        <div class="text-center">
            <a href="<?php _e($step_3a_button_link); ?>" class="btn filled"><?php _e($step_3a_button_text); ?></a>
        </div>
    </div>
    <!-- <div class="mf-progress">
        <a href="<?php //echo get_site_url()?>/medical-form"><img src="<?php //echo get_stylesheet_directory_uri() ?>/assets/img/caret-left.svg" />Back</a>
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:10%;"></div>
        </div>
    </div> -->
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="3"> < Back</a> 
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:10%;"></div>
        </div>
    </div>
</div>

<!-- disqalify step -->
<div class="mf-step" mf-step="11.1" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_20_title); ?></h2>
        </div>

        <div class="mf-content mf-content__sorry">
            <?php _e($step_20_description); ?>
        </div>

        <div class="text-center">
            <a href="<?php _e($step_20_button_link); ?>" class="btn filled"><?php _e($step_20_button_text); ?></a>
        </div>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.1">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:10%;"></div>
    </div>
</div>
</div>

<div class="mf-step" mf-step="11.2" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_28_title); ?></h2>
        </div>

        <div class="mf-content mf-content__sorry">
            <?php _e($step_28_description); ?>
        </div>

        <div class="text-center">
            <a href="<?php _e($step_28_button_link); ?>" class="btn filled"><?php _e($step_28_button_text); ?></a>
        </div>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.3">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:10%;"></div>
    </div>
</div>
</div>

<!--================================
                  Common Templates Ends
       =================================-->


<!--================================
                  First Info Step
       =================================-->
<div class="mf-step" mf-step="1">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_1_title); ?><span>.</span></h2>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php _e($step_1_left_content); ?>
                
                <div class="text-center">
	                <a href="#" class="btn filled mf-next"><?php _e($step_1_button_text); ?></a>
                </div>
            </div>
<!--
            <div class="col-lg-6 col-lg-offset-1">
                <?php if( have_rows('step_1_right_content') ): ?>
                        <ul class="home-lists">
                            <?php
                                while( have_rows('step_1_right_content') ): the_row();
                                    $step_1_right_content_image = get_sub_field('step_1_right_content_image');
                                    $step_1_right_content_description = get_sub_field('step_1_right_content_description');
                            ?>
                                    <li><img src="<?php _e($step_1_right_content_image);?>" /><?php _e($step_1_right_content_description);?></li>
                            <?php
                                endwhile;
                            ?>
                        </ul>
                <?php endif; ?>
                <a href="#" class="btn filled mf-next"><?php _e($step_1_button_text); ?></a>
            </div>
-->
        </div>
    </div>
</div>

<!--================================
                Second Info Step
       ================================-->
<div class="mf-step" mf-step="2" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_2_question); ?></h2>
        </div>

        <div class="img-radio">
            <div class="img-radio-item">
                <input type="radio" name="medical_form_details[personal_information][gender][answer]" value="Male" id="male">
                <label for="male" class="mf-next"> <img class="svg" src="<?php _e($step_2_male_image); ?>" /><span><?php _e($step_2_male_text); ?></span></label>
            </div>
            <div class="img-radio-item">
                <input type="radio" name="medical_form_details[personal_information][gender][answer]" value="Female" id="female">
                <label for="female" class="mf-stop" goto-step="2.1"><img class="svg" src="<?php _e($step_2_female_image); ?>" /><span><?php _e($step_2_female_text); ?></span></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[personal_information][gender][question]" value="<?php _e($step_2_question); ?>" />
    </div>

    <div class="mf-progress">
        <a href="#" class="mf-prev"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/caret-left.svg" />Back</a>
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:10%;"></div>
        </div>
    </div>
</div>

<!--================================
                Third Info Step
       ================================-->
<div class="mf-step validate" mf-step="3" style="display:none;">
    <div class="mf-step__item">
        <div class="text-center">
            <h2><?php _e($step_3_question); ?></h2>
        </div>

        <div class="animate-inputs text-center">
            <div class="animate-input">
                <input type="tel" required name="medical_form_details[personal_information][date_of_birth][answer]" id="dob" placeholder="dd/mm/yyyy" maxlength="10">
                <label for="dob"><?php _e($step_3_label); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[personal_information][date_of_birth][question]" value="<?php _e($step_3_question); ?>" />
        <!-- <div class="text-center">
            <a href="#" class="btn filled year-validate disabled"><?php _e($step_3_button_text); ?></a>
        </div> -->
    </div>

    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:15%;"></div>
    </div>
</div>

</div>

<!--================================
                Fourth Info Step
       ================================-->
<div class="mf-step" mf-step="4" style="display:none;">
    <div class="mf-step__item mf-step__hw">
        <div class="text-center">
            <h2><?php _e($step_4_question); ?></h2>
        </div>

        <div class="animate-inputs">
            <div class="mf-height">
                <div class="animate-input">
                    <input type="number" min="70" max="250" name="medical_form_details[personal_information][height][answer]" id="height">
                    <label for="height"><?php _e($step_4_height_label); ?></label>
                </div>

                <div class="chk-btn-wrap">
                    <input type="checkbox" name="medical_form_details[personal_information][height_no_info][answer]" value="<?php _e($step_4_dont_know_text); ?>" id="heightchk">
                    <label for="heightchk"><?php _e($step_4_dont_know_text); ?></label>
                </div>
            </div>
            <input type="hidden" name="medical_form_details[personal_information][height][question]" value="<?php _e($step_4_height_label); ?>" />
            <input type="hidden" name="medical_form_details[personal_information][height_no_info][question]" value="<?php _e($step_4_height_label); ?>" />
            <div class="mf-weight">
                <div class="animate-input">
                    <input min="40" max="250" type="number" name="medical_form_details[personal_information][weight][answer]" id="weight">
                    <label for="weight"><?php _e($step_4_weight_label); ?></label>
                </div>
                <div class="chk-btn-wrap">
                    <input type="checkbox" name="medical_form_details[personal_information][weight_no_info][answer]" value="<?php _e($step_4_weight_dont_know_text); ?>" id="weightchk">
                    <label for="weightchk"><?php _e($step_4_weight_dont_know_text); ?></label>
                </div>
            </div>
            <input type="hidden" name="medical_form_details[personal_information][weight][question]" value="<?php _e($step_4_weight_label); ?>" />
            <input type="hidden" name="medical_form_details[personal_information][weight_no_info][question]" value="<?php _e($step_4_weight_label); ?>" />
        </div>

        <div class="text-center">
            <a href="#" class="btn filled mf-next disabled"><?php _e($step_4_button_text); ?></a>
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:25%;"></div>
    </div>
</div>
</div>

<!--================================
                Fifth Info Step
       ================================-->
<div class="mf-step" mf-step="5" style="display:none;">
    <div class="mf-step__item mf-step__diet">
        <div class="text-center">
            <h2><?php _e($step_5_question); ?></h2>
        </div>

        <?php if (have_rows('step_5_options')) : ?>
            <div class="img-radio">
                <?php
                $counter = 0;
                while (have_rows('step_5_options')) : the_row();
                    $step_5_option_image = get_sub_field('step_5_option_image');
                    $step_5_option_text = get_sub_field('step_5_option_text');
                    ?>
                    <div class="img-radio-item">
                        <input type="radio" name="medical_form_details[personal_information][diet][answer]" value="<?php _e($step_5_option_text); ?>" id="food-<?php _e($counter); ?>">
                        <label for="food-<?php _e($counter); ?>" class="mf-next"> <img class="svg" src="<?php _e($step_5_option_image); ?>" /><span><?php _e($step_5_option_text); ?></span></label>
                    </div>
                    <?php
                    $counter++;
                endwhile;
                ?>
            </div>
            <input type="hidden" name="medical_form_details[personal_information][diet][question]" value="<?php _e($step_5_question); ?>" />
        <?php endif; ?>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:30%;"></div>
    </div>
</div>
</div>
<!--================================
                Fiftha Info Step
       ================================-->
<div class="mf-step" mf-step="5a" style="display:none;">
    <div class="mf-step__item mf-step__maintain">
        <div class="text-center">
            <h2><?php _e($step_26_question); ?></h2>
        </div>
        <?php if (have_rows('step_26_options')) : ?>
            <div class="radio-btn-wrap">
                <?php
                $counter = 0;
                while (have_rows('step_26_options')) : the_row();
                    $step_26_option = get_sub_field('step_26_option');

                    ?>
                    <div class="radio-btn">
                        <input type="radio" name="medical_form_details[sexual_activity][uses][answer]" value="<?php _e($step_26_option); ?>" id="uses-<?php _e($counter); ?>">
                        <label for="uses-<?php _e($counter); ?>" class="mf-next"><?php _e($step_26_option); ?></label>
                    </div>
                    <?php
                    $counter++;
                endwhile;
                ?>
            </div>
            <input type="hidden" name="medical_form_details[sexual_activity][uses][question]" value="<?php _e($step_26_question); ?>" />
        <?php endif; ?>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>


<!--================================
                Sixth Info Step
       ================================-->
<div class="mf-step" mf-step="6" style="display:none;">
    <div class="mf-step__item mf-step__maintain">
        <div class="text-center">
            <h2><?php _e($step_6_question); ?></h2>
        </div>

        <?php if (have_rows('step_6_options')) : ?>
            <div class="radio-btn-wrap">
                <?php
                $counter = 0;
                while (have_rows('step_6_options')) : the_row();
                    $step_6_options_text = get_sub_field('step_6_options_text');

                    ?>
                    <div class="radio-btn">
                        <input type="radio" name="medical_form_details[sexual_activity][erection][answer]" value="<?php _e($step_6_options_text); ?>" id="erection-<?php _e($counter); ?>">
                        <label for="erection-<?php _e($counter); ?>" class="mf-stop" goto-step="7.1"><?php _e($step_6_options_text); ?></label>
                    </div>
                    <?php
                    $counter++;
                endwhile;
                ?>
            </div>
            <input type="hidden" name="medical_form_details[sexual_activity][erection][question]" value="<?php _e($step_6_question); ?>" />
        <?php endif; ?>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<!--================================
                Seventh Info Step
       ================================-->
<div class="mf-step" mf-step="7" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2>
                <div class="tooltip-heading">
                    <img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/noun_Information_558363_000000.svg" />
                    <p><?php _e($step_7_info_tool_tip); ?></p>
                </div>
                Have you ever been prescribed or approved by a doctor to take <span data-content="<?php _e($step_7_sildenafil_tool_tip); ?>">Sildenafil</span> or Cialis?
            </h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[recommended_prescription][prescription][answer]" value="Yes" id="yes7">
                <label for="yes7" class="mf-next"><?php _e($step_7_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[recommended_prescription][prescription][answer]" value="None" id="no7">
                <label for="no7" class="mf-stop" goto-step="9"><?php _e($step_7_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[recommended_prescription][prescription][question]" value="Have you ever been prescribed or approved by a doctor to take Sildenafil or Cialis?" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="7.1">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<!--================================
                Eigth Info Step
       ================================-->
<div class="mf-step" mf-step="8" style="display:none;">
    <div class="mf-step__item mf-step__chkbox mf-step__chkbox__validate">
        <div class="text-center">
            <h2><?php _e($step_10_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input data-product="1" type="checkbox" name="medical_form_details[recommended_prescription][previous_use_sildenafil][answer]" value="Sildenafil" id="step7.01">
                <label for="step7.01"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_10_sildenafil_text); ?></label>
            </div>
            <div class="radio-btn">
                <input data-product="2" type="checkbox" name="medical_form_details[recommended_prescription][previous_use_cialis][answer]" value="Cialis" id="step7.02">
                <label for="step7.02"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_10_cialis_text); ?></label>
            </div>
            <div class="radio-btn">
                <input data-product="3" type="checkbox" name="medical_form_details[recommended_prescription][previous_use_cialis_daily][answer]" value="Daily Cialis" id="step7.03">
                <label for="step7.03"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_10_daily_cialis_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[recommended_prescription][previous_use_sildenafil][question]" value="<?php _e($step_10_question); ?>" />
        <input type="hidden" name="medical_form_details[recommended_prescription][previous_use_cialis][question]" value="<?php _e($step_10_question); ?>" />
        <input type="hidden" name="medical_form_details[recommended_prescription][previous_use_cialis_daily][question]" value="<?php _e($step_10_question); ?>" />
        <div class="text-center">
            <a href="#" class="btn filled mf-stop" goto-step="9"><?php _e($step_10_button_text); ?></a>

            <!-- goto both -->
            <a href="#" class="btn filled mf-products" style="display:none;"><?php _e($step_10_button_alt_text); ?></a>

            <!-- goto Cialis only -->
            <!-- <a href="#" class="btn filled mf-stop" goto-step="8.1" style="display:none;">Continue</a>  -->

            <!-- goto Sildenafil only -->
            <!-- <a href="#" class="btn filled mf-stop" goto-step="8.1" style="display:none;">Continue</a>         -->
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<!--================================
    Show Producsts if checked
================================-->
<div class="products-wrapper">
    <div class="mf-product" data-product="1" style="display:none;">
        <div class="mf-step__item mf-step__yesno">
            <div class="text-center">
                <h2><?php _e($step_11_a_question); ?></h2>
            </div>

            <div class="radio-btn-wrap">
                <div class="radio-btn">
                    <input type="radio" name="medical_form_details[recommended_prescription][sildenafil_effective][answer]" value="Yes" id="yes8a">
                    <label for="yes8a" class="next-product"><?php _e($step_11_a_yes_text); ?></label>
                </div>
                <div class="radio-btn">
                    <input type="radio" name="medical_form_details[recommended_prescription][sildenafil_effective][answer]" value="No" id="no8a">
                    <label for="no8a" class="next-product"><?php _e($step_11_a_no_text); ?></label>
                </div>
            </div>
            <input type="hidden" name="medical_form_details[recommended_prescription][sildenafil_effective][question]" value="<?php _e($step_11_a_question); ?>" />
        </div>
        <div class="mf-progress">
            <a href="#" class="mf-stop hide-products" goto-step="8">
                < Back</a> <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
        </div>
    </div>
</div>

<div class="mf-product" data-product="2" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_11_b_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[recommended_prescription][cialis_effective][answer]" value="Yes" id="yes8b">
                <label for="yes8b" class="next-product"><?php _e($step_11_b_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[recommended_prescription][cialis_effective][answer]" value="No" id="no8b">
                <label for="no8b" class="next-product"><?php _e($step_11_b_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[recommended_prescription][cialis_effective][question]" value="<?php _e($step_11_b_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="8">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<div class="mf-product" data-product="3" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_11_c_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[recommended_prescription][daily_cialis_effective][answer]" value="Yes" id="yes8c">
                <label for="yes8c" class="next-product"><?php _e($step_11_c_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[recommended_prescription][daily_cialis_effective][answer]" value="No" id="no8c">
                <label for="no8c" class="next-product"><?php _e($step_11_c_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[recommended_prescription][daily_cialis_effective][question]" value="<?php _e($step_11_c_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="8">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>
</div>

<!--================================
                Ninth Info Step
       ================================-->
<div class="mf-step" mf-step="9" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_12_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input class="yes" type="radio" name="medical_form_details[medical_history][medical_condition][answer]" value="Yes" id="yes9">
                <label for="yes9" class="mf-next"><?php _e($step_12_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input class="no" data-skip="10" type="radio" name="medical_form_details[medical_history][medical_condition][answer]" value="None" id="no9">
                <label for="no9" class="mf-stop" goto-step="9.b"><?php _e($step_12_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][medical_condition][question]" value="<?php _e($step_12_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="7">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<!--================================
                Ninth Sub Steps
       ================================-->
<div class="mf-step" mf-step="9.a" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_12_a_question); ?></h2>
        </div>

        <div class="animate-inputs animate-textarea">
            <div class="animate-input">
                <textarea class="validate-for-next" type="text" required name="medical_form_details[medical_history][medical_condition_description][answer]" id="medical-conditions" placeholder="<?php _e($step_12_a_placeholder_text); ?>"></textarea>
                <label for="medical-conditions"><?php _e($step_12_a_label); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][medical_condition_description][question]" value="<?php _e($step_12_a_question); ?>" />
        <div class="text-center">
            <a href="#" class="btn filled mf-next disabled"><?php _e($step_12_a_button_text); ?></a>
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="9">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>


<div class="mf-step end-step-validate" mf-step="9.b" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_12_b_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input class="yes" type="radio" name="medical_form_details[medical_history][medical_history][answer]" value="Yes" id="yes9b">
                <label for="yes9b" class="mf-next"><?php _e($step_12_b_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input class="no" data-skip="10" type="radio" name="medical_form_details[medical_history][medical_history][answer]" value="None" id="no9b">
                <label for="no9b" class="mf-stop" goto-step="9.d"><?php _e($step_12_b_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][medical_history][question]" value="<?php _e($step_12_b_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="9">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<div class="mf-step" mf-step="9.c" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($past_admission_question); ?></h2>
        </div>

        <div class="repeat-block">
            <div class="animate-inputs animate-textarea">
                <div class="animate-input">
                    <!-- <input type="date" name="date" id="dob"> -->
                    <input required type="tel" name="medical_form_details[medical_history][medical_history_desctiption][answer][a][date]" id="date9.c" placeholder="YYYY" maxlength="4">
                    <label for="date9.c"><?php _e($step_12_b_label); ?></label>
                </div>
                <div class="animate-input">
                    <textarea type="text" required name="medical_form_details[medical_history][medical_history_desctiption][answer][a][description]" id="textarea9.c" placeholder="Please provide details"></textarea>
                    <label for="textarea9.c"><?php _e($step_12_b_description); ?></label>
                </div>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][medical_history_desctiption][question]" value="<?php _e($past_admission_question); ?>" />
        <div id="add-more">
            <span>ADD MORE</span>
        </div>

        <div class="text-center">
            <a href="#" class="btn filled mf-next disabled">Next</a>
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="9.b">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<div class="mf-step end-step-validate" mf-step="9.d" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_12_c_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input class="yes" type="radio" name="medical_form_details[medical_history][allergies][answer]" value="Yes" id="yes9d">
                <label for="yes9d" class="mf-next"><?php _e($step_12_c_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input class="no" data-skip="10" type="radio" name="medical_form_details[medical_history][allergies][answer]" value="None" id="no9d">
                <label for="no9d" class="mf-stop" goto-step="9.1"><?php _e($step_12_c_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][allergies][question]" value="<?php _e($step_12_c_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="9.b">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<div class="mf-step" mf-step="9.e" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($allergies_description_text); ?></h2>
        </div>

        <div class="animate-inputs animate-textarea">
            <div class="animate-input">
                <textarea class="validate-for-next" type="text" required name="medical_form_details[medical_history][allergies_description][answer]" id="allergies" placeholder="Please provide details"></textarea>
                <label for="allergies"><?php _e($allergies_label); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][allergies_description][question]" value="<?php _e($allergies_description_text); ?>" />
        <div class="text-center">
            <a href="#" class="btn filled mf-next disabled"><?php _e($allergies_next_button_text); ?></a>
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop hide-products" goto-step="9.d">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:35%;"></div>
    </div>
</div>
</div>

<!--================================
                Ninth.1 Info Step
       ================================-->
<div class="mf-step" mf-step="9.1" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2 style="position:relative;">
                <div class="tooltip-heading">
                    <img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/noun_Information_558363_000000.svg" />
                    <p><?php _e( 'such as Amyl nitrate, GTN patch, Mononitrates, nitroglycerins', 'woocommerce' ); ?></p>
                </div>
            <?php _e($step_13_question); ?>
            </h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][nitrate][answer]" value="Yes" id="yes9a">
                <label class="mf-stop" for="yes9a" goto-step="11.1"><?php _e($step_13_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][nitrate][answer]" value="None" id="no9a">
                <label class="mf-next" for="no9a"><?php _e($step_13_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][nitrate][question]" value="<?php _e($step_13_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.d">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:60%;"></div>
    </div>
</div>
</div>
<!--================================
                Ninth.2 Info Step
       ================================-->
<div class="mf-step" mf-step="9.2" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($herbal_supplements_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input class="yes" type="radio" name="medical_form_details[medical_history][herbs][answer]" value="Yes" id="yes9.22">
                <label class="mf-stop" for="yes9.22" goto-step="9.2a"><?php _e($herbal_supplements_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input class="no" type="radio" name="medical_form_details[medical_history][herbs][answer]" value="None" 3 id="no9.23">
                <label class="mf-next" for="no9.23"><?php _e($herbal_supplements_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][herbs][question]" value="<?php _e($herbal_supplements_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.1">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:60%;"></div>
    </div>
</div>
</div>

<!--================================
                Ninth.3 Info Step
       ================================-->
<div class="mf-step" mf-step="9.3" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_21_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][blood_pressure_test][answer]" value="Yes" id="yes9c">
                <label class="mf-next" for="yes9c"><?php _e($step_21_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][blood_pressure_test][answer]" value="No" id="no9c">
                <label class="mf-stop" goto-step="11.2" for="no9c"><?php _e($step_21_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][blood_pressure_test][question]" value="<?php _e($step_21_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.2">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:60%;"></div>
    </div>
</div>
</div>

<!--================================
                Ninth.4 Info Step
       ================================-->
<div class="mf-step" mf-step="9.4" style="display:none;">
    <div class="mf-step__item mf-step__chkbox mf-step__stop">
        <div class="text-center">
            <h2><?php _e($step_22_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="checkbox" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="<?php _e($step_22_hypertension_text); ?>" id="step9.4">
                <label for="step9.4"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_22_hypertension_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="checkbox" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="<?php _e($step_22_hypotension_text); ?>" id="step9.42">
                <label for="step9.42"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_22_hypotension_text); ?></label>
            </div>
            <input type="checkbox" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="None" id="step9.43" checked="checked" class="mf-hidden-input">
        </div>
        <input type="hidden" name="medical_form_details[medical_history][blood_pressure_diagnosis][question]" value="<?php _e($step_22_question); ?>" />
        <div class="text-center">
            <a href="#" class="btn filled mf-next"><?php _e($step_22_button_text); ?></a>
            <a href="#" class="btn filled mf-stop" goto-step="11.1" style="display:none;"><?php _e($step_22_button_alt_text); ?></a>
        </div>

    </div>

    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.3">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:60%;"></div>
    </div>
</div>
</div>


<!--================================
                Ninth.5 Info Step
       ================================-->
<div class="mf-step" mf-step="9.5" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_23_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="Yes" id="yes9e">
                <label class="mf-stop" goto-step="11.1" for="yes9e"><?php _e($step_23_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="None" id="no9e">
                <label class="mf-next" for="no9e"><?php _e($step_23_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][lightheadedness][question]" value="<?php _e($step_23_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="9.4">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:60%;"></div>
    </div>
</div>
</div>

<!--================================
                Tenth Info Step
       ================================-->
<div class="mf-step" mf-step="10" style="display:none;">
    <div class="mf-step__item mf-step__chkbox mf-step__stop">
        <div class="text-center">
            <h2><?php _e($step_14_question); ?></h2>
        </div>

        <?php if (have_rows('step_14_options')) : ?>
            <div class="radio-btn-wrap">
                <?php
                $counter = 0;
                while (have_rows('step_14_options')) : the_row();
                    $step_14_option_text = get_sub_field('step_14_option_text');

                    ?>
                    <div class="radio-btn">
                        <input type="checkbox" name="medical_form_details[medical_history][cardiovascular_symptoms][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_14_option_text); ?>" id="symptoms-<?php _e($counter); ?>">
                        <label for="symptoms-<?php _e($counter); ?>"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_14_option_text); ?></label>
                    </div>
                    <?php
                    $counter++;
                endwhile;
                ?>
                <input type="checkbox" name="medical_form_details[medical_history][cardiovascular_symptoms][answer]" value="None" id="step10" checked="checked" class="mf-hidden-input" />
            </div>
            <input type="hidden" name="medical_form_details[medical_history][cardiovascular_symptoms][question]" value="<?php _e($step_14_question); ?>" />
        <?php endif; ?>

        <div class="text-center">
            <a href="#" class="btn filled mf-next"><?php _e($step_14_button_text); ?></a>
            <a href="#" class="btn filled mf-stop" goto-step="11.1" style="display:none;"><?php _e($step_14_button_alt_text); ?></a>
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:55%;"></div>
    </div>
</div>
</div>


<!--================================
                Eleventh Info Step
       ================================-->
<div class="mf-step" mf-step="11" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_15_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][heart_attack_past][answer]" value="Yes" id="yes11">
                <label for="yes11" class="mf-stop" goto-step="11.1"><?php _e($step_15_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][heart_attack_past][answer]" value="None" id="no11">
                <label for="no11" class="mf-next"><?php _e($step_15_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][heart_attack_past][question]" value="<?php _e($step_15_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:82%;"></div>
    </div>
</div>
</div>

<!--================================
                Twelvth Info Step
       ================================-->
<div class="mf-step" mf-step="12" style="display:none;">
    <div class="mf-step__item mf-step__yesno">
        <div class="text-center">
            <h2><?php _e($step_16_question); ?></h2>
        </div>

        <div class="radio-btn-wrap">
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][stroke_TIA][answer]" value="Yes" id="yes12">
                <label for="yes12" class="mf-stop" goto-step="11.1"><?php _e($step_16_yes_text); ?></label>
            </div>
            <div class="radio-btn">
                <input type="radio" name="medical_form_details[medical_history][stroke_TIA][answer]" value="None" id="no12">
                <label for="no12" class="mf-next"><?php _e($step_16_no_text); ?></label>
            </div>
        </div>
        <input type="hidden" name="medical_form_details[medical_history][stroke_TIA][question]" value="<?php _e($step_16_question); ?>" />
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:86%;"></div>
    </div>
</div>
</div>

<!--================================
                Thirteenth Info Step
       ================================-->
<div class="mf-step" mf-step="13" style="display:none;">
    <div class="mf-step__item mf-step__chkbox mf-step__stop">
        <div class="text-center">
            <h2><?php _e($step_17_question); ?></h2>
        </div>

        <?php if (have_rows('step_17_options')) : ?>
            <div class="radio-btn-wrap">
                <?php
                $counter = 0;
                while (have_rows('step_17_options')) : the_row();
                    $step_17_options_text = get_sub_field('step_17_options_text');
                    $step_17_option_length = get_sub_field('step_17_option_length');
                    ?>
                    <div class="radio-btn <?php _e($step_17_option_length); ?>">
                        <input type="checkbox" name="medical_form_details[medical_history][conditions_1][answer]" value="<?php _e($step_17_options_text); ?>" id="condition1-<?php _e($counter); ?>">
                        <label for="condition1-<?php _e($counter); ?>"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_17_options_text); ?></label>
                    </div>
                    <?php
                    $counter++;
                endwhile;
                ?>
                <input type="checkbox" name="medical_form_details[medical_history][conditions_1][answer]" value="None" id="step13" checked="checked" class="mf-hidden-input" />
            </div>
            <input type="hidden" name="medical_form_details[medical_history][conditions_1][question]" value="<?php _e( $step_17_question); ?>" />
        <?php endif; ?>

        <div class="text-center">
            <a href="#" class="btn filled mf-next"><?php _e($step_17_button_text); ?></a>
            <a href="#" class="btn filled mf-stop" goto-step="11.1" style="display:none;"><?php _e($step_17_button_alt_text); ?></a>
        </div>

    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:90%;"></div>
    </div>
</div>
</div>


<!--================================
                Fourteenth Info Step
       ================================-->
<div class="mf-step" mf-step="14" style="display:none">
    <div class="mf-step__item mf-step__chkbox mf-step__stop">
        <div class="text-center">
            <h2><?php _e($step_18_question); ?></h2>
        </div>

        <?php if (have_rows('step_18_options')) : ?>
            <div class="radio-btn-wrap">
                <?php
                $counter = 0;
                while (have_rows('step_18_options')) : the_row();
                    $step_18_options_text = get_sub_field('step_18_options_text');
                    $step_18_option_length = get_sub_field('step_18_option_length');
                ?>
                    <div class="radio-btn <?php _e($step_18_option_length); ?>">
                        <input type="checkbox" name="medical_form_details[medical_history][conditions_2][answer]" value="<?php _e($step_18_options_text); ?>" id="condition2-<?php _e($counter); ?>">
                        <label for="condition2-<?php _e($counter); ?>"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_18_options_text); ?></label>
                    </div>

                <?php
                    $counter++;
                endwhile;
                ?>
                <input type="checkbox" name="medical_form_details[medical_history][conditions_2][answer]" value="None" id="step14" checked="checked" class="mf-hidden-input" />
                <input type="hidden" name="medical_form_details[redirection_link][sildenafil_redirection_link]" value="<?php _e($sildenafil_redirection_link); ?>">
                <input type="hidden" name="medical_form_details[redirection_link][cialis_redirection_link]" value="<?php _e($cialis_redirection_link); ?>">
                <input type="hidden" name="medical_form_details[redirection_link][daily_cialis_redirection_link]" value="<?php _e($daily_cialis_redirection_link); ?>">
                <input type="hidden" name="medical_form_details[medical_history][conditions_2][question]" value="<?php _e($step_18_question); ?>" />

				<input type="hidden" name="page_redirect_link" value="<?php echo $page_redirect_link ?>" />

            </div>
        <?php endif; ?>

        <div class="text-center">
            <!-- <a href="#" class="btn filled mf-next">None apply</a> -->
            <a href="#" class="btn filled mf-stop" goto-step="14.1" style="display:none;"><?php _e($step_18_button_text); ?></a>
            <!-- goto final form if no surgery-->
            <!-- TESTING TESTING
			<a href="#" class="btn filled mf-stop big"><?php _e($step_18_button_text); ?></a>
			-->
            <!-- <a href="#" class="btn filled form-2-submit"><?php _e($step_18_button_text); ?></a> -->
        </div>
    </div>
	<div class="mf-progress">
        <a href="#" class="mf-prev">< Back</a>
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:95%;"></div>
        </div>
    </div>
</div>
<!-- </div> -->

<!--================================
                Fifteenth Info Step - NOT IN USE, IT REDIRECTS TO POPUP
       ================================-->
<!-- final form 3 -->
<div class="mf-step" mf-step="15" style="display:none">
    <div class="mf-step__item mf-step__rform">
        <div class="text-center">
            <h2><?php _e($step_24_title); ?></h2>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <?php _e($step_24_description); ?>
            </div>
            <div class="col-lg-5 col-lg-offset-2">
                <div class="animate-inputs">
                    <div class="animate-input">
                        <input type="text" name="mf_fullname" id="form3_rfullname" required>
                        <label for="form3-rfullname"><?php _e($step_24_name_label); ?></label>
                    </div>
                    <div class="animate-input">
                        <input type="email" name="mf_email" id="form3_remail" required>
                        <label for="form3-remail"><?php _e($step_24_email_label); ?></label>
                    </div>
                    <div class="animate-input">
                        <input type="password" name="mf_password" id="form3_rpassword" required>
                        <label for="form3-rpassword"><?php _e($step_24_password_label); ?></label>
                        <img class="svg eye" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/eye.svg" />
                    </div>
                    <p class="small">* password must contain at least 6 characters, an uppercase character and a number.</p>
                </div>
                <input type="hidden" id="flagged_user_suggested_product" name="flagged_user_suggested_product" value="">
                <!-- <input type="submit" id="mf-form-3" class="btn filled form-submit" value="<?php _e($step_24_button_text); ?>" /> -->
                <a href="#" class="btn filled form-3-btn"><?php _e($step_24_button_text); ?></a>
            </div>
        </div>
    </div>
    <div class="mf-progress">
        <a href="#" class="mf-prev">< Back</a>
        <div class="mf-progress__bar">
            <div class="mf-progress__fill" style="width:95%;"></div>
        </div>
    </div>
</div>

<!--================================
                Sixteenth Info Step
       ================================-->
<!-- final form 4 -->
<div class="mf-step" mf-step="16" style="display:none">
    <div class="mf-step__item mf-step__rform">
        <div class="text-center">
            <h2><?php _e($step_25_title); ?></h2>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <?php _e($step_25_description); ?>
            </div>
            <div class="col-lg-5 col-lg-offset-2">
                <div class="animate-inputs">
                    <div class="animate-input">
                        <textarea type="text" name="medical_form_details[additional_details][answer]" id="form4_description" placeholder="Your details..." required></textarea>
                        <label for="form4_description"><?php _e($step_25_medical_detail_label); ?></label>
                    </div>
                </div>
                <input type="hidden" name="medical_form_details[additional_details][question]" value="<?php _e( $step_25_title); ?>" />
                <input id="mf-form-4" type="submit" class="btn filled form-submit" value="<?php _e($step_25_finish_text); ?>" />
                <!-- <input id="mf-form-4" type="submit" class="btn filled form-submit" value="<?php _e($step_25_finish_text); ?>" /> -->
                <!-- <a href="#" class="btn filled"><?php _e($step_25_finish_text); ?></a> -->
            </div>
        </div>
    </div>

    <div class="mf-progress">
        <a href="#" class="mf-stop" goto-step="15">
            < Back</a> <div class="mf-progress__bar">
                <div class="mf-progress__fill" style="width:100%;"></div>
    </div>
</div>

</div>

</form>
</div>
</div>

<?php
/*Call to footer scripts*/
wp_footer();
