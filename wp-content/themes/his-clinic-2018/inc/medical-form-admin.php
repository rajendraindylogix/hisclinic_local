<?php
/*
	NOT IN USE
*/
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

define("MF_KEY", 'medical-form');

class Medical_Form_List extends WP_List_Table
{
	public function __construct() {
		parent::__construct([
			'singular' => 'Medical Form',
			'plural'   => 'Medical Forms',
			'ajax'     => false //should this table support ajax?
		]);
	}

	public static function get_users( $per_page = 20, $page_number = 1 ) {
		global $wpdb;

        $args = [
            'role' => 'customer',
            'meta_query' => [
                [
                    'key' => MF_KEY,
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

	public function record_count() {
        $users = $this->get_users(-1);

		return count($users);
	}

	public function no_items() {
		_e( 'No forms avaliable.', 'medical-form' );
	}

	protected function display_tablenav( $which ) {
		?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">
        <?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
		?>
		<br class="clear" />
	</div>
		<?php
	}

	public function column_default( $item, $column_name ) {
        $user_id = $item->id;
		switch ( $column_name ) {
            case 'id':
                $url = get_admin_url() . 'users.php?page=medical-forms&user_id=' . $item->id;
                return "<a href='$url'>#{$item->id}</a>";
            case 'user_fullname':
                return get_full_name($user_id);
            case 'user_email':
				return $item->$column_name;
            case 'flagged':
                $form = get_user_meta($user_id, MF_KEY, true);
                $form = json_decode($form);
                $flagged = 'No';
                foreach ($form as $key => $value) {
                    if ($key !== 'symptoms-of-ed' && $value === 'Yes') {
                        $flagged = '<p style="background-color: red; color: white;">YES</p>';
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
                    return '<a href="'.get_admin_url().'edit.php?post_status=all&post_type=shop_order&_customer_user='.$user_id.'">View orders</a>';
                } else {
                    return '-';
                }

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
    }

	function get_columns() {
		$columns = [
            'id' => 'User ID',
            'user_fullname' => 'Customer name',
            'user_email' => 'Email',
            'flagged' => 'Flagged',
            'previous_orders' => 'Previous orders',
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'email' => array( 'email', true ),
			'created' => array( 'created', true ),
			'updated' => array( 'updated', true ),
		);

		return $sortable_columns;
	}

	protected function extra_tablenav( $which ) {
		?>
		<div class="alignleft actions">
		<?php
		if ( 'top' === $which && !is_singular() ) {

			do_action( 'restrict_manage_posts', $this->screen->post_type, $which );

			submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
		}
		?>
		</div>
		<?php
		do_action( 'manage_posts_extra_tablenav', $which );
	}

	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		$per_page = 20;
		$current_page = $this->get_pagenum();
		$total_items = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		]);

		$this->items = self::get_users( $per_page, $current_page );
	}
}

class Medical_Form_Index
{
	static $instance;
	public $forms_obj;

	public function menu() {
        // add_submenu_page(
        //     'users.php',
        //     'Medical Forms',
        //     'Medical Forms',
        //     'manage_options',
        //     'medical-forms',
        //     [$this, 'body']
        // );
        // add_menu_page('Medical Forms', 'Medical Forms', 'prescribe_capability', 'medical-forms', [$this, 'body']);

		$this->forms_obj = new Medical_Form_List();
	}

	public function body() {
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

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

class Medical_Form_Single
{
	static $instance;
	public $forms_obj;

	public function menu() {
        add_submenu_page(
            'users.php',
            'Medical Forms',
            'Medical Forms',
            'manage_options',
            'medical-forms',
            [$this, 'body']
        );

		$this->forms_obj = new Medical_Form_List();
	}

	public function body() {
        $user_id = $_GET['user_id'];

        if (!$user = get_user_by('id', $user_id)) {
            echo "Invalid User";
            return false;
        }

        if (!$form = get_user_meta($user_id, MF_KEY, true)) {
            echo "User does not have a medical form";
            return false;
        } else {
            $form = json_decode($form);
        }

        $user_data = get_userdata($user_id);
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

                            <?php foreach ($form as $key => $value): ?>
                                <?php
                                    if ($key !== 'symptoms-of-ed' && $value === 'Yes') {
                                        $flag = 'background-color: red; color: white;';
                                    } else {$flag = '';}
                                ?>
                                <tr>
                                    <td style="<?php echo $flag; ?>"><?php echo ucwords(str_replace('-', ' ', $key)) ?></td>
                                    <td style="<?php echo $flag; ?>"><?php echo ($value) ? $value : 'n/a' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

function medical_forms_admin_page() {
    if (empty($_GET['user_id'])) {
        $medical_form = Medical_Form_Index::get_instance();
    } else {
        $medical_form = Medical_Form_Single::get_instance();
    }

    $medical_form->menu();
}
// add_action('admin_menu', 'medical_forms_admin_page');
