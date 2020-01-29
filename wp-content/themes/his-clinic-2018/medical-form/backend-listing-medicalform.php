<?php defined('ABSPATH') or die('No script kiddies please!');

define("MF_KEY_NEW_2", 'medicalform-new');
define("MF_UNREAD_QUERY_KEY", 'unread-query');

class Medical_Form_List_New extends WP_List_Table{
    private $query_args = [
        'meta_query' => [
            [
                'key' => MF_KEY_NEW_2,
                'compare' => 'EXISTS',
            ]
        ],
        'orderby' => 'ID',
        'order' => 'DESC',
    ];

    private $medical_forms = null;
    private $support_queries = null;
    private $unread_support_queries = null;
    
    private $per_page = 20;

    public function __construct(){
        parent::__construct([
            'singular' => 'Medical Form',
            'plural'   => 'Medical Forms',
            'ajax'     => false //should this table support ajax?
        ]);
        
        $current_page = $this->get_pagenum();

        $this->medical_forms = $this->get_users($this->per_page, $current_page);
        $this->support_queries = $this->get_users($this->per_page, $current_page, 'support_queries');
        $this->unread_support_queries = $this->get_users($this->per_page, $current_page, 'unread_support_queries');
    }

    public function get_users($per_page = 20, $page_number = 1, $view = null){
        global $wpdb;

        $args = $this->query_args;

        if ($view == 'support_queries') {
            $args['meta_query'][] = [
                'key' => 'hc_dr_support_messages',
                'compare' => 'EXISTS',
            ];
        } else if ($view == 'unread_support_queries') {
            $args['meta_query'][] = [
                'key' => MF_UNREAD_QUERY_KEY,
                'value' => true,
            ];
        }

        $args['number'] = $per_page;
        $args['paged'] = $page_number;

        return new WP_User_Query($args);
    }

    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();

        $view = (!empty($_REQUEST['view'])) ? $_REQUEST['view'] : null;

        if ($view == 'support_queries') {
            $obj = $this->support_queries;
        } else if ($view == 'unread_support_queries') {
            $obj = $this->unread_support_queries;
        } else {
            $obj = $this->medical_forms;
        }
        
        $this->items = $obj->get_results();

