<?php
/*
 Plugin Name: WooCommerce - e-Path Payment Gateway
 Plugin URI: https://woocommerce.com/products/e-path-gateway/
 Description: A WooCommerce payment gateway for e-Path. An e-Path account is required for this gateway to function.
 Version: 1.7.0
 Author: OM4
 Author URI: https://om4.com.au/plugins/
 Text Domain: wcepath
 Woo: 18679:08fbb7cbed7455fa3bf2bd94dcf61e5d
 WC requires at least: 3.0.0
 WC tested up to: 3.4.0
*/

/*
Copyright 2012-2018 OM4 (email: plugins@om4.com.au    web: https://om4.com.au/plugins/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '08fbb7cbed7455fa3bf2bd94dcf61e5d', '18679' );

load_plugin_textdomain( 'wcepath', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );

function init_epath_gateway() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	/**
	 * e-Path Payment Gateway for WooCommerce
	 *
	 */
	class WC_ePath_Payment_Gateway extends WC_Payment_Gateway {

		private $test_gateway_url = 'http://e-path.com.au/demo/demo/demo.php';

		/**
		 * @var OM4_Epath
		 */
		private $epath;

		/**
		 * The minimum WooCommerce version that this plugin supports.
		 */
		const MINIMUM_SUPPORTED_WOOCOMMERCE_VERSION = '3.0.0';

		/**
		 * Constructor. Executed automatically by WooCommerce.
		 */
		public function __construct() {

			// WooCommerce version check
			if ( version_compare( WOOCOMMERCE_VERSION, self::MINIMUM_SUPPORTED_WOOCOMMERCE_VERSION, '<' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice' ) );
				return;
			}

			$this->id         = 'epath';
			$this->has_fields = false;

			$this->method_title = __( 'e-Path', 'wcepath' );

			// Load the form fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

			// Define user set variables

			$this->title = $this->get_option('title');
			$this->gateway_url = $this->get_option('gateway_url');

			$this->logo_amex = $this->get_option('logo_amex');
            $this->logo_diners_club = $this->get_option('logo_diners_club');

            $this->supports = array( 'products', 'subscriptions', 'subscription_cancellation', 'subscription_suspension', 'subscription_reactivation' ); // add subscription support

			// Credit card logos are from http://e-path.com.au/credit_card_logos.html
			$icon = 'images/epath_vm';
			if ( 'yes' == $this->logo_amex )
				$icon .= 'a';
			if ( 'yes' == $this->logo_diners_club )
				$icon .= 'd';
			$icon .= '.gif';
			$this->icon = plugins_url($icon, __FILE__);

			// Actions

			add_action( "woocommerce_update_options_payment_gateways_{$this->id}", array( $this, 'process_admin_options' ) );

			add_action( "woocommerce_receipt_{$this->id}", array( $this, 'redirect_to_epath') );
			add_action( "woocommerce_thankyou_{$this->id}", array( $this, 'order_received_page' ) );

			if ( did_action('template_redirect') ) {
				$this->template_redirect();
			}


			if ( ! $this->is_valid_for_use() )
				$this->enabled = false;
		}

		/**
		 * Check if this gateway is enabled and available in the user's country
		 */
		private function is_valid_for_use() {
			// All countries are now supported
			return true;
		}

		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'enabled'		 => array(
					'title'	 => __( 'Enable/Disable', 'wcepath' ),
					'type'		=> 'checkbox',
					'label'	 => __( 'Enable e-Path Payments', 'wcepath' ),
					'default' => 'yes'
				),
				'title'			 => array(
					'title'			 => __( 'Title', 'wcepath' ),
					'type'				=> 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'wcepath' ),
					'default'		 => __( 'Credit Card (e-Path)', 'wcepath' )
				),
				'gateway_url' => array(
					'title'			 => __( 'Gateway URL', 'wcepath' ),
					'type'				=> 'text',
					'description' => sprintf(__( 'Your unique secure e-Path gateway URL. Use <code>%s</code> for testing purposes.', 'wcepath' ), $this->test_gateway_url ),
					'default'		 => $this->test_gateway_url,
					'css' 		=> 'min-width:350px;',
				),
				'logo_amex'		 => array(
					'title'	 => __( 'American Express Logo', 'wcepath' ),
					'type'		=> 'checkbox',
					'label'	 => __( 'Display the American Express logo during checkout', 'wcepath' ),
					'description' => __( 'If enabled, the American Express logo will be shown to the customer during checkout.', 'wcepath' ),
					'default' => 'no'
				),
				'logo_diners_club'		 => array(
						'title'	 => __( 'Diners Logo', 'wcepath' ),
						'type'		=> 'checkbox',
						'label'	 => __( 'Display the Diners Club logo during checkout', 'wcepath' ),
						'description' => __( 'If enabled, the Diners Club logo will be shown to the customer during checkout.', 'wcepath' ),
						'default' => 'no'
					),
			);

		}

		/**
		 * Admin Panel Options
		 */
		public function admin_options() {
			?>
		<h3><?php echo esc_html($this->method_title); ?></h3>
		<p><?php esc_html_e( 'The e-Path service allows you to collect credit card details and then manually process payments offline. No SSL certificate is required because the credit card handling is all performed on the e-Path website.', 'wcepath' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'During checkout, the customer\'s credit card details are stored in your e-Path account.', 'wcepath' ); ?></li>
				<li><?php esc_html_e( 'You then log into your e-Path account (via the e-Path website) to retrieve the customer\'s credit card details.', 'wcepath' ); ?></li>
				<li><?php printf( __( 'You then manually process the credit card payment, and <a href="%s" target=_"blank">update the order\'s status</a>.', 'wcepath' ), 'http://docs.woothemes.com/document/managing-orders/' ); ?></li>
			</ul>
		<p><?php _e( '<a href="https://docs.woocommerce.com/document/e-path-payment-gateway/">Documentation</a>.', 'wcepath' ); ?></p>
		<table class="form-table">
				<?php $this->generate_settings_html(); ?>
		</table><!--/.form-table-->
		<?php
		}

		/**
		 * Instruct the checkout submit process to transfer the customer to the Checkout -> Pay page
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {

			$order = new WC_Order($order_id);

			$redirect_url = $order->get_checkout_payment_url(true);

			return array(
				'result' 	=> 'success',
				'redirect'	=> $redirect_url
			);
		}

		/**
		 * The Checkout -> Pay page.
		 *
		 * Their order has been submitted, now redirect them to the e-Path website for payment.
		 **/
		public function redirect_to_epath( $order_id ) {

			$order = new WC_Order( $order_id );

			if ( !class_exists('OM4_Epath') )
				require_once( dirname(__FILE__) . '/includes/' . 'OM4_Epath.php' );

			$order_description = sprintf( __('Online Order (%1$s) from His Clinic (%2$s)', 'wcepath'), $order->get_id(), $order->get_formatted_billing_full_name());

            $items = $order->get_items();
            foreach ($items as $item_key => $item) {

                $product_id = $item['product_id'];
                if ($product_id == 5878 || $product_id == 539 || $product_id == 526 || $product_id == 472) {
                    $payment_freq = 'MONTHLY';
                    break;
                } elseif ($product_id == 38407 || $product_id == 38415 || $product_id == 38411) {
                    $payment_freq = 'ONCE only';
                    break;
                } else {
                    $payment_freq = '';
                }
            }

			$this->epath = new OM4_Epath(
				$this->gateway_url,
				$order->get_id(),
				$order->get_total(),
				$order->get_billing_email(),
				$this->get_return_url( $order ),
				$order_description,
				null,
				$payment_freq, // get payment frequency parameter
				$order->get_currency()
			);

			echo $this->epath->FormStart();
			?>
			<noscript><p><input type="submit" name="" value="<?php echo esc_html_e('Click here to pay for your order', 'wcepath') ?>" border="0"></p></noscript>
			<?php
			echo $this->epath->FormEnd();
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$('form#epath_redirect')
							.append('<p class="aligncenter"><?php echo esc_html_e('We are now transferring you to our secure payment gateway. Please wait...', 'wcepath') ?></p>')
							.submit();
				});
			</script>
			<?php
		}

		/**
		 * Attempts to detect incorrect order received URLs such as
		 *  http://domain.com/checkout/order-received/1429?key=wc_order_5462c319882a6--utm_nooverride=1
		 *
		 * Which can occur if Google Analytics Integration is enabled (or another plugin modifies the URL.
		 *
		 */
		public function template_redirect() {
			if ( is_order_received_page() && isset( $_GET['key'] ) && false !== strpos( $_SERVER['REQUEST_URI'], '--' ) ) {
				$url =  str_replace( '--', '&', $_SERVER['REQUEST_URI'] );
				wp_safe_redirect( $url );
				exit;
			}
		}


		/**
		 * The customer has submitted their Credit Card details to e-Path, and now is viewing the "Order Received" page.
		 * @param int $order_id
		 */
		public function order_received_page( $order_id ) {

			$order = new WC_Order( $order_id );

			// Mark as on-hold because the payment will need to be processed manually by the shop owner
			//test - prevent e-path from modifiying order status if it is already scripted or completed
			if ($order->get_status() !== 'scripted' && $order->get_status() !== 'completed') {
				$order->update_status( 'on-hold', __( 'Credit Card details stored in e-Path - ready for processing.', 'wcepath' ) );
			}

			// Reduce stock levels
            wc_reduce_stock_levels( $order->get_id() );

			// Empty cart
			WC()->cart->empty_cart();

		}

		/**
		 * Displays a message if the user isn't using a supported version of WooCommerce.
		 */
		public function admin_notice() {
			?>
			<div id="message" class="error">
				<p><?php printf( __( 'The WooCommerce e-Path Payment Gateway plugin is only compatible with WooCommerce version %s or later. Please update WooCommerce.', 'wcepath' ), self::MINIMUM_SUPPORTED_WOOCOMMERCE_VERSION ); ?></p>
			</div>
			<?php
		}

	}

	/**
	 * Add the gateway to WooCommerce
	 **/
	function add_epath_gateway( $methods ) {
		$methods[] = 'WC_ePath_Payment_Gateway'; return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_epath_gateway' );

	function wcepath_init() {
	    if ( is_admin() ) {
            require_once( dirname( __FILE__ ) . '/includes/wc-epath-privacy.php' );
        }
    }
	add_action( 'init', 'wcepath_init' );

}
add_action( 'plugins_loaded', 'init_epath_gateway', 0 );
