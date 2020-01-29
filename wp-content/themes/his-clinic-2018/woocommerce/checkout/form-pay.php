<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals();
?>

<div class="woocommerce-checkout" id="prod-order-details">
    <div class="mf-step">
        <div class="container">
            <div class="mf-progress">
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:100%;"></div>
                </div>
            </div>
            <div class="mf-step__item">
                <div class="text-center">
                    <h2><?php _e('Place Your Order', 'his-clinic');?></h2> 
                </div>
                <div class="row"> 
                    <div class="col-md-6">
                        <?php include('pay-order-description.php') ?>
                    </div>
                    <div class="col-md-6">
                        <form id="order_review" method="post">
                            
                            <div class="order-description-right">
                                <table class="shop_table woocommerce-checkout-review-order-table">
                                    <?php if ( count( $order->get_items() ) > 0 ) : ?>
                                        <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                                            <?php
                                                if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
                                                    continue;
                                                }

                                                $product = $item->get_product();
                                                $pack_size = $product->get_attribute('pa_pack-size');
                                                
                                                if ($product->get_meta('_satt_data') && !empty($product->get_meta('_satt_data')['active_subscription_scheme_key'])) {
                                                    $is_subscription = true;    
                                                } else {
                                                    $is_subscription = false;
                                                }
                                            ?>

                                            <thead>
                                                <tr>
                                                    <th class="product-name">Product</th>
                                                    <th>
                                                        <?php echo esc_html( $product->get_title() ) ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            
                                            <tbody>
                                                <tr class="tr-xs visible-xs first">
                                                    <td>
                                                        <div class="tescription"><?php _e( 'Product', 'woocommerce' ); ?></div>
                                                        <div class="content"><?php echo esc_html( $product->get_title() ) ?></div>
                                                    </td>
                                                    <td>
                                                        <div class="tescription"><?php _e( 'Tablets per pack', 'woocommerce' ); ?></div>
                                                        <div class="content"><?php echo esc_attr($pack_size); ?></div>
                                                    </td>
                                                </tr>
                                                <tr class="tr-xs visible-xs">
                                                    <td>
                                                        <div class="tescription"><?php _e( 'Subscription', 'woocommerce' ); ?></div>
                                                        <div class="content"><?php echo ($is_subscription) ? 'Yes' : 'No'; ?></div>
                                                    </td>
                                                    <td>
                                                        <?php if ($order->get_shipping_method()) : ?>

                                                            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

                                                                <div class="tescription"><?php _e( 'Shipping', 'woocommerce' ); ?></div>
                                                                <div class="content"><?php echo $order->get_shipping_method() ?></div>

                                                            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <?php if ($pack_size):  ?>
                                                    <tr class="">
                                                        <td class="pack sixe">
                                                            <?php _e( 'NUMBER OF TABLETS PER PACK', 'woocommerce' ); ?>
                                                        </td>
                                                        <td class="pwdwqotal">
                                                            <?php echo esc_attr($pack_size); ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                endif; ?>

                                                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item_checkout_rvw', $cart_item, $cart_item_key ) ); ?>">
                                                    <td class="pack sixe">
                                                        <?php _e( 'SUBSCRIPTION', 'woocommerce' ); ?>
                                                    </td>
                                                    <td class="pwdwqotal">
                                                        <?php echo ($is_subscription) ? 'Yes' : 'No'; ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <tfoot>
                                        <?php if ( $totals ) : ?>
                                            <?php foreach ( $totals as $total ) : ?>
                                                <tr>
                                                    <th scope="row"><?php echo $total['label']; ?></th><?php // @codingStandardsIgnoreLine ?>
                                                    <td class="product-total"><?php echo $total['value']; ?></td><?php // @codingStandardsIgnoreLine ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tfoot>
                                </table>

                                <div id="payment">
                                    <?php if ( $order->needs_payment() ) : ?>
                                        <ul class="wc_payment_methods payment_methods methods">
                                            <?php
                                            if ( ! empty( $available_gateways ) ) {
                                                foreach ( $available_gateways as $gateway ) {
                                                    wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                                                }
                                            } else {
                                                echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', __( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
                                            }
                                            ?>
                                        </ul>
                                    <?php endif; ?>

                                    <div class="form-row">
                                        <input type="hidden" name="woocommerce_pay" value="1" />

                                        <?php wc_get_template( 'checkout/terms.php' ); ?>
                                    </div>
                                    
                                    <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

                                    <div class="text-right text-center-xs">            
                                        <button class="btn filled place-order-button">Place Order</button>
                                    </div>

                                    <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

                                    <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
                                </div>
                            </div>    

                        </form>                            
                    </div>
                </div>

                <?php include('accepted-cards.php') ?>
            </div>
        </div>
    </div>
</div>