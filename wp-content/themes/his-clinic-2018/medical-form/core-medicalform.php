<?php
defined('ABSPATH') or die('No script kiddies please!');
define("MF_KEY_NEW", 'medical-form-new');

if (!class_exists('MedicalFormNew')) {
	class MedicalFormNew
	{
		private $fields = [
			'gender' => null,
			'date' => null,
			'height' => null,
			'heightchk' => null,
			'weightchk' => null,
			'weight' => null,
			'diet' => null,
			'uses' => null,
			'erection' => null,
			'prescription' => null,
			'heart_disease' => null,
			'previous_use_sildenafil' => null,
			'sildenafil_effective' => null,
			'previous_use_cialis' => null,
			'cialis_effective' => null,
			'previous_use_cialis_daily' => null,
			'daily_cialis_effective' => null,
			'surgeries' => null,
			'nitrate' => null,
			'blood_pressure_test' => null,
			'blood_pressure_diagnosis' => null,
			'lightheadedness' => null,
			'cardiovascular_symptoms' => null,
			'heart_attack_past' => null,
			'stroke_TIA' => null,
			'conditions_1' => null,
			'conditions_2' => null,
			'form4_description' => null,
			'form1_fullname' => null,
			'form1_email' => null,
			'form1_password' => null,
			'form2_fullname' => null,
			'form2_email' => null,
			'form2_password' => null,
			'form3_fullname' => null,
			'form3_email' => null,
			'form3_password' => null,
			'heart_redirection' => null,
			'sildenafil_redirection_link' => null,
			'cialis_redirection_link' => null,
		];

		public function __construct($data = [])
		{
			foreach ($data as $key => $value) {
				if (array_key_exists($key, $this->fields)) {
					$this->fields[$key] = $value;
				}
			}
		}

		public function get_serialized_data()
		{
			return json_encode($this->fields);
		}
	}
}
if (!class_exists('Medical_Form')) {
	class Medical_Form{
		function __construct(){
			$this->define_constants();
			add_action('admin_enqueue_scripts', array($this, 'mf_backend_assets'));
			add_action('wp_enqueue_scripts', array($this, 'mf_frontend_assets'));
			add_action('upload_mimes', array($this, 'add_file_types_to_uploads'));
			add_action('wp_ajax_new_process_medical_form', array($this, 'new_process_medical_form'));
			add_action('wp_ajax_nopriv_new_process_medical_form', array($this, 'new_process_medical_form'));

			add_action('wp_ajax_medical_form_store_cookie', array($this, 'medical_form_store_cookie'));
			add_action('wp_ajax_nopriv_medical_form_store_cookie', array($this, 'medical_form_store_cookie'));

			add_action('wp_ajax_new_process_medical_form_popup', array($this, 'new_process_medical_form_popup'));
			add_action('wp_ajax_nopriv_new_process_medical_form_popup', array($this, 'new_process_medical_form_popup'));
			add_action('restrict_manage_posts', array($this, 'add_export_button'));
//			add_action( 'restrict_manage_posts', array( $this, 'hc_import_sync_all_users' ));
			add_action('init', array($this, 'func_export_all_posts'));
			add_action('wp_footer', array($this, 'mf_model_sign_up'));
		}

		/*passes success form parameters to the pop up form*/
		function medical_form_store_cookie()
		{
			$fields = array();

			foreach ($_POST as $k => $v) {
				if (isset($_POST[$k])) {
					$fields[$k] = $_POST[$k];
				}
			}

			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}

			if (isset($_SESSION['medical_form_data'])) {
				$_SESSION['medical_form_data'] = '';
			}
			$_SESSION['medical_form_data'] = $fields;

			wp_send_json_success();
			wp_die();
		}

		/*pop up modal HTML*/
		function mf_model_sign_up()
		{
			$fields = array();

			if( isset( $_SESSION['medical_form_data'] ) && ! empty( $_SESSION['medical_form_data'] ) ) {

				foreach ($_SESSION['medical_form_data'] as $k => $v) {
					if (isset($_SESSION['medical_form_data'])) {
						$fields[$k] = $_SESSION['medical_form_data'][$k];
					}
				}

			}

			$serialized_fields = base64_encode(serialize($fields));
			/*echo '<pre>';
			print_r($serialized_fields);
			echo '</pre>'; */

			if (isset($_GET)) {
				$modal_open = isset($_GET['sign_up']) ? $_GET['sign_up'] : '';
				$redirection = isset($_GET['redirection']) ? $_GET['redirection'] : '';
				if (isset($modal_open) && !empty($modal_open) && $modal_open == 'true' && !is_user_logged_in()) {


					/*echo '<pre>';
						print_r($_GET);
						echo '</pre>';*/
					$pop_up_question = get_option('pop_up_questions');
					$step_19_title = isset($pop_up_question['step_19_title']) ? sanitize_text_field($pop_up_question['step_19_title']) : 'Our doctors have recommended the right treatment for you';
					$step_19_description = isset($pop_up_question['step_19_description']) ? ($pop_up_question['step_19_description']) : '<p>Based on your medical history and individual needs, our doctors have provided a personalised treatment</p><p>Please complete your account to view your prescription.</p>';
					$step_19_name_label = isset($pop_up_question['step_19_name_label']) ? sanitize_text_field($pop_up_question['step_19_name_label']) : 'Full Name';
					$step_19_last_name_label = isset($pop_up_question['step_19_last_name_label']) ? sanitize_text_field($pop_up_question['step_19_last_name_label']) : 'Last Name';
					$step_19_email_label = isset($pop_up_question['step_19_email_label']) ? sanitize_text_field($pop_up_question['step_19_email_label']) : 'Email';
					$step_19_password_label = isset($pop_up_question['step_19_password_label']) ? sanitize_text_field($pop_up_question['step_19_password_label']) : 'Password';
					$step_19_button_label = isset($pop_up_question['step_19_button_label']) ? sanitize_text_field($pop_up_question['step_19_button_label']) : 'Complete account to order';

					?>
				<div id="MF_SIGNUP_MODAL" class="modal" style="display: block;">
					<div class="modal-content">
						<div class="mf-step" mf-step="7.2" style="/* display:none; */">
							<div class="mf-step__item mf-step__rform">
								<div class="text-center">
									<h2><?php _e($step_19_title); ?></h2>
								</div>
								<div class="row">
									<div class="col-lg-5">
										<?php _e($step_19_description); ?>
									</div>
									<form id="md_popup_signup" action="">
										<div class="col-lg-6 col-lg-offset-1">
											<div class="animate-inputs">
												<div class="animate-input">
													<input type="text" name="form2_first_name" id="form2-rfirstname" required>
													<label for="form2-rfirstname"><?php _e($step_19_name_label); ?></label>
												</div>
												<div class="animate-input">
													<input type="text" name="form2_last_name" id="form2-rlastname" required>
													<label for="form2-rlastname"><?php _e($step_19_last_name_label); ?></label>
												</div>
												<div class="animate-input">
													<input type="email" name="form2_email" id="form2-remail" required>
													<label for="form2-remail"><?php _e($step_19_email_label); ?></label>
												</div>
												<div class="animate-input">
													<input type="password" name="form2_password" id="form2-rpassword" required>
													<label for="form2-rpassword"><?php _e($step_19_password_label); ?></label>
													<img class="svg eye" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/eye.svg" />
												</div>
											</div>
											<p class="small">* password must contain at least 6 characters, an uppercase character and a number.</p>
											<input type="hidden" name="medical_form_data" value="<?php echo $serialized_fields; ?>" />
											<input type="hidden" name="redirection" value="<?php echo $redirection; ?>" />

											<input type="submit" id="mf-form-2" class="btn filled form-submit" value="<?php _e($step_19_button_label); ?>" />
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>

				</div>
			<?php
			}
		} else {
			return;
		}
	}

	/*Add export button*/
	function add_export_button()
	{
		$screen = get_current_screen();

		if (isset($screen->parent_file) && (('medical-forms-2' == $screen->parent_file) || ('users.php' == $screen->parent_file))) {
			?>
			<input type="submit" name="export_all_posts" id="export_all_posts" class="button button-primary" value="Export Medical Details">
			<script type="text/javascript">
				jQuery(function($) {
					$('#export_all_posts').insertAfter('#post-query-submit');
				});
			</script>
		<?php
		}
	}

	/** Add user merge button */
	function hc_import_sync_all_users() {
		?>
			<button id="hc-merge-old-medical-form-users" class="button button-primary"><?php _e( 'Merge Old Medical Data', 'woocommerce' ); ?></button>
			<button id="hc-merge-gravity-form-users" class="button button-primary"><?php _e( 'Merge Gravity forms Data', 'woocommerce' ); ?></button>
			
			<button id="hc-merge-orders-allergies-question" class="button button-primary"><?php _e( 'Merge user checkout allergies question', 'woocommerce' ); ?></button>

		<?php 
	}


	/*Export function*/
	function func_export_all_posts()
	{
		if (isset($_GET['export_all_posts'])) {
			$user_query = new WP_User_Query(array('role' => 'Customer'));

			if (!empty($user_query->get_results())) {
				$questions = get_option('medical_form_questions');

				$questions_new['name'] = 'Name';
				$questions_new['email'] = 'Email';
				$questions_new['gender'] = $questions['gender'];
				$questions_new['date'] = $questions['date'];
				$questions_new['height'] = $questions['height'];
				$questions_new['weight'] = $questions['weight'];
				$questions_new['diet'] = $questions['diet'];
				$questions_new['uses'] = $questions['uses'];
				$questions_new['erection'] = $questions['erection'];
				$questions_new['prescription'] = $questions['prescription'];
				$questions_new['heart_disease'] = $questions['heart_disease'];
				$questions_new['previous_use_sildenafil'] = $questions['previous_use_sildenafil'];
				$questions_new['sildenafil_effective'] = $questions['sildenafil_effective'];
				$questions_new['previous_use_cialis'] = $questions['previous_use_cialis'];
				$questions_new['cialis_effective'] = $questions['cialis_effective'];
				$questions_new['previous_use_cialis_daily'] = $questions['previous_use_cialis_daily'];
				$questions_new['daily_cialis_effective'] = $questions['daily_cialis_effective'];
				$questions_new['surgeries'] = $questions['surgeries'];
				$questions_new['nitrate'] = $questions['nitrate'];
				$questions_new['blood_pressure_test'] = $questions['blood_pressure_test'];
				$questions_new['blood_pressure_diagnosis'] = $questions['blood_pressure_diagnosis'];
				$questions_new['lightheadedness'] = $questions['lightheadedness'];
				$questions_new['cardiovascular_symptoms'] = $questions['cardiovascular_symptoms'];
				$questions_new['heart_attack_past'] = $questions['heart_attack_past'];
				$questions_new['stroke_TIA'] = $questions['stroke_TIA'];
				$questions_new['conditions_1'] = $questions['conditions_1'];
				$questions_new['conditions_2'] = $questions['conditions_2'];
				$questions_new['form4_description'] = $questions['form4_description'];

				header('Content-type: text/csv');
				header('Content-Disposition: attachment; filename="medical-details-' . date('Y-m-d_H:i:s') . '.csv"');
				header('Pragma: no-cache');
				header('Expires: 0');

				$file = fopen('php://output', 'w');

				fputcsv($file, $questions_new);

				foreach ($user_query->get_results() as $user) {

					$form = get_user_meta($user->ID, 'medical-form-new', true);

					if (!empty($form)) {
						$form = json_decode($form);

						$height = '';
						if (isset($form->height) && !empty($form->height)) {
							$height = $form->height;
						} else {
							$height = $form->heightchk;
						}

						$weight = '';
						if (isset($form->weight) && !empty($form->weight)) {
							$weight = $form->weight;
						} else {
							$weight = $form->weightchk;
						}

						$form_new = array();

						$form_new[] = get_full_name($user->ID);
						$form_new[] = $user->user_email;
						$form_new[] = isset($form->gender) ? $form->gender : '';
						$form_new[] = isset($form->date) ? $form->date : '';

						$form_new[] = $height;
						$form_new[] = $weight;

						$form_new[] = isset($form->diet) ? $form->diet : '';
						$form_new[] = isset($form->uses) ? $form->uses : '';
						$form_new[] = isset($form->erection) ? $form->erection : '';
						$form_new[] = isset($form->prescription) ? $form->prescription : '';
						$form_new[] = isset($form->heart_disease) ? $form->heart_disease : '';
						$form_new[] = isset($form->previous_use_sildenafil) ? $form->previous_use_sildenafil : '';
						$form_new[] = isset($form->sildenafil_effective) ? $form->sildenafil_effective : '';
						$form_new[] = isset($form->previous_use_cialis) ? $form->previous_use_cialis : '';
						$form_new[] = isset($form->cialis_effective) ? $form->cialis_effective : '';
						$form_new[] = isset($form->previous_use_cialis_daily) ? $form->previous_use_cialis_daily : '';
						$form_new[] = isset($form->daily_cialis_effective) ? $form->daily_cialis_effective : '';
						$form_new[] = isset($form->surgeries) ? $form->surgeries : '';
						$form_new[] = isset($form->nitrate) ? $form->nitrate : '';
						$form_new[] = isset($form->blood_pressure_test) ? $form->blood_pressure_test : '';
						$form_new[] = isset($form->blood_pressure_diagnosis) ? $form->blood_pressure_diagnosis : '';
						$form_new[] = isset($form->lightheadedness) ? $form->lightheadedness : '';
						$form_new[] = isset($form->cardiovascular_symptoms) ? $form->cardiovascular_symptoms : '';
						$form_new[] = isset($form->heart_attack_past) ? $form->heart_attack_past : '';
						$form_new[] = isset($form->stroke_TIA) ? $form->stroke_TIA : '';
						$form_new[] = isset($form->conditions_1) ? $form->conditions_1 : '';
						$form_new[] = isset($form->conditions_2) ? $form->conditions_2 : '';
						$form_new[] = isset($form->form4_description) ? $form->form4_description : '';

						fputcsv($file, $form_new);
					}
				}
				exit();
			} else {
				echo 'No users found.';
			}
		}
	}

	function define_constants()
	{
		defined('MF_VERSION') or define('MF_VERSION', '1.0.1');
	}

	function mf_backend_assets()
	{
		wp_enqueue_style('mf-backend-style', get_template_directory_uri() . '/medical-form/css/mf_backend.css', array(), MF_VERSION);
		
		$screen = get_current_screen();
		
		wp_register_script('mf-backend-js', get_template_directory_uri() . '/medical-form/js/mf_backend.js', array('jquery'), MF_VERSION);

		wp_localize_script('mf-backend-js', 'new_wp_paths', ['new_admin' => admin_url('admin-ajax.php'), 'new_theme' => theme(), 'new_home_url' => home_url()]);

		wp_enqueue_script( 'mf-backend-js' );

		if( 'users_page_medical-forms-new' === $screen->id ) {
			
			wp_enqueue_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), MF_VERSION );
			wp_enqueue_script( 'bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), MF_VERSION );
			wp_enqueue_style( 'hisclinic-product-css', get_template_directory_uri() . '/assets/css/products.css', array(), MF_VERSION );
			wp_enqueue_style( 'hisclinic-custom-css', get_template_directory_uri() . '/assets/css/custom.css', array(), MF_VERSION );

			wp_enqueue_style( 'hisclinic-woocommerce account-css', get_template_directory_uri() . '/assets/css/waccount.css', array(), MF_VERSION );

			wp_enqueue_script( 'theme-validate', get_template_directory_uri() . '/assets/js/jquery.validate.min.js', array( 'jquery' ), MF_VERSION );
			wp_enqueue_script( 'theme-mask', get_template_directory_uri() . '/assets/js/jquery.mask.min.js', array( 'jquery' ), MF_VERSION );
			wp_enqueue_script( 'jquery-slick-bckend', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', ['jquery'], '', true );

			wp_enqueue_script( 'theme-script-for-backend', get_template_directory_uri() . '/assets/js/scripts.js', array( 'jquery', 'wp-util' ), MF_VERSION );

			wp_localize_script(
				'theme-script-for-backend',
				'wp_paths',
				[
					'admin' => admin_url('admin-ajax.php'),
					'theme' => theme(),
					'home_url' => home_url(),
					'mf_admision_date' => get_field('step_12_b_label'),
					'mf_admision_description' =>get_field('step_12_b_description'),
				]
			);
		}
	}

	function mf_frontend_assets()
	{
		wp_enqueue_style('mf-frontend-style', get_template_directory_uri() . '/medical-form/css/mf_frontend.css', array(), MF_VERSION);
		wp_enqueue_script('mf-frontend-js', get_template_directory_uri() . '/medical-form/js/mf_frontend.js', array('jquery'), MF_VERSION);
		wp_localize_script('mf-frontend-js', 'new_wp_paths', ['new_admin' => admin_url('admin-ajax.php'), 'new_theme' => theme(), 'new_home_url' => home_url()]);
	}

	function print_array($array)
	{
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}

	function add_file_types_to_uploads($file_types)
	{
		$new_filetypes = array();
		$new_filetypes['svg'] = 'image/svg+xml';
		$file_types = array_merge($file_types, $new_filetypes);
		return $file_types;
	}

	function new_process_medical_form_popup()
	{
		if (!$_POST) {
			return false;
		}

		$client_info = array();
		$client_info['first_name'] = $_POST['form2_first_name'];
		$client_info['last_name'] = $_POST['form2_last_name'];
		$client_info['email'] = $_POST['form2_email'];
		$client_info['password'] = $_POST['form2_password'];

		$suggested_product_url = $_POST['redirection'];

		$fields = unserialize(base64_decode($_POST['medical_form_data']));

		$valid = 'true';

		if (!filter_var($client_info['email'], FILTER_VALIDATE_EMAIL)) {
			$valid = 'false';
		}

		$success = false;
		$message = 'Success';

		if ($valid) {
			$approved = 1;

			// Checking user does not exist
			if (!get_user_by('email', $client_info['email'])) {
				$user_id = wc_create_new_customer($client_info['email'], $client_info['email'], $client_info['password']);

				if ($user_id) {
					update_user_meta($user_id, 'first_name', $client_info['first_name']);
					update_user_meta($user_id, 'last_name', $client_info['last_name']);
					update_user_meta($user_id, 'approved', $approved);
					update_user_meta($user_id, 'suggested_product', $suggested_product_url);
					//wp_set_current_user($user_id);
					wp_set_auth_cookie($user_id, true);

					if (save_medical_form_data($user_id, $fields)) {
						$success = true;

						update_user_meta( $user_id, 'mf-personal_information-updated', time() );
						update_user_meta( $user_id, 'mf-sexual_activity-updated', time() );
						update_user_meta( $user_id, 'mf-medical_history-updated', time() );
					} else {
						$message = 'There was a problem when saving data, please try again.';
					}

					/*Order Starts*/
					global $woocommerce;

					$address = array(
					'first_name' => 'Joe',
					'last_name'  => 'Conlin',
					'company'    => 'Speed Society',
					'email'      => 'joe@testing.com',
					'phone'      => '760-555-1212',
					'address_1'  => '123 Main st.',
					'address_2'  => '104',
					'city'       => 'San Diego',
					'state'      => 'Ca',
					'postcode'   => '92121',
					'country'    => 'US'
					);

					// Now we create the order
					$order = wc_create_order(array('customer_id'=> $user_id));

					// The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
					$order->add_product( get_product('38411'), 1); // This is an existing SIMPLE product
					//$order->set_address( $address, 'billing' );
					//
					$order->calculate_totals();
					$order->update_status("wc-doctorquestions", 'Imported order', TRUE); 

					/*Order Ends*/

					if (!$approved) {
						$args = [
							'subject' => 'New customer that requires verification.',
							'heading' => 'New customer checkup',
							'content' => "
		                            <p>Hi, there is a new customer that didn't pass the medical form. Details are below:</p>
		                            <p>User #{$user_id}</p>
		                            <p>{$fields['first-name']} {$fields['last-name']}, {$fields['email']}</p>
		                        ",
						];
						email_admin($args);

						$args = [
							'name' => $fields['first-name'] . ' ' . $fields['last-name'],
							'email' => $fields['email'],
						];
						email_customer($args);
					}
				} else {
					$message = 'There was a problem when creating the user, please try again.';
				}
			} else {
				$message = 'The email you entered is already registered. Please login.';
			}
		} else {
			$message = 'There was a problem with your form, please check the form and try again.';
		}

		echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_SLASHES);
		exit;
	}

	function new_process_medical_form()
	{
		if (!$_POST) {
			return false;
		}

		$client_info = array();
		$client_info['name'] = $_POST['mf_fullname'];
		$client_info['email'] = $_POST['mf_email'];
		$client_info['password'] = $_POST['mf_password'];

		$valid = 'true'; 

		$fields = array();
		foreach ($_POST as $k => $v) {
			if (isset($_POST[$k])) {
				$fields[$k] = $_POST[$k];
			}
		}

		unset($fields['mf_password']);

		if (!filter_var($client_info['email'], FILTER_VALIDATE_EMAIL)) {
			$valid = 'false';
		}

		$success = false;
		$message = 'Success';
		$redirection = home_url( '/' );

		if ($valid) {
			$approved = 0;

			// Checking user does not exist
			if (!get_user_by('email', $client_info['email'])) {
				$user_id = wc_create_new_customer($client_info['email'], $client_info['email'], $client_info['password']);

				// $default_sgst_prod_id   = function_exists( 'get_field' ) ? get_field( 'default_product', 'option' ) : '';

				$default_sgst_prod = $_POST['flagged_user_suggested_product'];

				if ($user_id) {
					update_user_meta($user_id, 'first_name', $client_info['name']);
					update_user_meta($user_id, 'approved', $approved);
					update_user_meta($user_id, 'suggested_product', $default_sgst_prod );
					//wp_set_current_user($user_id);
					wp_set_auth_cookie($user_id, true);

					if (save_medical_form_data($user_id, $fields)) {
						$success = true;

						update_user_meta( $user_id, 'mf-personal_information-updated', time() );
						update_user_meta( $user_id, 'mf-sexual_activity-updated', time() );
						update_user_meta( $user_id, 'mf-medical_history-updated', time() );
					} else {
						$message = 'There was a problem when saving data, please try again.';
					}

					if (!$approved) {
						$args = [
							'subject' => 'New customer that requires verification.',
							'heading' => 'New customer checkup',
							'content' => "
		                            <p>Hi, there is a new customer that didn't pass the medical form. Details are below:</p>
		                            <p>User #{$user_id}</p>
		                            <p>{$fields['first-name']} {$fields['last-name']}, {$fields['email']}</p>
		                        ",
						];
						email_admin($args);

						$args = [
							'name' => $fields['first-name'] . ' ' . $fields['last-name'],
							'email' => $fields['email'],
						];
						email_customer($args);
					}
				} else {
					$message = 'There was a problem when creating the user, please try again.';
				}
			} else {
				$message = 'The email you entered is already registered. Please login.';
			}
		} else {
			$message = 'There was a problem with your form, please check the form and try again.';
		}

		echo json_encode(['success' => $success, 'message' => $message, 'redirection' => $redirection], JSON_UNESCAPED_SLASHES);
		exit;
	}
}

