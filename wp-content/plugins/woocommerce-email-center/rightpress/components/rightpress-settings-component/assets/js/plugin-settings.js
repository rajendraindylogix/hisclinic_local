/**
 * RightPress Plugin Settings Scripts
 */

jQuery(document).ready(function() {

    /**
     * Toggle fields
     */
    jQuery('.rightpress-plugin-settings-has-conditions').each(function() {

        // Reference child field
        var child_field = jQuery(this);

        // Get all conditions for this setting
        var all_conditions = rightpress_plugin_settings.conditions[jQuery(this).prop('id')];

        // Iterate over all conditions
        jQuery.each(all_conditions, function(parent_key, conditions) {

            // Set up event listeners on parent
            jQuery(('#' + parent_key)).bind('keyup change', function() {

                // Initial state
                var conditions_pass = true;

                // Iterate over all conditions (we are doing this again for a reason!)
                jQuery.each(all_conditions, function(current_parent_key, current_conditions) {

                    // Reference parent field
                    var parent_field = jQuery(('#' + current_parent_key));

                    // Iterate over child-parent conditions
                    jQuery.each(current_conditions, function(condition_method, condition_options) {

                        // Is checked
                        if (condition_method === 'is_checked') {
                            if (!parent_field.is(':checked')) {
                                conditions_pass = false;
                                return false;
                            }
                        }
                    });

                    // Break after at least one failed condition
                    if (!conditions_pass) {
                        return false;
                    }
                });

                // Toggle field
                child_field.prop('disabled', !conditions_pass).closest('tr').css('display', (conditions_pass ? 'table-row' : 'none'));

            }).change();
        });
    });

    /**
     * We are done by now, remove preloader
     */
    jQuery('#rightpress-plugin-settings-preloader').remove();

});
