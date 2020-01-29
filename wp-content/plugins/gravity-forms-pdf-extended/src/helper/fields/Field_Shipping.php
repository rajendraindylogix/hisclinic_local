<?php

namespace GFPDF\Helper\Fields;

use GFPDF\Helper\Helper_Abstract_Field_Products;

/**
 * Gravity Forms Shipping Field
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
class Field_Shipping extends Helper_Abstract_Field_Products {

	/**
	 * Return the HTML form data
	 *
	 * @return array
	 *
	 * @since 4.3
	 */
	public function form_data() {
		$value    = $this->value();

		if ( isset( $value['shipping_formatted'] ) ) {
			$name = ( isset( $value['shipping_name'] ) ) ? $value['shipping_name'] . " ({$value['shipping_formatted']})" : '';
			$name = esc_html( $name );

			$price = ( isset( $value['shipping'] ) ) ? $value['shipping'] : '';
			$price = esc_html( $price );

			return $this->set_form_data( $name, $price );
		}

		return $this->set_form_data( '', '' );
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

		if ( isset( $value['shipping_formatted'] ) ) {
			$html .= $value['shipping_name'] . ' - ' . $value['shipping_formatted'];
		}

		return parent::html( $html );
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

		if ( isset( $data['products_totals']['shipping'] ) ) {
			$this->cache( [
				'shipping'           => esc_html( $data['products_totals']['shipping'] ),
				'shipping_formatted' => esc_html( $data['products_totals']['shipping_formatted'] ),
				'shipping_name'      => esc_html( $data['products_totals']['shipping_name'] ),
			] );
		} else {
			$this->cache( [] );
		}

		return $this->cache();
	}
}