$medical_form_obj = new Medical_Form();
}
if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

require get_template_directory() . '/medical-form/backend-listing-medicalform.php';

class Medical_Form_List_2 extends WP_List_Table
{
	public function __construct()
	{
		parent::__construct([
			'singular' => 'Medical Form 2',
			'plural'   => 'Medical Forms 2',
			'ajax'     => false //should this table support ajax?
		]);
	}

	public static function get_users($per_page = 20, $page_number = 1)
	{
		global $wpdb;

		$args = [
			'role' => 'customer',
			'meta_query' => [
				[
					'key' => MF_KEY_NEW,
					'compare' => 'EXISTS',
				]
			],
			'number' => $per_page,
			'paged' => $page_number,
			'orderby' => 'ID',
			'order' => 'DESC',
		];

		// if ( ! empty( $_REQUEST['orderby'] ) ) {
		// 	$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		// 	$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		// }

		return get_users($args);
	}

	public function record_count()
	{
		$users = $this->get_users(-1);

		return count($users);
	}

	public function no_items()
	{
		_e('No forms avaliable.', 'medical-form');
	}

	protected function display_tablenav($which)
	{
		?>
		<div class="tablenav <?php echo esc_attr($which); ?>">
			<?php
			$this->extra_tablenav($which);
			$this->pagination($which);
			?>
			<br class="clear" />
		</div>
		<?php
	}

