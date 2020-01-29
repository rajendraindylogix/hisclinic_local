<?php
/*
   $Revision: 4508 $
   $LastChangedDate: 2012-07-10 09:06:51 +1000 (Tue, 10 Jul 2012) $
   $Id: OM4_Epath.php 4508 2012-07-09 23:06:51Z james $


   Copyright 2012-2013 OM4 (email: info@om4.com.au    web: http://om4.com.au/)

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
 * OM4 e-Path - http://e-path.com.au/integration.html
 *
 * Important:
 * 1. e-Path's encryption system cannot (by default) handle return URLs that contain an ampersand characters (&).
 *    If the specified return URL contains an ampersand, the e-Path account holder must inform e-Path that their e-Path gateway needs to be modified to work with ampersands in the return URL.
 * 2. The order description must not contain a # character
 */
class OM4_Epath {

	private $test_gateway_url = 'http://e-path.com.au/demo/demo/demo.php';

	private
		$gateway_url,
		$order_number,
		$order_description,
		$order_amount,
		$currency,
		$payment_frequency,
		$optional_value,
		$customer_email,
		$return_url;

	public
		$data,
		$token;

	/**
	 * Constructor
	 * @param string $gateway_url string Your unique secure e-Path gateway URL
	 * @param string $order_number Unique order number
	 * @param float $order_amount The amount the customer is paying and authorising you to charge
	 * @param string $customer_email Customer email address
	 * @param string $return_url Return URL (the URL e-Path will automatically send the customer back to after entering their credit card details)
	 * @param string $order_description (optional) What the customer is buying or "Online Order" for brevity. Must not include a # character! Defaults to 'Online Order Number xyz'
	 * @param string $optional_value Optional field value (this is an optional parameter, it may be left out)
	 * @param string $payment_frequency (optional) The charge frequency (Once, monthly etc). Defaults to 'One Payment Only'
	 * @param string $currency (optional) The 3 letter country currency code you will be charging the credit card in, eg, AUD, USD etc. Defaults to 'AUD'
	 */
	public function __construct($gateway_url, $order_number, $order_amount, $customer_email, $return_url, $order_description = null, $optional_value = null, $payment_frequency = 'ONCE only', $currency = 'AUD') {
		$this->gateway_url = is_null($gateway_url) ? $this->test_gateway_url : $gateway_url;

		if ( $this->gateway_url == 'http://e-path.com.au/demo1/demo1/demo1.php' ) {
			// This old test URL no longer exists
			$this->gateway_url = $this->test_gateway_url;
		}

		$this->order_number = $order_number;
		$this->order_amount = '$' . number_format( (double) $order_amount, 2, '.', '' );
		$this->currency = substr( preg_replace('/[^a-z]/i', '', $currency), 0, 3); // up to 3 alphabetic characters
		$this->customer_email = $customer_email;

		if ( strpos($return_url, '&') !== false ) {
			// See note above regarding ampersand characters in the return URL
			$return_url = str_replace( '&', '--', $return_url );
		}
		$this->return_url = $return_url;

		if ( is_null($order_description) ) {
			$order_description = "Online Order Number {$order_number}";
		}
		$this->order_description = str_replace( '#', '', $order_description );

		$this->optional_value = $optional_value;
		$this->payment_frequency = $payment_frequency;
	}

	public function FormData() {
		$this->data = array(
			'ord' => $this->order_number, // A unique order number
			'des' => $this->order_description, // What the customer is buying or "Online Order" for brevity
			'amt' => $this->order_amount, // The amount the customer is paying and authorising you to charge
			'cur' => $this->currency, // The country currency code you will be charging the credit card in, eg, AUD, USD etc
			'frq' => $this->payment_frequency, // The charge frequency (Once, monthly etc)
			'ceml' => $this->customer_email, //
			'ret' => $this->return_url //  A return URL (the URL e-Path will send customer back to)
		);
		if ( !is_null($this->optional_value) ) {
			$this->data['opt'] = $this->optional_value; //
		}
		return $this->data;
	}

	public function Form() {
		$this->FormStart();
		$this->FormEnd();
	}

	public function FormStart() {
        $this->FormData();

		$form = '<form action="' . esc_url($this->gateway_url) . '" method="post" name="epath_redirect" id="epath_redirect">';

		foreach ( $this->data as $field_name => $field_value ) {
            $form .= '<input type="hidden" name="' . esc_attr($field_name) . '" value="' . esc_attr($field_value) . '" />';
		}

		return $form;
	}

	public function FormEnd() {
		return '</form>';
	}

	/**
	 * Transfer the customer to the e-Path website for payment. Uses the parameters defined in the constructor above
	 * @return void
	 */
	public function RedirectToEPathForPayment() {
		om4_form_display_and_auto_submit( $this->FormStart(), $this->FormEnd(), 'Redirecting to secure payment gateway...', 'Click Here to Make Payment Now' );
	}
}
