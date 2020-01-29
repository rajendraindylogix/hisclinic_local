<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Version Control
 */
$version = '1010';

global $rightpress_version;

if (!$rightpress_version || $rightpress_version < $version) {
    $rightpress_version = $version;
}

// Check if class has already been loaded
if (!class_exists('RightPress_Loader_1010')) {

    /**
     * Main Loader Class
     *
     * @class RightPress_Loader_1010
     * @package RightPress
     * @author RightPress
     */
    final class RightPress_Loader_1010
    {

        /**
         * Get version number
         *
         * @access public
         * @return string
         */
        public static function get_version()
        {

            global $rightpress_version;
            return $rightpress_version;
        }

        /**
         * Load classes used in all RightPress plugins
         *
         * @access public
         * @return void
         */
        public static function load()
        {

            // Initialize
            self::init();

            // Load helper classes
            require_once self::get_path('classes/helper/rightpress-conditions.class.php');
            require_once self::get_path('classes/helper/rightpress-forms.class.php');
            require_once self::get_path('classes/helper/rightpress-help.class.php');
            require_once self::get_path('classes/helper/rightpress-wc.class.php');

            // Load utility classes
            require_once self::get_path('classes/utility/rightpress-datetime.class.php');
            require_once self::get_path('classes/utility/rightpress-exception.class.php');

            // Load legacy class
            require_once self::get_path('classes/rightpress-legacy.class.php');
        }

        /**
         * Load class collection(s)
         *
         * @access public
         * @param string|array $names
         * @return void
         */
        public static function load_class_collection($names)
        {

            // Initialize
            self::init();

            // Iterate over collection names
            foreach ((array) $names as $name) {

                // Initialize directory iterator
                $directory_iterator = new RecursiveDirectoryIterator(self::get_path('class-collections/' . $name));

                // Initialize iterator iterator
                $iterator_iterator = new RecursiveIteratorIterator($directory_iterator);

                // Get list of all PHP files in current collection
                $file_names = new RegexIterator($iterator_iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

                // Require all files
                foreach ($file_names as $file_name) {

                    // Some sanity checks (little experience with SPL Iterators)
                    if (isset($file_name[0]) && is_string($file_name[0]) && file_exists($file_name[0])) {
                        require_once $file_name[0];
                    }
                }
            }
        }

        /**
         * Load component(s)
         *
         * @access public
         * @param string|array $names
         * @return void
         */
        public static function load_component($names)
        {

            // Initialize
            self::init();

            // Iterate over component names
            foreach ((array) $names as $name) {

                // Get main component file path
                $file_path = self::get_component_path($name, ($name . '.class.php'));

                // Component does not exist
                if (!file_exists($file_path)) {
                    RightPress_Help::doing_it_wrong('RightPress_Loader::load_component', "Component $name does not exist.", RightPress_Loader::get_version());
                    exit;
                }

                // Load main component file
                require_once $file_path;
            }
        }

        /**
         * Load jQuery plugin(s)
         *
         * @access public
         * @param string|array $names
         * @return void
         */
        public static function load_jquery_plugin($names)
        {

            global $rightpress_version;

            // Initialize
            self::init();

            // Iterate over plugin names
            foreach ((array) $names as $name) {

                // Get relative file path
                $file_path = 'jquery-plugins/' . $name . '/' . $name;

                // jQuery plugin does not exist
                if (!file_exists(plugin_dir_path(__FILE__) . $file_path . '.js')) {
                    RightPress_Help::doing_it_wrong('RightPress_Loader::load_jquery_plugin', "jQuery plugin $name does not exist.", RightPress_Loader::get_version());
                    exit;
                }

                // Enqueue script file
                wp_enqueue_script($name, RIGHTPRESS_LIBRARY_URL . '/' . $file_path . '.js', array('jquery'), $rightpress_version);

                // Enqueue optional styles file
                if (file_exists(plugin_dir_path(__FILE__) . $file_path . '.css')) {
                    wp_enqueue_style($name, RIGHTPRESS_LIBRARY_URL . '/' . $file_path . '.css', array(), $rightpress_version);
                }
            }
        }

        /**
         * Get path with trailing slash (if $suffix is omitted or includes trailing slash)
         *
         * @access public
         * @param string $suffix
         * @return string
         */
        public static function get_path($suffix = '')
        {

            // Initialize
            self::init();

            // Get path
            return dirname(__FILE__) . '/' . ltrim($suffix, '/');
        }

        /**
         * Get component path with trailing slash (if $suffix is omitted or includes trailing slash)
         *
         * @access public
         * @param string $name
         * @param string $suffix
         * @return string
         */
        public static function get_component_path($name, $suffix = '')
        {

            // Initialize
            self::init();

            // Get component path
            return self::get_path('components/' . $name . '/') . ltrim($suffix, '/');
        }

        /**
         * Get component url with trailing slash (if $suffix is omitted or includes trailing slash)
         *
         * @access public
         * @param string $name
         * @param string $suffix
         * @return string
         */
        public static function get_component_url($name, $suffix = '')
        {

            // Initialize
            self::init();

            // Get component url
            return RIGHTPRESS_LIBRARY_URL . '/components/' . $name . '/' . ltrim($suffix, '/');
        }

        /**
         * Initialize
         *
         * Note: This method must be called at the beginning of each method of this class
         *
         * @access private
         * @return void
         */
        private static function init()
        {

            // System not ready
            if (!did_action('plugins_loaded')) {
                error_log('Error: RightPress library must not be used before WordPress action plugins_loaded is executed.');
                exit;
            }

            // Define library url
            if (!defined('RIGHTPRESS_LIBRARY_URL')) {
                define('RIGHTPRESS_LIBRARY_URL', plugins_url('', __FILE__));
            }
        }
    }
}

// Check if class has already been loaded
if (!class_exists('RightPress_Loader')) {

    /**
     * Convenience Loader Class
     *
     * Warning! No changes can be made to this class since this one can be of any version, not the latest one
     * if more than one version of RightPress library is on the same installation
     *
     * @class RightPress_Loader
     * @package RightPress
     * @author RightPress
     */
    final class RightPress_Loader
    {

        /**
         * Method overload
         *
         * @access public
         * @param string $method_name
         * @param array $arguments
         * @return mixed
         */
        public static function __callStatic($method_name, $arguments)
        {

            global $rightpress_version;

            // Call method of main class
            return call_user_func_array(array(('RightPress_Loader_' . $rightpress_version), $method_name), $arguments);
        }
    }
}
