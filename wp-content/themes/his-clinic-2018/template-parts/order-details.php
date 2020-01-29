<?php 
/**
 * Order Details Template block
 */
$product_id = $_GET['prod_id'];
$_pf = new WC_Product_Factory();

$product    = $_pf->get_product( $product_id );
$attributes = $product->get_variation_attributes();
?>
<div id="mf-app">
    <div class="container">
        <div id="prod-order-details">
            <div class="mf-step">
                <div class="mf-progress">
                    <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="mf-stop" >< <?php _e( 'Back', 'his-clinic' ); ?></a>
                    <div class="mf-progress__bar">
                        <div class="mf-progress__fill" style="width:25%;"></div>
                    </div>
                </div>
                
                <?php if (is_product_allowed($product)): ?>

                    <div class="mf-step__item">
                        <div class="text-center">
                            <h2><?php _e('Order Details', 'his-clinic');?></h2> 
                        </div>
                        <div class="row">
                            <div class="col-md-6 prod-order-details-left ">
                                <p>
                                    <strong><?php 
                                        foreach ( $attributes as $attribute_name => $options ) : 

                                            $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );

                                        endforeach;

                                        echo  __( 'Your doctor recommended treatment is for ', 'woocommerce' ) . get_the_title( $product_id ) . __( ', in a tablet pack size of ', 'woocommerce' ) . $selected . __( '.', 'woocommerce' );
                                    ?></strong>
                                </p>
                                <div class="readmore-xs">
                                    <span class="show-content more"><?php _e( 'Read More', 'woocommerce' ); ?></span>
                                    <div class="readmore-xs-content">
                                    <p>Your recommendation is based on:</p>
                                    <ul>
                                        <li>Your sexual activity</li>
                                        <li>The symptoms of erectile dysfunction that you experience</li>
                                        <li>Your treatment history</li>
                                        <li>Your medical history</li>
                                    </ul>
                                        <?php echo do_shortcode( '[acf field="order_details_text" post_id="'. $product_id .'"]' ); ?>
                                        <span class="show-content less"><?php _e( 'Read Less', 'woocommerce' ); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 prod-order-details-right">
                                <div style="display:none;" id="hisclinic-order-details">
                                    <?php echo do_shortcode( '[add_to_cart_form id="' . $product_id . '"]' ) ?>
                                </div>
                                <table class="product-order-detail-table">
                                    <thead>
                                        <tr>
                                            <th><?php _e( 'Product', 'woocommerce' ); ?></th>
                                            <th><?php echo get_the_title( $product_id ); ?></th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <h4><?php _e( 'NUMBER OF TABLETS PER PACK', 'woocommerce' ); ?></h4>
                                            <span class="description"><?php _e( '(This can be changed in your account if signing up to a subscription service)', 'woocommerce' ); ?></span>
                                        </td>
                                        <td>
                                            <div class="options">
                                                <?php 
                                                foreach ( $attributes as $attribute_name => $options ) : 
                                                    $terms = wc_get_product_terms($product->get_id(), $attribute_name, [
                                                        'fields' => 'all',
                                                        'orderby' => 'menu_order',
                                                    ]);
                                                    
                                                    $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );

                                                    $options = array_map( 'absint', $options );

                                                        asort( $options, SORT_NUMERIC );

                                                        foreach ($options as $k => $option) {
                                                            if (requires_pre_purchase($product, $option)) {
                                                                continue;
                                                            }

                                                            $checked = ($selected == $option) ? 'checked' : null;
                                                            $label = $option;


                                                            foreach ($terms as $term) {
                                                                if ($term->slug == $option) {
                                                                    $label = $term->name;
                                                                    break;
                                                                }
                                                            }

                                                            echo "
                                                                <label class='option field radio'>
                                                                    <input class='variation-sync-variation' type='radio' name='attribute_$attribute_name' value='$option' $checked>
                                                                    <span class='box'>$label</span>
                                                                </label>
                                                            ";
                                                        }
                                                endforeach; ?>
                                            </div> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <?php  
                                            $parent_product = '';
                                            $product_id                           = WCS_ATT_Core_Compatibility::get_product_id( $product );
                                            $subscription_schemes                 = WCS_ATT_Product_Schemes::get_subscription_schemes( $product );
                                            $force_subscription                   = is_a( $parent_product, 'WC_Product' ) ? WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $parent_product ) : WCS_ATT_Product_Schemes::has_forced_subscription_scheme( $product );
                                            $default_subscription_scheme_key      = is_a( $parent_product, 'WC_Product' ) ? WCS_ATT_Product_Schemes::get_default_subscription_scheme( $parent_product, 'key' ) : WCS_ATT_Product_Schemes::get_default_subscription_scheme( $product, 'key' );
                                            $posted_subscription_scheme_key       = WCS_ATT_Product_Schemes::get_posted_subscription_scheme( $product_id );
                                            $options                              = array();

                                            // Filter default key.
                                            $default_subscription_scheme_key = apply_filters( 'wcsatt_get_default_subscription_scheme_id', $default_subscription_scheme_key, $subscription_schemes, false === $force_subscription, $product ); // Why 'false === $force_subscription'? The answer is back-compat.

                                            // Option selected by default.
                                            if ( null !== $posted_subscription_scheme_key ) {
                                                $default_subscription_scheme_key = $posted_subscription_scheme_key;
                                            }

                                            $default_subscription_scheme_option_value = false === $default_subscription_scheme_key ? '0' : $default_subscription_scheme_key;

                                            // Non-recurring (one-time) option.
                                            if ( false === $force_subscription ) {

                                                $none_string = _x( 'One-time purchase', 'product subscription selection - negative response', 'woocommerce-subscribe-all-the-things' );

                                                $options[] = array(
                                                    'class'       => 'one-time-option',
                                                    'description' => apply_filters( 'wcsatt_single_product_one_time_option_description', $none_string, $product ),
                                                    'value'       => '0',
                                                    'selected'    => '0' === $default_subscription_scheme_option_value,
                                                    'data'        => apply_filters( 'wcsatt_single_product_one_time_option_data', array(), $product )
                                                );
                                            }

                                            // Subscription options.
                                            foreach ( $subscription_schemes as $subscription_scheme ) {

                                                $sub_price_html_args = array(
                                                    'subscription_price' => true,
                                                    'product_price'      => true
                                                );

                                                $price_class = 'price';

                                                if ( false === $subscription_scheme->has_price_filter() ) {

                                                    $price_class = 'no-price';

                                                    if ( $subscription_scheme->is_synced() ) {
                                                        $sub_price_html_args[ 'product_price' ] = false;
                                                    } else {
                                                        $sub_price_html_args[ 'subscription_price' ] = false;
                                                    }
                                                }

                                                $sub_price_html = WCS_ATT_Product_Prices::get_price_html( $product, $subscription_scheme->get_key(), $sub_price_html_args );
                                                $sub_price_html = false === $sub_price_html_args[ 'subscription_price' ] ? '<span class="subscription-details">' . $sub_price_html . '</span>' : $sub_price_html;
                                                $sub_price_html = '<span class="' . $price_class . ' subscription-price">' . $sub_price_html . '</span>';

                                                $option_data = array(
                                                    'subscription_scheme'   => array_merge( $subscription_scheme->get_data(), array( 'is_prorated' => WCS_ATT_Sync::is_first_payment_prorated( $product, $subscription_scheme->get_key() ) ) ),
                                                    'overrides_price'       => $subscription_scheme->has_price_filter(),
                                                    'discount_from_regular' => apply_filters( 'wcsatt_discount_from_regular', false )
                                                );

                                                $description = false === $force_subscription ? sprintf( _x( '%s', 'product subscription selection - positive response', 'woocommerce-subscribe-all-the-things' ), $sub_price_html ) : $sub_price_html;

                                                $options[] = array(
                                                    'class'       => 'subscription-option',
                                                    'description' => apply_filters( 'wcsatt_single_product_subscription_option_description', $description, $sub_price_html, $subscription_scheme->has_price_filter(), false === $force_subscription, $product, $subscription_scheme ),
                                                    'value'       => $subscription_scheme->get_key(),
                                                    'selected'    => $default_subscription_scheme_option_value === $subscription_scheme->get_key(),
                                                    'data'        => apply_filters( 'wcsatt_single_product_subscription_option_data', $option_data, $subscription_scheme, $product )
                                                );
                                            } 
                                        ?>
                                            <h4><?php _e( 'SUBSCRIPTION SERVICE', 'woocommerce' ); ?></h4>
                                            <?php  foreach( $options as $key => $option ) : 
                                                
                                                // print_r( $option );

                                                if ( '0' !== $option['value'] ) : 
                                            ?>
                                                    <span class="description"><?php echo __( '(Save ', 'woocommerce' ) . $option['data']['subscription_scheme']['discount'] . __( ' % on your next order)', 'woocommerce' ); ?><br>
                                                    <?php _e( 'A new order will be reviewed and new parcel sent every 28 days for all orders.', 'woocommerce' ); ?>
                                                    </span>
                                            <?php 
                                                endif;
                                        endforeach; ?> 
                                        </td>
                                        <td class="subscription-options">
                                            <div class="options">
                                            <?php
                                                foreach( $options as $key => $option ) {

                                                    // print_r( $option );
                                                    ?>
                                                        <label <?php echo 'style="display:none";'; ?> for="<?php echo esc_attr( $option['class'] ); ?>-<?php echo esc_attr( $option['value'] ); ?>">
                                                            <input class="subscription-options" data-name="convert_to_sub_<?php echo absint( $product_id ); ?>" <?php echo $option['class'] === 'one-time-option' ? 'checked' : ''; ?> name="var_subscriptions_options" id="<?php echo esc_attr( $option['class'] ); ?>-<?php echo esc_attr( $option['value'] ); ?>" type="radio" value="<?php echo esc_attr( $option['value'] ); ?>">
                                                            <span class="box"><?php echo $option['value'] === '1_month' ? __( 'Monthly', 'woocommerce' ) : __( 'One Time', 'woocommerce' ); ?></span>
                                                        </label>
                                                    <?php
                                                }
                                            ?>
                                            <span id="toggle-mo"><?php _e( 'Monthly', 'woocommerce' ); ?></span>
                                            </div> 
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td>
                                            <h4><?php _e( 'Do you have any severe allergies to food or medication?', 'woocommerce' ); ?></h4>
                                        </td>
                                        <td>
                                            <div class="options">
                                                <label class="option field radio" for="allergies-yes">
                                                    <input class="allergies-options" name="allergies_check" id="allergies-yes" type="radio" value="yes">
                                                    <span class="box"><?php echo __( 'Yes', 'woocommerce' ); ?></span>
                                                </label>
                                                <label class="option field radio" for="allergies-no">
                                                    <input class="allergies-options" name="allergies_check" id="allergies-no" type="radio" checked value="no">
                                                    <span class="box"><?php echo __( 'No', 'woocommerce' ); ?></span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr> -->
                                    <!-- <tr id="allergies-details">
                                        <td colspan="2">
                                            <form id="allergy-text">
                                                <div class="detail-textarea" style="display:none;">
                                                    <h4><?php _e( 'Please provide details of the allergies', 'woocommerce' ); ?></h4>
                                                    <div class="animate-input">
                                                        <textarea bind="bindedAllergiesDetails" id="allergies-details-input" name="allergies_details" cols="50" rows="10"></textarea>
                                                    </div>
                                                </div>
                                            </form>
                                        </td>
                                    </tr> -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="empty"><?php _e( 'empty', 'woocommerce' ); ?></td>
                                            <td>
                                                <span class="variation-price"><?php $product->get_price(); ?></span>
                                                <div class="btn-div">
                                                    <button id="hc-order-checkout" class="btn filled" value="Continue"><?php _e( 'Continue', 'woocommerce' ); ?></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                
                    <h2><?php echo get_field('message_for_not_allowed_product', 'option') ?></h2>
                    
                    <?php if (is_user_logged_in()): ?>
                        <a href="<?php echo home_url('my-account/request-treatment-change') ?>" class="btn filled auto">
                            <span class="text">Request Treatment Change</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo home_url('medical-form') ?>" class="btn filled check-eligibility">
                            <span class="text">Check Eligibility</span>
                        </a>
                    <?php endif ?>
                    
                <?php endif ?>
            </div>

        </div>
    </div>
</div>