	public function column_default($item, $column_name)
	{
		$user_id = $item->ID;
		switch ($column_name) {
			case 'id':
				$url = get_admin_url() . 'users.php?page=medical-forms-2&user_id=' . $item->ID;
				return "<a href='$url'>#{$item->ID}</a>";
			case 'user_fullname':
				return get_full_name($user_id);
			case 'user_email':
				return $item->$column_name;
			case 'flagged':
				$form = get_user_meta($user_id, MF_KEY_NEW, true);
				$flagged_for_review = get_user_meta($user_id, 'flagged_for_review', true);
				$form = json_decode($form);
				$flagged = 'No';
				foreach ($form as $key => $value) {
					if (
						($key == 'heart_disease' && $value === 'Yes') || ($key == 'surgeries' && $value === 'Yes') || ('true' === $flagged_for_review)
					) {
						$flagged = '<p style="background-color: #fa1c41; color: white; padding:3px;">YES</p>';
						break;
					}
				}
				return $flagged;
			case 'previous_orders':
				$args = array(
					'customer_id'   => $user_id,
					'limit'         => 1,
				);
				$order = wc_get_orders($args);
				if (!empty($order)) {
					return '<a href="' . get_admin_url() . 'edit.php?post_status=all&post_type=shop_order&_customer_user=' . $user_id . '">View orders</a>';
				} else {
					return '-';
				}

			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	function get_columns()
	{
		$columns = [
			'id' => 'User ID',
			'user_fullname' => 'Customer name',
			'user_email' => 'Email',
			'flagged' => 'Flagged',
			'previous_orders' => 'Previous orders',
		];

		return $columns;
	}

	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'email' => array('email', true),
			'created' => array('created', true),
			'updated' => array('updated', true),
		);

		return $sortable_columns;
	}

