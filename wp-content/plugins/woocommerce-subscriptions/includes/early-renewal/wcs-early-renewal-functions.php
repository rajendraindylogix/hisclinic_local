<?php
/**
 * WooCommerce Subscriptions Early Renewal functions.
 *
 * @author   Prospress
 * @category Core
 * @package  WooCommerce Subscriptions/Functions
 * @since    2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Checks the cart to see if it contains an early subscription renewal.
 *
 * @return bool|array The cart item containing the early renewal, else false.
 * @since  2.3.0
 */
function wcs_cart_contains_early_renewal() {

	$cart_item = wcs_cart_contains_renewal();

	if ( $cart_item && ! empty( $cart_item['subscription_renewal']['subscription_renewal_early'] ) ) {
		return $cart_item;
	}

	return false;
}

/**
 * Checks if a user can renew an active subscription early.
 *
 * @param int|WC_Subscription $subscription Post ID of a 'shop_subscription' post, or instance of a WC_Subscription object.
 * @param int $user_id The ID of a user.
 * @since 2.3.0
 * @return bool Whether the user can renew a subscription early.
 */
function wcs_can_user_renew_early( $subscription, $user_id = 0 ) {

	if ( ! is_object( $subscription ) ) {
		$subscription = wcs_get_subscription( $subscription );
	}

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	if ( ! $subscription ) {
		$can_renew_early = false;
	} elseif ( ! $subscription->has_status( array( 'active' ) ) ) {
		$can_renew_early = false;
	} elseif ( 0 === $subscription->get_total() ) {
		$can_renew_early = false;
	} elseif ( $subscription->get_time( 'trial_end' ) > gmdate( 'U' ) ) {
		$can_renew_early = false;
	} elseif ( ! $subscription->get_time( 'next_payment' ) ) {
		$can_renew_early = false;
	} elseif ( WC_Subscriptions_Synchroniser::subscription_contains_synced_product( $subscription ) ) {
		$can_renew_early = false;
	} elseif ( ! $subscription->payment_method_supports( 'subscription_date_changes' ) ) {
		$can_renew_early = false;
	} else {
		// Make sure all line items still exist.
		$all_line_items_exist = true;

		foreach ( $subscription->get_items() as $line_item ) {
			$product = wc_get_product( wcs_get_canonical_product_id( $line_item ) );

			if ( false === $product ) {
				$all_line_items_exist = false;
				break;
			}
		}

		$can_renew_early = $all_line_items_exist;
	}

	/**
	 * Allow third-parties to filter whether the customer can renew a subscription early.
	 *
	 * @since 2.3.0
	 * @param bool            $can_renew_early Whether early renewal is permitted.
	 * @param WC_Subscription $subscription The subscription being renewed early.
	 * @param int             $user_id The user's ID.
	 */
	return apply_filters( 'woocommerce_subscriptions_can_user_renew_early', $can_renew_early, $subscription, $user_id );
}

/**
 * Check if a given order is a subscription renewal order.
 *
 * @param WC_Order|int $order The WC_Order object or ID of a WC_Order order.
 * @since 2.3.0
 * @return bool True if the order contains an early renewal, otherwise false.
 */
function wcs_order_contains_early_renewal( $order ) {

	if ( ! is_object( $order ) ) {
		$order = wc_get_order( $order );
	}

	$subscription_id  = absint( wcs_get_objects_property( $order, 'subscription_renewal_early' ) );
	$is_early_renewal = wcs_is_order( $order ) && $subscription_id > 0;

	/**
	 * Allow third-parties to filter whether this order contains the early renewal flag.
	 *
	 * @since 2.3.0
	 * @param bool     $is_renewal True if early renewal meta was found on the order, otherwise false.
	 * @param WC_Order $order The WC_Order object.
	 */
	return apply_filters( 'woocommerce_subscriptions_is_early_renewal_order', $is_early_renewal, $order );
}

/**
 * Returns a URL for early renewal of a subscription.
 *
 * @param  int|WC_Subscription $subscription WC_Subscription ID, or instance of a WC_Subscription object.
 * @return string The early renewal URL.
 * @since  2.3.0
 */
function wcs_get_early_renewal_url( $subscription ) {
	$subscription_id = is_a( $subscription, 'WC_Subscription' ) ? $subscription->get_id() : absint( $subscription );

	$url = add_query_arg( array(
		'subscription_renewal_early' => $subscription_id,
		'subscription_renewal'       => 'true',
		'wcs_nonce'                  => wp_create_nonce( 'wcs-renew-' . $subscription_id ),
	), get_permalink( wc_get_page_id( 'myaccount' ) ) );

	/**
	 * Allow third-parties to filter the early renewal URL.
	 *
	 * @since 2.3.0
	 * @param string $url The early renewal URL.
	 * @param int    $subscription_id The ID of the subscription to renew to.
	 */
	return apply_filters( 'woocommerce_subscriptions_get_early_renewal_url', $url, $subscription_id );
}
