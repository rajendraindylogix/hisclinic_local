/**
 * RightPress Product Settings Control
 */

(function () {

    /**
     * Register plugin
     */
    jQuery.fn.rightpress_product_settings_control = function(params) {

        var selector_checkbox_simple    = 'input#' + params.key;
        var selector_checkbox_variable  = 'input.' + params.key + '_variable';

        var selector_settings_simple    = '.show_if' + params.key + '_simple';
        var selector_settings_variable  = '.show_if' + params.key + '_variable';

        var unprefixed_key      = params.key.slice(1);
        var class_flag_variable = unprefixed_key + '_variable_flag';

        /**
         * Toggle settings fields for simple product
         */
        function toggle_simple_product_fields() {

            if (jQuery('select#product-type').val() === 'simple' && jQuery(selector_checkbox_simple).is(':checked')) {

                jQuery(selector_settings_simple).find('input,select').prop('disabled', false);
                jQuery(selector_settings_simple).show();
            }
            else {

                jQuery(selector_settings_simple).hide();
                jQuery(selector_settings_simple).find('input,select').prop('disabled', true);
            }
        }

        // On product type change
        jQuery('body').bind('woocommerce-product-type-change',function() {
            toggle_simple_product_fields();
        });

        // On checkbox state change
        jQuery(selector_checkbox_simple).change(function() {
            toggle_simple_product_fields();
        });

        // On page load
        toggle_simple_product_fields();

        /**
         * Toggle settings fields for variable product
         */
        function toggle_variable_product_fields() {

            if (jQuery('select#product-type').val() === 'variable') {

                jQuery(selector_checkbox_variable).each(function() {

                    // Reference settings fields
                    var variation_fields = jQuery(this).closest('div.woocommerce_variation').find(selector_settings_variable);

                    if (jQuery(this).is(':checked')) {

                        // Enable settings
                        variation_fields.find('input,select').prop('disabled', false);

                        // Display settings
                        variation_fields.show();

                        // Display icon on product variation handle
                        if (jQuery(this).closest('div.woocommerce_variation').find(('.' + class_flag_variable)).length == 0) {
                            jQuery(this).closest('div.woocommerce_variation').find('h3').first().find('select').last().after('<span class="' + class_flag_variable + '">' + params.title + '</span>');
                        }
                    }
                    else {

                        // Hide settings
                        variation_fields.hide();

                        // Disable settings
                        variation_fields.find('input,select').prop('disabled', true);

                        // Remove icon from product variation handle
                        jQuery(this).closest('div.woocommerce_variation').find(('.' + class_flag_variable)).remove();
                    }
                });
            }
        }

        // On checkbox state change
        jQuery(selector_checkbox_variable).each(function() {
            jQuery(this).change(function() {
                toggle_variable_product_fields();
            });
        });

        // On variable product options change
        jQuery(document).on('change', '#variable_product_options', function(){
            toggle_variable_product_fields();
        });

        // On click
        jQuery(document).on('click', selector_checkbox_variable, function(){
            toggle_variable_product_fields();
        });

        // On new variation
        jQuery('#variable_product_options').on('woocommerce_variations_added', function() {
            toggle_variable_product_fields();

            jQuery(selector_checkbox_variable).last().each(function() {
                jQuery(this).change(function() {
                    toggle_variable_product_fields();
                });
            });
        });

        // On page load
        toggle_variable_product_fields();
    };




}());
