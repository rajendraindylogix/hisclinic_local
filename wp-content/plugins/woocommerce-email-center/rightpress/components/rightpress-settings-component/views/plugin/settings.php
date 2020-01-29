<?php

/**
 * Settings page main settings view
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<div id="rightpress-plugin-settings">

    <input type="hidden" name="current_tab" value="<?php echo $current_tab; ?>" />

    <?php settings_fields($this->get_settings_group_key($current_tab)); ?>
    <?php do_settings_sections($this->get_settings_page_id($current_tab)); ?>

    <div></div>

    <?php submit_button(); ?>

</div>
