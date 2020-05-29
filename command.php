<?php

class Crocoblock_CLI extends WP_CLI_Command
{

    /**
     * Update crocoblock plugins
     *
     * ## EXAMPLES
     *
     *     wp crocoblock update
     *
     * @subcommand update
     */
    public function update()
    {
        // Circumvent direct file call.
        define('WPINC', 'wp-includes');

        $this->processUpdate('Jet_Woo_Builder_DB_Upgrader', 'jet-woo-builder');
        $this->processUpdate('Jet_Elements_DB_Upgrader', 'jet-elements');


    }


    private function processUpdate($className, $plugin_name) {
        if (file_exists(WP_PLUGIN_DIR . "/{$plugin_name}/includes/class-{$plugin_name}-db-upgrader.php")) {
            require_once(WP_PLUGIN_DIR . "/{$plugin_name}/includes/class-{$plugin_name}-db-upgrader.php");
            if (class_exists($className)) {
                WP_CLI::log("Starting {$className}");
                try {
                    $most_recent_callback = $this->getMostRecentCallback($className);
                    WP_CLI::log('Executing '.$most_recent_callback);
                    // Get instance for class
                    $instance = call_user_func(array($className, 'get_instance'));
                    // Execute most recent update function
                    $instance->$most_recent_callback;
                    WP_CLI::log("Finished {$className}");
                } catch (Exception $exception) {
                    WP_CLI::error_multi_line($exception->getMessage());
                    WP_CLI::error("Something went wrong with {$className}");
                }
            }
        }

    }

    private function getMostRecentCallback($className) {
        // Get all methods in class
        $methods = get_class_methods($className);
        // Strip everything but numbers
        $method_versions = preg_replace('/[^0-9]/', '', get_class_methods($className));
        // To prevent minor update versions from interfering with major ones strip it down to three numbers.
        foreach($method_versions as $key => $version) {
            if (strlen($version) > 3) {
                $method_versions[$key] = mb_strimwidth($version, 0, 3, '');
            }
        }
        // Get array key for highest number
        $highest_version_key = array_keys($method_versions, max($method_versions));
        return $methods[$highest_version_key[0]];


    }
}

WP_CLI::add_command( 'crocoblock', 'Crocoblock_CLI' );