	protected function extra_tablenav($which)
	{
		?>
		<div class="alignleft actions">
			<?php
			if ('top' === $which && !is_singular()) {

				do_action('restrict_manage_posts', $this->screen->post_type, $which);

				//submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			}
			?>
		</div>
		<?php
		do_action('manage_posts_extra_tablenav', $which);
	}

	public function prepare_items()
	{
		$this->_column_headers = $this->get_column_info();

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = $this->record_count();

		$this->set_pagination_args([
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_users($per_page, $current_page);
	}
}

class Medical_Form_Index_2
{
	static $instance;
	public $forms_obj;

	public function menu()
	{
		add_submenu_page(
			'users.php',
			'Medical Forms 2',
			'Medical Forms 2',
			'manage_options',
			// 'medical-forms-2',
			[$this, 'body']
		);
		add_menu_page('Medical Forms 2', 'Medical Forms 2', 'prescribe_capability', 'medical-forms-2', [$this, 'body']);

		$this->forms_obj = new Medical_Form_List_2();
	}

	public function body()
	{
		?>
		<div class="wrap">
			<h2>Medical Forms</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form action="<?php echo admin_url() ?>admin.php" method="get">
								<input type="hidden" name="page" value="medical_forms">
								<?php
								$this->forms_obj->prepare_items();
								$this->forms_obj->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	public static function get_instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

class Medical_Form_Single_2
{
	static $instance;
	public $forms_obj;

	public function menu()
	{
		add_submenu_page(
			'users.php',
			'Medical Forms 2',
			'Medical Forms 2',
			'manage_options',
			'medical-forms-2',
			[$this, 'body']
		);

		$this->forms_obj = new Medical_Form_List_2();
	}

	public function body()
	{
		$user_id = $_GET['user_id'];

		if (!$user = get_user_by('id', $user_id)) {
			echo "Invalid User";
			return false;
		}

		if (!$form = get_user_meta($user_id, MF_KEY_NEW, true)) {
			echo "User does not have a medical form";
			return false;
		} else {
			$form = json_decode($form);
		}

		$user_data = get_userdata($user_id);

		$questions = get_option('medical_form_questions');

		/*echo '<pre>';
        print_r($questions);
        echo '</pre>';

        echo '<pre>';
        print_r($form);
        echo '</pre>';*/

		?>
		<div class="wrap">
			<h2>Medical Form</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<table class="widefat striped">
							<tr>
								<td>Name</td>
								<td><?php echo get_full_name($user_data->ID) ?></td>
							</tr>
							<tr>
								<td>Email</td>
								<td><?php echo $user_data->user_email ?> </td>
							</tr>

							<?php foreach ($form as $key => $value) : ?>
								<?php
								//echo $key.'<br>';
								switch ($key) {
									case 'form1_fullname':
										$value = '';
										break;
									case 'form1_email':
										$value = '';
										break;
									case 'form1_password':
										$value = '';
										break;
									case 'form2_fullname':
										$value = '';
										break;
									case 'form2_email':
										$value = '';
										break;
									case 'form2_password':
										$value = '';
										break;
									case 'form3_fullname':
										$value = '';
										break;
									case 'form3_email':
										$value = '';
										break;
									case 'form3_password':
										$value = '';
										break;
									case 'heart_disease':
										$question = $questions[$key];
										break;
									case 'heart_redirection':
										$value = '';
										break;
									case 'sildenafil_redirection_link':
										$value = '';
										break;
									case 'cialis_redirection_link':
										$value = '';
										break;
									case 'sildenafil_effective':
										$question = $questions[$key];
										break;
									case 'cialis_effective':
										$question = $questions[$key];
										break;
									case 'daily_cialis_effective':
										$question = $questions[$key];
										break;
									case 'previous_use_sildenafil':
										$question = $questions[$key];
										break;
									case 'previous_use_cialis':
										$question = $questions[$key];
										break;
									case 'previous_use_cialis_daily':
										$question = $questions[$key];
										break;
									case 'gender':
										$question = $questions[$key];
										break;
									case 'date':
										$question = $questions[$key];
										break;
									case 'height':
										$question = $questions[$key];
										break;
									case 'heightchk':
										$question = $questions[$key];
										break;
									case 'weightchk':
										$question = $questions[$key];
										break;
									case 'weight':
										$question = $questions[$key];
										break;
									case 'diet':
										$question = $questions[$key];
										break;
									case 'uses':
										$question = $questions[$key];
										break;
									case 'erection':
										$question = $questions[$key];
										break;
									case 'prescription':
										$question = $questions[$key];
										break;
									case 'surgeries':
										$question = $questions[$key];
										break;
									case 'nitrate':
										$question = $questions[$key];
										break;
									case 'blood_pressure_test':
										$question = $questions[$key];
										break;
									case 'blood_pressure_diagnosis':
										$question = $questions[$key];
										break;
									case 'lightheadedness':
										$question = $questions[$key];
										break;
									case 'cardiovascular_symptoms':
										$question = $questions[$key];
										break;
									case 'heart_attack_past':
										$question = $questions[$key];
										break;
									case 'stroke_TIA':
										$question = $questions[$key];
										break;
									case 'conditions_1':
										$question = $questions[$key];
										break;
									case 'conditions_2':
										$question = $questions[$key];
										break;
									case 'form4_description':
										$question = $questions[$key];
										break;
								}
								if (
									($key == 'heart_disease' && $value === 'Yes') || ($key == 'surgeries' && $value === 'Yes')
								) {
									$flag = 'background-color: #fa1c41; color: white;';
								} else {
									$flag = '';
								}
								if (!empty($value)) {
									?>
									<tr>
										<td style="<?php echo $flag; ?>"><?php echo $question; ?></td>
										<td style="<?php echo $flag; ?>"><?php echo ($value) ? $value : 'n/a' ?></td>
									</tr>
								<?php
								}
								?>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	public static function get_instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

function medical_forms_admin_page_2()
{
	if (empty($_GET['user_id'])) {
		$medical_form = Medical_Form_Index_2::get_instance();
	} else {
		$medical_form = Medical_Form_Single_2::get_instance();
	}

	$medical_form->menu();
}
// add_action('admin_menu', 'medical_forms_admin_page_2');

?>