        $this->set_pagination_args([
            'total_items' => $obj->get_total(), //WE have to calculate the total number of items
            'per_page'    => $this->per_page //WE have to determine how many items to show on a page
        ]);
    }

    public function no_items(){
        _e('No forms available.', 'medicalform-new');
    }

    protected function display_tablenav($which){
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

    public function column_default($item, $column_name){
        $user_id = $item->ID;
        switch ($column_name) {
            case 'id':
                $url = get_admin_url() . 'users.php?page=medical-forms-new&user_id=' . $item->ID;
            return "<a href='$url'>#{$item->ID}</a>";

            case 'user_fullname':
                return get_full_name($user_id);
            case 'user_email':
            return $item->$column_name;

            case 'flagged':
                $form = get_medical_form_data($user_id);
                $flagged_for_review = get_user_meta($user_id, 'approved', true);
                $flagged = 'No';
                if($flagged_for_review != 1){
                    $flagged = '<p style="background-color: #fa1c41; color: white; padding:3px;">YES</p>';
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

    function get_columns(){
        $columns = [
            'id' => 'User ID',
            'user_fullname' => 'Customer name',
            'user_email' => 'Email',
            'flagged' => 'Flagged',
            'previous_orders' => 'Previous orders',
        ];

        return $columns;
    }

    public function get_sortable_columns(){
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
            }
            ?>
        </div>
        <?php
        do_action('manage_posts_extra_tablenav', $which);
    }

    public function get_views()
    {
        $views = array();
        $view = (!empty($_REQUEST['view'])) ? $_REQUEST['view'] : 'all';
     
        // All
        $class = ($view == 'all') ? ' class="current"' : '';
        $all_url = remove_query_arg('view');
        $views['all'] = "<a href='{$all_url }' {$class} >All ({$this->medical_forms->get_total()})</a>";
     
        // All Support Queries
        $support_queries = add_query_arg('view', 'support_queries');
        $class = ($view == 'support_queries') ? ' class="current"' : '';
        $views['support_queries'] = "<a href='{$support_queries}' {$class} >Support Queries ({$this->support_queries->get_total()})</a>";
        
        // Unread Support Queries
        $unread_support_queries = add_query_arg('view', 'unread_support_queries');
        $class = ($view == 'unread_support_queries') ? ' class="current"' : '';
        $views['unread_support_queries'] = "<a href='{$unread_support_queries}' {$class} >Unread Support Queries ({$this->unread_support_queries->get_total()})</a>";
     
        return $views;
    }
}

class Medical_Form_Index_New{
    static $instance;
    public $forms_obj;

    public function menu(){
        add_submenu_page(
            'users.php',
            'Medical Forms',
            'Medical Forms',
            'manage_options',
            'medical-forms-new',
            [$this, 'body']
        );
        add_menu_page('Medical Forms', 'Medical Forms', 'prescribe_capability', 'medical-forms-new', [$this, 'body']);

        $this->forms_obj = new Medical_Form_List_New();
    }

    public function body(){
        ?>
        <div class="wrap">
            <h2>Medical Forms</h2>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <?php $this->forms_obj->views(); ?>
                        
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

    public static function get_instance(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

class Medical_Form_Single_New{
    static $instance;
    public $forms_obj;

    public function menu(){
        add_submenu_page(
            'users.php',
            'Medical Forms',
            'Medical Forms',
            'manage_options',
            'medical-forms-new',
            [$this, 'body']
        );

        $this->forms_obj = new Medical_Form_List_New();
    }

    public function body(){
        $user_id = $_GET['user_id'];

        if (!$user = get_user_by('id', $user_id)) {
            echo "Invalid User";
            return false;
        }

        if (!$form = get_medical_form_data($user_id)) {
            echo "User does not have a medical form";
            return false;
        }

        function object_to_array($data)
        {
            if (is_array($data) || is_object($data))
            {
                $result = array();
                foreach ($data as $key => $value)
                {
                    $result[$key] = object_to_array($value);
                }
                return $result;
            }
            return $data;
        }

        $form_data =  object_to_array($form);

        $old_form = get_user_meta($user_id, 'medical-form', true);

        // print_r( $form_data );

        // echo 'OLD FORM \n';

        // print_r( $old_form );

        $user_data = get_userdata($user_id);

        $recommended_product = get_user_meta($user_id, 'suggested_product', true);
        if(isset($recommended_product) && !empty($recommended_product)){
            $recommended_product = get_user_meta($user_id, 'suggested_product', true);
        }else{
            if (get_user_meta($user_id, 'approved', true)) {
                $recommended_product = 'Sildafil';
            }else{
                $recommended_product = 'User is currently Flagged';
            }
        }

        $treatment_change_requests = get_user_meta( $user_id, 'treatment_change_requests', true );
        ?>
        <div class="wrap woocommerce-account">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="product-information">
                            <div class="container">
                                <ul class="nav nav-tabs mobile-nav" id="myTab" role="tablist">
                                    <li class="nav-item active">
                                        <a class="nav-link" id="medical-form-tab" data-toggle="tab" href="#medical-form" role="tab" aria-controls="medical-form"
                                            aria-selected="true" aria-expanded="true"><?php _e( 'Medical Form', 'woocommerce' ); ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="treatment-change-request-tab" data-toggle="tab" href="#treatment-change-request" role="tab" aria-controls="treatment-change-request"
                                            aria-selected="true" aria-expanded="false"><?php _e( 'Treatment Change Requests', 'woocommerce' ); ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="support-queries-tab" data-toggle="tab" href="#support-queries" role="tab" aria-controls="support-queries"
                                            aria-selected="true" aria-expanded="false" data-id="<?php echo $user_id ?>"><?php _e( 'Support Queries', 'woocommerce' ); ?></a>
                                    </li>
                                </ul>
                                <div class="suggested-product">
                                    <?php 
                                        // Get 10 most recent product IDs in date descending order.
                                        $query = new WC_Product_Query( array(
                                            'limit' => 10,
                                            'orderby' => 'date',
                                            'order' => 'DESC',
                                            'return' => 'ids',
                                        ) );
                                        $products_all = $query->get_products();

                                        $suggested_product = get_user_meta( $user_id, 'suggested_product', true );

                                        if ( ! empty( $products_all ) ) :

                                            if ( isset( $_POST['action'] ) && 'hc_admin_update_suggested_prod' === $_POST['action']  ) :

                                                // var_dump( $_POST['new_medical_questionaire_checked'] );

                                                if ( isset( $_POST['new_medical_questionaire_checked'] )  && 'true' === $_POST['new_medical_questionaire_checked'] ) :

                                                    update_user_meta( $user_id, 'new_medical_questionaire_checked', true );
                                                else :

                                                    update_user_meta( $user_id, 'new_medical_questionaire_checked', false );

                                                endif;

                                            endif;


                                    ?>
                                        <form method="POST" action="<?php echo admin_url( 'users.php?page=medical-forms-new&user_id=' . $user_id ); ?>" id="suggest-products-form">
                                            <div class="chk-btn-wrap-users">
                                                <input type="checkbox" <?php checked( get_user_meta( $user_id, 'new_medical_questionaire_checked', true ) , true ); ?> name="new_medical_questionaire_checked" 
                                                value="true" id="new-medical-questionaire-checked">
                                                <label for="new-medical-questionaire-checked">New Medical Questionaire Checked</label>
                                            </div>
                                            <label for="suggested-product"><?php _e( 'Suggested Product', 'woocommerce' ); ?></label>
                                            <select name="suggested_product_usr" id="suggested-product">
                                                <option value=""><?php _e( 'Select Product', 'woocommerce' ); ?></option>
                                                <?php foreach( $products_all as $ky => $product ) : ?>
                                                <option <?php selected( $suggested_product, esc_url( get_permalink( $product ) ) ) ?> value="<?php echo esc_url( get_permalink( $product ) ) ?>"><?php echo esc_attr( get_the_title( $product ) ) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="action" value="hc_admin_update_suggested_prod">
                                            <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ) ?>" > </br>
                                            <button type="submit" class="btn btn-filled" value="Save Changes">Save Changes </button>
                                        </form>
                                    <?php
                                        endif;
                                    ?>
                                </div>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade in active" id="medical-form" role="tabpanel" aria-labelledby="medical-form-tab">
                                            <?php
                                            if ( isset( $_POST['medical_form_details'] ) && ! empty( $_POST['medical_form_details'] ) ) {

                                                if ( ( isset( $_POST['action'] ) && 'hc_dashboard_save_user_updates' === $_POST['action'] ) && ( isset( $_POST['update_key'] ) && ! empty( $_POST['update_key'] ) ) ) {
                                                    
                                                    $saved_medical_form_data = get_medical_form_data($user_id);
                                                    // echo '=======================';
                                                    // print_r( $saved_medical_form );
                                                    // echo '=======================';

                                                    $medical_form_details = isset( $saved_medical_form_data['medical_form_details'] ) && ! empty( $saved_medical_form_data['medical_form_details'] ) ? $saved_medical_form_data['medical_form_details'] : array();

                                                    $updated_details = isset( $_POST['medical_form_details'] ) && ! empty( $_POST['medical_form_details'] ) ? $_POST['medical_form_details'] : array();
                                                    // echo '=======================';
                                                    // print_r( $updated_details );
                                                    // echo '=======================';

                                                    if ( ! empty( $updated_details ) && ! empty( $_POST['update_key'] ) ) {

                                                        // $medical_form_details[$_POST['update_key']] = $updated_details;

                                                        foreach ( $medical_form_details[$_POST['update_key']] as $key => $value ) {
                                                            if ( isset( $updated_details[$_POST['update_key']][$key] ) ) {
																$question = isset($medical_form_details[$_POST['update_key']][$key]['question']) ? $medical_form_details[$_POST['update_key']][$key]['question'] : '';
                                                                $medical_form_details[$_POST['update_key']][$key] = $updated_details[$_POST['update_key']][$key];
                                                                $medical_form_details[$_POST['update_key']][$key]['question'] = $question;
                                                            }
                                                        }

                                                        $new_medical_array['medical_form_details'] = $medical_form_details;

                                                        // Save data to account
                                                        save_medical_form_data($user_id, $new_medical_array);

                                                        if ( 'personal_information' !== $_POST['update_key'] ) {
                                                            // Flag User.
                                                            update_user_meta( $user_id, 'approved', false );
                                                        }

                                                        ?>

                                                        <div class="woocommerce-notices-wrapper">
                                                            <div class="woocommerce-message" role="alert">
                                                                <?php _e( 'Medical form updated', 'wooocmmerce' ); ?>	
                                                            </div>
                                                        </div>
                                                        
                                                        <?php
                                                    }

                                                }

                                            }

                                            if (!$form_data = get_medical_form_data($user_id)) {
                                                
                                                echo __( "User does not have a medical form", "woocommerce" );

                                                return;

                                            } else {

                                                $medical_form_details = isset( $form_data['medical_form_details'] ) && ! empty( $form_data['medical_form_details'] ) ? $form_data['medical_form_details'] : array();

                                                $medical_form_page = hisclinic_get_page_id_by_page_name( 'medical-form' );

                                                if ( ! empty( $medical_form_details ) ) :
                                                ?>
                                                    <div id="hc-account-dashbrd-md" class="accordions">
                                                        <?php 

                                                            $personal_information = isset( $medical_form_details['personal_information'] ) ? $medical_form_details['personal_information'] : array();
                                                            $sexual_activity      = isset( $medical_form_details['sexual_activity'] ) ? $medical_form_details['sexual_activity'] : array();
                                                            $medical_history      = isset( $medical_form_details['medical_history'] ) ? $medical_form_details['medical_history'] : array();

                                                            if ( ! empty( $personal_information ) ) :

                                                                // print_r( $medical_history );
                                                        ?>
                                                                <div class="accordion">
                                                                    <form id="admin-update-p" method="POST" action="<?php echo admin_url( 'users.php?page=medical-forms-new&user_id=' . $user_id ); ?>" id="personal-details-updates">
                                                                    <div class="title"><?php _e( 'Personal Information', 'woocommerce' ); ?></div>
                                                                    <div class="box">
                                                                        <div class="animate-input">
                                                                            <label for="customer-name"><?php _e( 'Customer Name', 'woocommerce' ); ?></label>
                                                                            <h3><?php 
                                                                                echo get_full_name($user_id);
                                                                            ?></h3>
                                                                        </div>
                                                                        <?php 
                                                                            $dob_answer = $personal_information['date_of_birth']['answer'];
                                                                        ?>
                                                                            <div class="animate-input">
                                                                                <input type="text" class="mask-date"  value="<?php echo esc_attr( $dob_answer ); ?>" name="medical_form_details[personal_information][date_of_birth][answer]" id="dob">
                                                                                <label for="dob"><?php _e('Date of Birth', 'woocommerce'); ?></label>
                                                                            </div>
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
                                                                                <div class="mf-step" mf-step="4">
                                                                                    <div class="mf-step__item mf-step__hw">

                                                                                        <div class="animate-inputs">
                                                                                            <div class="mf-height">
                                                                                                <div class="animate-input">
                                                                                                    <input min="70" max="250" type="number" value="<?php echo esc_attr( $personal_information['height']['answer'] ); ?>" name="medical_form_details[personal_information][height][answer]" id="height">
                                                                                                    <label for="height"><?php _e($step_4_height_label); ?></label>
                                                                                                </div>

                                                                                                <div class="chk-btn-wrap">
                                                                                                    <input type="checkbox"  <?php echo $personal_information['height_no_info']['answer'] === $step_4_dont_know_text ? 'checked="checked"' : ''; ?> name="medical_form_details[personal_information][height_no_info][answer]" value="<?php _e($step_4_dont_know_text); ?>" id="heightchk">
                                                                                                    <label for="heightchk"><?php _e($step_4_dont_know_text); ?></label>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="mf-weight">
                                                                                                <div class="animate-input">
                                                                                                    <input min="40" max="250" type="number"  value="<?php echo esc_attr( $personal_information['weight']['answer'] ); ?>" name="medical_form_details[personal_information][weight][answer]" id="weight">
                                                                                                    <label for="weight"><?php _e($step_4_weight_label); ?></label>
                                                                                                </div>
                                                                                                <div class="chk-btn-wrap">
                                                                                                    <input type="checkbox" <?php echo $personal_information['weight_no_info']['answer'] === $step_4_weight_dont_know_text ? 'checked="checked"' : ''; ?> name="medical_form_details[personal_information][weight_no_info][answer]" value="<?php _e($step_4_weight_dont_know_text); ?>" id="weightchk">
                                                                                                    <label for="weightchk"><?php _e($step_4_weight_dont_know_text); ?></label>
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
                                                                                                        <input required="required" <?php echo $personal_information['diet']['answer'] === $step_5_option_text ? 'checked="checked"' : ''; ?> type="radio" name="medical_form_details[personal_information][diet][answer]" value="<?php _e($step_5_option_text); ?>" id="food-<?php _e($counter); ?>">
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

                                                                        <?php
                                                                        ?>
                                                                        <div class="save-btn">
                                                                            <input type="hidden" name="action" value="hc_dashboard_save_user_updates">
                                                                            <input type="hidden" name="update_key" value="personal_information">
                                                                            <button type="submit" data-form="personal-details-updates" class="hc-dashboard-save-user-updates btn btn-filled">
                                                                                <span class="text">Save Changes</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    </form>
                                                                </div>
                                                        <?php endif; 
                                                            if ( ! empty( $sexual_activity ) ) :
                                                        ?>
                                                            <div class="accordion">
                                                                <form method="POST" action="<?php echo admin_url( 'users.php?page=medical-forms-new&user_id=' . $user_id ); ?>" id="sexual-activity-updates">
                                                                <div class="title"><?php _e( 'Sexual Activity', 'woocommerce' ); ?></div>
                                                                <div class="box">
                                                                    <!--================================
                                                                    Fiftha Info Step
                                                                    ================================-->
                                                                    <div class="mf-step" mf-step="5a">
                                                                        <div class="mf-step__item mf-step__maintain">
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
                                                                                            <input required type="radio" <?php checked( $sexual_activity['uses']['answer'], $step_26_option ); ?> name="medical_form_details[sexual_activity][uses][answer]" value="<?php _e($step_26_option); ?>" id="uses-<?php _e($counter); ?>">
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
                                                                    
                                                                    <!--================================
                                                                        Sixth Info Step
                                                                    ================================-->
                                                                    <div class="mf-step" mf-step="6">
                                                                        <div class="mf-step__item mf-step__maintain">
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
                                                                                            <input required type="radio" <?php checked( $sexual_activity['erection']['answer'], $step_6_options_text ); ?> name="medical_form_details[sexual_activity][erection][answer]" value="<?php _e($step_6_options_text); ?>" id="erection-<?php _e($counter); ?>">
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
                                                                    <div class="save-btn">
                                                                        <input type="hidden" name="action" value="hc_dashboard_save_user_updates">
                                                                        <input type="hidden" name="update_key" value="sexual_activity">
                                                                        <button type="submit" data-form="sexual-activity-updates" class="hc-dashboard-save-user-updates btn btn-filled">
                                                                            <span class="text">Save Changes</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        <?php endif; 
                                                            if ( ! empty( $medical_history ) ) :

                                                                $step_8_question = get_field('step_8_question', $medical_form_page);
                                                                $step_8_question = isset($step_8_question) ? sanitize_text_field($step_8_question) : 'Do you have, or have you ever had, Heart Disease?';
                                                                $step_8_yes_text = get_field('step_8_yes_text', $medical_form_page);
                                                                $step_8_yes_text = isset($step_8_yes_text) ? sanitize_text_field($step_8_yes_text) : 'Yes';
                                                                $step_8_no_text  = get_field('step_8_no_text', $medical_form_page);
                                                                $step_8_no_text  = isset($step_8_no_text) ? sanitize_text_field($step_8_no_text) : 'No';


                                                        ?>
                                                            <div class="accordion">
                                                            <form method="POST" action="<?php echo admin_url( 'users.php?page=medical-forms-new&user_id=' . $user_id ); ?>" id="medical-history-updates">
                                                                <div class="title"><?php _e( 'Medical History', 'woocommerce' ); ?></div>
                                                                <div class="box">
                                                                    <!-- Heart disease question -->
                                                                    <div class="mf-step" mf-step="7.1">
                                                                        <div class="mf-step__item mf-step__yesno">
                                                                            <div class="text-center">
                                                                                <h2><?php _e($step_8_question); ?></h2>
                                                                            </div>

                                                                            <div class="radio-btn-wrap">
																				<div class="radio-btn">
																					<input required type="radio" <?php checked( strtolower($medical_history['heart_disease']['answer']), 'yes' ); ?> name="medical_form_details[medical_history][heart_disease][answer]" value="Yes" id="yes7.1">
																					<label for="yes7.1" goto-step="7.3"><?php _e($step_8_yes_text); ?></label>
																				</div>
																				<div class="radio-btn">
																					<input type="radio" <?php checked( in_array(strtolower($medical_history['heart_disease']['answer']), array('no', 'none')), true ); ?> name="medical_form_details[medical_history][heart_disease][answer]" value="No" id="no7.1">
																					<label for="no7.1" goto-step="8"><?php _e($step_8_no_text); ?></label>
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
											                                        <input required class="yes" <?php checked( strtolower($medical_history['medical_condition']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][medical_condition][answer]" value="Yes" id="yes9">
											                                        <label class="toggle-below-yes" for="yes9"><?php _e($step_12_yes_text); ?></label>
											                                    </div>
											                                    <div class="radio-btn">
											                                        <input required class="no" <?php checked( in_array(strtolower($medical_history['medical_condition']['answer']), array('no', 'none')), true ); ?> data-skip="10" type="radio" name="medical_form_details[medical_history][medical_condition][answer]" value="None" id="no9">
											                                        <label class="toggle-below-no" for="no9"><?php _e($step_12_no_text); ?></label>
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

                                                                            <div class="animate-inputs animate-textarea">
                                                                                <div class="animate-input">
                                                                                    <textarea class="validate-for-next" type="text" name="medical_form_details[medical_history][medical_condition_description][answer]" id="medical-conditions" placeholder="<?php _e($step_12_a_placeholder_text); ?>"><?php echo $medical_history['medical_condition_description']['answer']; ?></textarea>
                                                                                    <!-- <label for="medical-conditions"><?php _e($step_12_a_label); ?></label> -->
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                    <div class="mf-step end-step-validate" mf-step="9.b">
                                                                        <div class="mf-step__item mf-step__yesno">
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
											                                        <input required class="yes" <?php checked( strtolower($medical_history['medical_history']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][medical_history][answer]" value="Yes" id="yes9b">
											                                        <label  class="toggle-below-yes" for="yes9b"><?php _e($step_12_b_yes_text); ?></label>
											                                    </div>
											                                    <div class="radio-btn">
											                                        <input class="no" <?php checked( in_array(strtolower($medical_history['medical_history']['answer']), array('no', 'none')), true ); ?> data-skip="10" type="radio" name="medical_form_details[medical_history][medical_history][answer]" value="None" id="no9b">
											                                        <label  class="toggle-below-no" for="no9b" goto-step="9.d"><?php _e($step_12_b_no_text); ?></label>
											                                    </div>
											                                </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mf-step textarea-toggle-step" mf-step="9.c" <?php echo 'yes' === strtolower($medical_history['medical_history']['answer']) ? 'style="display:flex;"' : 'style="display:none;"'; ?>>

                                                                        <?php 

                                                                            $past_admission_question = get_field('past_admission_question', $medical_form_page);
                                                                            $past_admission_question = isset($past_admission_question) ? sanitize_text_field($past_admission_question) : 'Please provide details about your history of hospital admission or surgery:';
                                                                            $step_12_b_label = get_field('step_12_b_label', $medical_form_page);
                                                                            $step_12_b_label = isset($step_12_b_label) ? sanitize_text_field($step_12_b_label) : 'Date of hospital admission or surgery';
                                                                            $step_12_b_description = get_field('step_12_b_description', $medical_form_page);
                                                                            $step_12_b_description = isset($step_12_b_description) ? sanitize_text_field($step_12_b_description) : 'Details about your hospital admission or surgery';
                                                                        
                                                                        ?>

                                                                        <div class="mf-step__item mf-step__yesno">
                                                                            <!-- <div class="text-center">
                                                                                <h2><?php _e($past_admission_question); ?></h2>
                                                                            </div> -->

                                                                            <?php // print_r( $medical_history['medical_history_desctiption'] ); ?>

                                                                            <?php if ( ! empty( $medical_history['medical_history_desctiption']['answer'] ) ) :
                                                                                
                                                                                foreach( $medical_history['medical_history_desctiption']['answer'] as $key => $answer ) :    
                                                                            ?>

                                                                                    <div class="repeat-block">
                                                                                        <div class="animate-inputs animate-textarea">
                                                                                            <div class="animate-input">
                                                                                                <!-- <input type="date" name="date" id="dob"> -->
                                                                                                <input class="mask-date" value="<?php echo esc_attr( $medical_history['medical_history_desctiption']['answer'][$key]['date'] ); ?>" name="medical_form_details[medical_history][medical_history_desctiption][answer][<?php echo esc_attr( $key ); ?>][date]" id="date9.c" placeholder="DD/MM/YYYY" maxlength="10">
                                                                                                <!-- <label for="date9.c"><?php _e($step_12_b_label); ?></label> -->
                                                                                            </div>
                                                                                            <div class="animate-input">
                                                                                                <textarea type="text" name="medical_form_details[medical_history][medical_history_desctiption][answer][<?php echo esc_attr( $key ); ?>][description]" id="textarea9.c" placeholder="Please provide details"><?php echo esc_attr( $medical_history['medical_history_desctiption']['answer'][$key]['description'] ); ?></textarea>
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
                                                                                            <input class="mask-date" name="medical_form_details[medical_history][medical_history_desctiption][answer][a][date]" id="date9.c" placeholder="DD/MM/YYYY" maxlength="10">
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
                                                                            <div class="text-center">
                                                                                <h2><?php _e($step_12_c_question); ?></h2>
                                                                            </div>

                                                                            <div class="radio-btn-wrap">
											                                    <div class="radio-btn">
											                                        <input required <?php checked( strtolower($medical_history['allergies']['answer']), 'yes' ); ?> class="yes" type="radio" name="medical_form_details[medical_history][allergies][answer]" value="Yes" id="yes9d">
											                                        <label  class="toggle-below-yes" for="yes9d"><?php _e($step_12_c_yes_text); ?></label>
											                                    </div>
											                                    <div class="radio-btn">
											                                        <input <?php checked( in_array(strtolower($medical_history['allergies']['answer']), array('no', 'none')), true ); ?> class="no" data-skip="10" type="radio" name="medical_form_details[medical_history][allergies][answer]" value="None" id="no9d">
											                                        <label  class="toggle-below-no" for="no9d" goto-step="9.1"><?php _e($step_12_c_no_text); ?></label>
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

                                                                            <div class="animate-inputs animate-textarea">
                                                                                <div class="animate-input">
                                                                                    <textarea class="validate-for-next" type="text" name="medical_form_details[medical_history][allergies_description][answer]" id="allergies" placeholder="Please provide details"><?php echo esc_html( $medical_history['allergies_description']['answer'] ) ?></textarea>
                                                                                    <!-- <label for="allergies"><?php _e($allergies_label); ?></label> -->
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
                                                                                <div class="text-center">
                                                                                    <h2><?php _e($step_13_question); ?></h2>
                                                                                </div>

                                                                                <div class="radio-btn-wrap">
											                                        <div class="radio-btn">
											                                            <input required <?php checked( strtolower($medical_history['nitrate']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][nitrate][answer]" value="Yes" id="yes9a">
											                                            <label for="yes9a" goto-step="11.1"><?php _e($step_13_yes_text); ?></label>
											                                        </div>
											                                        <div class="radio-btn">
											                                            <input <?php checked( in_array(strtolower($medical_history['nitrate']['answer']), array('no', 'none')), true ); ?> type="radio" name="medical_form_details[medical_history][nitrate][answer]" value="None" id="no9a">
											                                            <label for="no9a"><?php _e($step_13_no_text); ?></label>
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
                                                                                <div class="text-center">
                                                                                    <h2><?php _e($herbal_supplements_question); ?></h2>
                                                                                </div>

                                                                                <div class="radio-btn-wrap">
											                                        <div class="radio-btn">
											                                            <input required <?php checked( strtolower($medical_history['herbs']['answer']), 'yes' ); ?> class="yes" type="radio" name="medical_form_details[medical_history][herbs][answer]" value="Yes" id="yes9.22">
											                                            <label class="toggle-below-yes" for="yes9.22" goto-step="9.2a"><?php _e($herbal_supplements_yes_text); ?></label>
											                                        </div>
											                                        <div class="radio-btn">
											                                            <input <?php checked( in_array(strtolower($medical_history['herbs']['answer']), array('no', 'none')), true ); ?> class="no" type="radio" name="medical_form_details[medical_history][herbs][answer]" value="None" 3 id="no9.23">
											                                            <label class="toggle-below-no" for="no9.23"><?php _e($herbal_supplements_no_text); ?></label>
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
                                                                                <!-- <div class="text-center">
                                                                                    <h2><?php _e($herbal_supplements_description); ?></h2>
                                                                                </div> -->

                                                                                <div class="animate-inputs animate-textarea">
                                                                                    <div class="animate-input">
                                                                                        <textarea class="validate-for-next" type="text" name="medical_form_details[medical_history][herbs_description][answer]" id="herbal_suppliments" placeholder="Please provide details"><?php echo esc_html( $medical_history['herbs_description']['answer'] ); ?></textarea>
                                                                                        <!-- <label for="herbal_suppliments"><?php _e($herbal_supplements_label_text); ?></label> -->
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

                                                                        <div class="text-center">
																			<h2><?php _e('Your blood pressure (BP) has been checked in the last 12 months.', 'woocommerce' ); ?></h2>
                                                                        </div>

                                                                        <div class="radio-btn-wrap">
											                                <div class="radio-btn">
											                                    <input required <?php checked( in_array(strtolower($medical_history['blood_pressure_test']['answer']), array('yes', 'yes - it\'s been checked')), true ); ?> type="radio" name="medical_form_details[medical_history][blood_pressure_test][answer]" value="Yes" id="yes9c">
																				<label class="toggle-below-yes" for="yes9c"><?php _e($step_21_yes_text); ?></label>
											                                </div>
											                                <div class="radio-btn">
											                                    <input <?php checked( in_array(strtolower($medical_history['blood_pressure_test']['answer']), array('no', 'none', 'no - i haven\'t had it checked')), true ); ?> type="radio" name="medical_form_details[medical_history][blood_pressure_test][answer]" value="No" id="no9c">
																				<label class="toggle-below-no" goto-step="11.2" for="no9c"><?php _e($step_21_no_text); ?></label>
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
																        <div class="text-center">
																            <h2><?php _e($step_22_question); ?></h2>
																        </div>
																
																        <div class="radio-btn-wrap">
																            <div class="radio-btn radio-btn--full">
																                <input <?php isset( $medical_history['blood_pressure_diagnosis'][0]['answer'] ) ? checked( $medical_history['blood_pressure_diagnosis'][0]['answer'], $step_22_hypertension_text ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="<?php _e($step_22_hypertension_text); ?>" id="step9.4">
																                <label for="step9.4"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_22_hypertension_text); ?></label>
																            </div>
																            <div class="radio-btn radio-btn--full">
																                <input <?php isset( $medical_history['blood_pressure_diagnosis'][1]['answer'] ) ? checked( $medical_history['blood_pressure_diagnosis'][1]['answer'], $step_22_hypotension_text ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="<?php _e($step_22_hypotension_text); ?>" id="step9.42">
																                <label for="step9.42"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_22_hypotension_text); ?></label>
																            </div>
																            <div class="radio-btn radio-btn--full">
																                <input <?php isset( $medical_history['blood_pressure_diagnosis']['answer'] ) ? checked( in_array(strtolower($medical_history['blood_pressure_diagnosis']['answer']), array('no', 'none', 'no, it was normal')), true ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][blood_pressure_diagnosis][answer]" value="None" id="step9.43">
																                <label for="step9.43"><img class="svg" src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/checkbox.svg" /><?php _e($step_22_button_text); ?></label>
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
                                                                        <div class="text-center">
                                                                            <h2><?php _e($step_23_question); ?></h2>
                                                                        </div>

                                                                        <div class="radio-btn-wrap">
											                                <div class="radio-btn">
											                                    <input required <?php checked( strtolower($medical_history['lightheadedness']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="Yes" id="yes10aa">
											                                    <label for="yes10aa" goto-step="11.1"><?php _e($step_23_yes_text); ?></label>
											                                </div>
											                                <div class="radio-btn">
											                                    <input <?php checked( in_array(strtolower($medical_history['lightheadedness']['answer']), array('no', 'none')), true ); ?> type="radio" name="medical_form_details[medical_history][lightheadedness][answer]" value="None" id="no10aa">
											                                    <label for="no10aa" class="no" goto-step="10a.2"><?php _e($step_23_no_text); ?></label>
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

                                                                        <div class="text-center">
                                                                            <h2><?php _e($step_14_question); ?></h2>
                                                                        </div>

                                                                        <?php if (have_rows('step_14_options', $medical_form_page)) : ?>
                                                                            <div class="radio-btn-wrap">
                                                                                <?php
                                                                                $counter = 0;
                                                                                while (have_rows('step_14_options', $medical_form_page)) : the_row();
                                                                                    $step_14_option_text = get_sub_field('step_14_option_text', $medical_form_page);

                                                                                    ?>
																					<div class="radio-btn radio-btn--full">
                                                                                        <input <?php checked( $medical_history['cardiovascular_symptoms'][$counter]['answer'], $step_14_option_text ); ?> type="checkbox" name="medical_form_details[medical_history][cardiovascular_symptoms][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_14_option_text); ?>" id="symptoms-<?php _e($counter); ?>">
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
																					<input <?php isset( $medical_history['cardiovascular_symptoms']['answer'] ) ? checked( in_array(strtolower($medical_history['cardiovascular_symptoms']['answer']), array('no', 'none')), true ) : ''; ?> type="checkbox" name="medical_form_details[medical_history][cardiovascular_symptoms][answer]" value="None" id="symptoms-<?php _e($counter + 1 ); ?>">

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

                                                                        <div class="text-center">
                                                                            <h2><?php _e($step_15_question); ?></h2>
                                                                        </div>

                                                                        <div class="radio-btn-wrap">
											                                <div class="radio-btn">
											                                    <input required <?php checked( strtolower($medical_history['heart_attack_past']['answer']), 'yes' ); ?>  type="radio" name="medical_form_details[medical_history][heart_attack_past][answer]" value="Yes" id="yes11">
											                                    <label for="yes11" goto-step="11.1"><?php _e($step_15_yes_text); ?></label>
											                                </div>
											                                <div class="radio-btn">
											                                    <input <?php checked( in_array(strtolower($medical_history['heart_attack_past']['answer']), array('no', 'none')), true ); ?>  type="radio" name="medical_form_details[medical_history][heart_attack_past][answer]" value="None" id="no11">
											                                    <label for="no11" ><?php _e($step_15_no_text); ?></label>
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

                                                                        <div class="text-center">
                                                                            <h2><?php _e($step_16_question); ?></h2>
                                                                        </div>

                                                                        <div class="radio-btn-wrap">
											                                <div class="radio-btn">
											                                    <input <?php checked( strtolower($medical_history['stroke_TIA']['answer']), 'yes' ); ?> type="radio" name="medical_form_details[medical_history][stroke_TIA][answer]" value="Yes" id="yes12">
											                                    <label for="yes12" goto-step="11.1"><?php _e($step_16_yes_text); ?></label>
											                                </div>
											                                <div class="radio-btn">
											                                    <input required <?php checked( in_array(strtolower($medical_history['stroke_TIA']['answer']), array('no', 'none')), true ); ?> type="radio" name="medical_form_details[medical_history][stroke_TIA][answer]" value="None" id="no12">
											                                    <label for="no12" ><?php _e($step_16_no_text); ?></label>
											                                </div>
											                            </div>
                                                                    </div>
                                                                </div>


                                                                <!--================================
                                                                                Thirteenth Info Step
                                                                    ================================-->
                                                                <div class="mf-step" mf-step="13">
                                                                    <div class="mf-step__item mf-step__chkbox mf-step__stop">
                                                                        <div class="text-center">

                                                                            <?php 
                                                                            
                                                                                $step_17_question = get_field('step_17_question', $medical_form_page);
                                                                                $step_17_question = isset($step_17_question) ? sanitize_text_field($step_17_question) : 'Do you have now, or have you ever had, any of the following conditions?';
                                                                                $step_17_button_text = get_field('step_17_button_text', $medical_form_page);
                                                                                $step_17_button_text = isset($step_17_button_text) ? sanitize_text_field($step_17_button_text) : 'None apply';
                                                                                $step_17_button_alt_text = get_field('step_17_button_alt_text', $medical_form_page);
                                                                                $step_17_button_alt_text = isset($step_17_button_alt_text) ? sanitize_text_field($step_17_button_alt_text) : 'Continue';
                                                                            ?>

                                                                            <h2><?php _e($step_17_question); ?></h2>
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
                                                                                        <input <?php checked( $medical_history['conditions_1'][$counter]['answer'], $step_17_options_text ); ?>  type="checkbox" name="medical_form_details[medical_history][conditions_1][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_17_options_text); ?>" id="condition1-<?php _e($counter); ?>">
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
																					<input  <?php isset( $medical_history['conditions_1']['answer'] ) ? checked( in_array(strtolower($medical_history['conditions_1']['answer']), array('no', 'none')), true ) : ''; ?>  type="checkbox" name="medical_form_details[medical_history][conditions_1][answer]" value="None" id="condition1-<?php _e($counter + 1 ); ?>">

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
                                                                        
                                                                        <div class="text-center">
                                                                            <h2><?php _e($step_18_question); ?></h2>
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
                                                                                        <input <?php checked( $medical_history['conditions_2'][$counter]['answer'], $step_18_options_text ); ?> type="checkbox" name="medical_form_details[medical_history][conditions_2][<?php echo esc_attr( $counter ); ?>][answer]" value="<?php _e($step_18_options_text); ?>" id="condition2-<?php _e($counter); ?>">
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
																					<input  <?php isset( $medical_history['conditions_2']['answer'] ) ? checked( in_array(strtolower($medical_history['conditions_2']['answer']), array('no', 'none')), true ) : ''; ?>  type="checkbox" name="medical_form_details[medical_history][conditions_2][answer]" value="None" id="condition2-<?php _e($counter + 1 ); ?>">
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

                                                                <div class="save-btn">
                                                                    <input type="hidden" name="action" value="hc_dashboard_save_user_updates">
                                                                    <input type="hidden" name="update_key" value="medical_history">
                                                                    <button type="submit" data-form="medical-history-updates" class="hc-dashboard-save-user-updates btn btn-filled">
                                                                        <span class="text">Save Changes</span>
                                                                    </button>
                                                                </div>
                                                                </div>
                                                            </form>
                                                            </div>
                                                        <?php endif; ?>

														<div class="accordion">
															<form id="admin-update-p" method="POST" action="<?php echo admin_url( 'users.php?page=medical-forms-new&user_id=' . $user_id ); ?>" id="additional-details-updates">
																<div class="title"><?php _e( 'Additional Details', 'woocommerce' ); ?></div>
																<div class="box">
																	<div class="mf-step textarea-step">
																		<div class="mf-step__item mf-step__yesno">
																			<div class="animate-inputs animate-textarea">
																				<div class="animate-input">
																					<textarea class="validate-for-next" type="text" name="medical_form_details[additional_details][answer]" id="additional_details" placeholder="Please provide additional details"><?php echo isset( $medical_form_details['additional_details']['answer'] ) ? esc_html( $medical_form_details['additional_details']['answer'] ) : ''; ?></textarea>
											                                    </div>
											                                </div>
											                            </div>
											                        </div>
											
																	<div class="save-btn">
																		<input type="hidden" name="action" value="hc_dashboard_save_user_updates">
																		<input type="hidden" name="update_key" value="additional_details">
																		<button type="submit" data-form="additional-details-updates" class="hc-dashboard-save-user-updates btn btn-filled">
																			<span class="text">Save Changes</span>
																		</button>
																	</div>
																</div>
															</form>
														</div>
                                                    </div>
                                                <?php
                                                endif;
                                            }
                                            ?>

                                        </div>
                                        <div class="tab-pane fade" id="treatment-change-request" role="tabpanel" aria-labelledby="treatment-change-request-tab">
                                            <?php 
                                                if ( ! empty( $treatment_change_requests ) && is_array( $treatment_change_requests ) ) : ?>
                                                    <div class="wrap">
                                                        <!-- <h2>Treatment change requests</h2> -->
                                                            <table  class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col"><?php _e( 'Currently Prescribed Product', 'woocommerce' ); ?></th>
                                                                        <th scope="col"><?php _e( 'Requested product change', 'woocommerce' ); ?></th>
                                                                        <th scope="col"><?php _e( 'Reason for change request', 'woocommerce' ); ?></th>
                                                                        <th scope="col"><?php _e( 'Action', 'woocommerce' ); ?></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach( $treatment_change_requests as $key => $request ) : ?>
                                                                        <tr id="<?php echo esc_attr( $key ); ?>">
                                                                                <td>
                                                                                    <?php echo esc_html( $request['current_treatment'] ); ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php echo esc_html( get_the_title( $request['treatment_change_to'] ) ); ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?php echo esc_html( $request['treatment_change_reson'] ); ?>
                                                                                </td>
                                                                                <td>
                                                                                    <form id="form-<?php echo esc_attr( $key ); ?>" class="admin-treatment-change-request">
                                                                                        <?php if( isset( $request['approved'] ) ) : ?>

                                                                                            <?php if ( $request['approved'] ) : ?>

                                                                                                <span class="approved">Approved</span>

                                                                                            <?php else: ?>

                                                                                                <span class="rejected">Rejected</span>

                                                                                            <?php endif; ?>

                                                                                        <?php else: ?>
                                                                                            <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>" >
                                                                                            <input type="hidden" name="request_key" value="<?php echo esc_attr( $key ); ?>" >
                                                                                            <input type="hidden" name="action" value="admin_handle_treatment_change">
                                                                                            <input type="hidden" name="treatment_change_to" value="<?php echo esc_attr( $request['treatment_change_to'] ); ?>" >
                                                                                            
                                                                                            <div class="radio-btn-wrap approve-reject">
                                                                                            <div class="radio-btn">
                                                                                                <input id="approve-true" type="radio" data-form="<?php echo esc_attr( $key ); ?>" class="button button-primary approve-reject" name="approve" value="true"><label for="approve-true">Approve</label>
                                                                                            </div>

                                                                                            <div class="radio-btn">
                                                                                                <input id="approve-false" type="radio" data-form="<?php echo esc_attr( $key ); ?>" class="button button-danger approve-reject" name="approve" value="false"><label for="approve-false">Reject</label>
                                                                                            </div>

                                                                                            </div>

                                                                                        <?php endif; ?>
                                                                                    </form>
                                                                                </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                    </div>
                                                <?php
                                                else :
                                                    
                                                    _e( 'No requests yet', 'woocommerce' );

                                                endif;
                                            ?>
                                        </div>
                                        <div class="tab-pane fade" id="support-queries" role="tabpanel" aria-labelledby="support-queries-tab">
                                            <!-- HELP NEW MESSAGE LAYOUT -->

                                            <div class="chat-app">

                                                <div id="chat-blocks-wrap">

                                                    <?php 
                                                        // Get Chats.
                                                        $hc_user_chats = get_user_meta( $user_id, 'hc_dr_support_messages', true );

                                                        if ( ! empty( $hc_user_chats ) && is_array( $hc_user_chats ) ) :

                                                            foreach ( $hc_user_chats as $key => $chat_block ) {
                                                                
                                                                ?>
                                                                <?php if ( $chat_block['composer'] === 'admin' ) : ?>
                                                                    <div class="right-wrap">
                                                                <?php endif; ?>
                                                                        <div class="chat-block chat-block--<?php echo $chat_block['composer'] === 'admin' ? 'right' : 'left'; ?> chat-block--<?php echo $chat_block['composer'] === 'admin' ? 'dark' : 'light'; ?>">
                                                                            <p><?php echo wp_kses_post( $chat_block['dr_support_chat'] ); ?></p>
                                                                            <img src="<?php echo 'user' === $chat_block['composer'] ? get_user_avatar_url(get_user_avatar( $user_id ) ) : get_stylesheet_directory_uri() . '/assets/img/admin-chat.png'; ?>" alt="avatar">
                                                                        </div>
                                                                <?php if ( $chat_block['composer'] === 'admin' ) : ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php
                                                            }
                                                        endif;
                                                    
                                                    ?>

                                                    <script type="text/html" id="tmpl-hc-chat-block">
                                                        <div class="right-wrap">
                                                            <div class="chat-block chat-block--right chat-block--dark">
                                                                <p>{{data.data.data.message}}</p>
                                                                <img src="{{data.data.data.avatar}}" alt="avatar">
                                                            </div>
                                                        </div>
                                                    </script>

                                                </div>

                                                <!-- <div class="right-wrap">
                                                    <div class="chat-block chat-block--right chat-block--dark">
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua?</p>
                                                        <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/user-chat.png" alt="">
                                                    </div>
                                                </div>

                                                <div class="chat-block chat-block--left chat-block--light">
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing.</p>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et.</p>
                                                        <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/user-chat.png" alt="">
                                                </div>

                                                <div class="right-wrap">
                                                    <div class="chat-block chat-block--right chat-block--dark">
                                                        <p> Hi John!</p>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua?</p>
                                                        <img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/admin-chat.png" alt="">
                                                    </div>
                                                </div> -->

                                                <div class="accordion-bottom">
                                                    <form id="hc-send-chat-message-backend">
                                                        <div class="label-textarea">
                                                            <textarea required name="dr_support_chat" id="msg-reply" placeholder="Type your message"></textarea>
                                                            <input type="hidden" name="action" value="hc_dr_support_send_mesage">
                                                            <input type="hidden" name="composer" value="admin">
                                                            <input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>">
                                                        </div>
                                                        <button id="hc-send-chat-submit" class="btn btn-filled" type="submit"><?php _e( 'Send Message', 'woocommerce' ); ?></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php 
    }

    public static function get_instance(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

function medical_forms_admin_page_new(){
    if (empty($_GET['user_id'])) {
        $medical_form_new = Medical_Form_Index_New::get_instance();
    } else {
        $medical_form_new = Medical_Form_Single_New::get_instance();
    }

    $medical_form_new->menu();
}
add_action('admin_menu', 'medical_forms_admin_page_new');
