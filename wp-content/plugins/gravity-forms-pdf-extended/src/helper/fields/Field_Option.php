<?php

namespace GFPDF\Helper\Fields;

use GFPDF\Helper\Helper_Abstract_Field_Products;

use GFFormsModel;
use GFCommon;

/**
 * Gravity Forms Options field
 *
 * @package     Gravity PDF
 * @copyright   Copyright (c) 2019, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.3
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
    This file is part of Gravity PDF.

    Gravity PDF – Copyright (c) 2019, Blue Liquid Designs

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * @since 4.3
 */
class Field_Option extends Helper_Abstract_Field_Products {

	/**
	 * Return the HTML form data
	 *
	 * @return array
	 *
	 * @since 4.3
	 */
	public function form_data() {
		$field = $this->field;

		/**
		 * Gravity Forms doesn't currently store the option field ID with the standard product information.
		 * However, it does allow multiple fields to be an option for a single product.
		 * This becomes problematic when you have multiple option fields that contains the same name and are
		 * trying to determine which field it was selected from.
		 *
		 * To get around this limitation we'll process the entry option fields and make this data available.
		 */

		/* Get the current option value for this field */
		$option_value = GFFormsModel::get_lead_field_value( $this->entry, $field );

		/* Ensure this variable is an array */
		$option_value = ( ! is_array( $option_value ) ) ? [ $option_value ] : $option_value;

		/* Reset the array keys and remove any empty values */
		$option_value = array_values( $option_value );
		$option_value = array_filter( $option_value );

		/* Get the field name ( */
		$name = array_map( function( $value ) use ( $field ) {
			$option_info = GFCommon::get_option_info( $value, $field, false );

			return esc_html( $option_info['name'] );
		}, $option_value );

		/* Get the field value (the price) */
		$price = array_map( function( $value ) use ( $field ) {
			$option_info = GFCommon::get_option_info( $value, $field, false );

			return esc_html( $option_info['price'] );
		}, $option_value );

		/**
		 * Valid option fields can only be radio, checkbox and select boxes
		 * To ensure backwards compatibility we'll remove the array if not a checkbox value
		 */
		if ( $field->inputType !== 'checkbox' ) {
			$name  = array_shift( $name );
			$price = array_shift( $price );
		}

		return $this->set_form_data( $name, $price );
	}

	/**
	 * Display the HTML version of this field
	 *
	 * @param string $value
	 * @param bool   $label
	 *
	 * @return string
	 *
	 * @since 4.3
	 */
	public function html( $value = '', $label = true ) {
		$value = $this->value();
		$html  = '';

		if ( isset( $value['options'] ) ) {
			$html .= $this->get_option_html( $value['options'] );
		}

		return parent::html( $html );
	}

	/**
	 * Get a HTML list of the product's selected options
	 *
	 * @param  array  $options A list of the selected products
	 * @param  string $html    Pass in an existing HTML, or default to blank
	 *
	 * @return string         The finalised HTML
	 *
	 * @since 4.3
	 */
	public function get_option_html( $options, $html = '' ) {
		if ( is_array( $options ) ) {
			$html .= '<ul class="product_options">';

			foreach ( $options as $option ) {
				$html .= '<li>' . esc_html( $option['option_name'] . ' - ' . $option['price_formatted'] ) . '</li>';
			}

			$html .= '</ul>';
		}

		return $html;
	}

	/**
	 * Get the standard GF value of this field
	 *
	 * @return array
	 *
	 * @since    4.3
	 */
	public function value() {
		if ( $this->has_cache() ) {
			return $this->cache();
		}

		$data = $this->products->value();

		if ( isset( $data['products'][ $this->field->productField ]['options'] ) ) {
			$this->cache( [
				'options' => array_filter( $data['products'][ $this->field->productField ]['options'], function( $option ) {
					return ! isset( $option['id'] ) || $option['id'] === $this->field->id;
				}),
			] );
		} else {
			$this->cache( [] );
		}

		return $this->cache();
	}
}