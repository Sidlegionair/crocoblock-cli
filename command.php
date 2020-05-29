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
        define('WPINC', 'wp-includes');

        $this->processUpdate('Jet_Woo_Builder_DB_Upgrader', 'jet-woo-builder');
        $this->processUpdate('Jet_Elements_DB_Upgrader', 'jet-elements');


    }


    public function processUpdate($className, $plugin_name) {
        if (file_exists(WP_PLUGIN_DIR . "/{$plugin_name}/includes/class-{$plugin_name}-db-upgrader.php")) {
            require_once(WP_PLUGIN_DIR . "/{$plugin_name}/includes/class-{$plugin_name}-db-upgrader.php");
            if (class_exists($className)) {
                WP_CLI::log("Starting {$className}");
                try {
                    $most_recent_callback = $this->getMostRecentCallback($className);
                    WP_CLI::log('Executing '.$most_recent_callback);
                    $instance = call_user_func(array($className, 'get_instance'));
                    $instance->$most_recent_callback;
                    WP_CLI::log("Finished {$className}");
                } catch (Exception $exception) {
                    WP_CLI::error_multi_line($exception->getMessage());
                    WP_CLI::error("Something went wrong with {$className}");
                }
            }
        }

    }

    public function getMostRecentCallback($className) {
        $methods = get_class_methods($className);
        $has_do_update = array_search('do_update', $methods);
        if($has_do_update !== false) {
            return $has_do_update;
        } else {
            $method_versions = preg_replace('/[^0-9]/', '', get_class_methods($className));
            foreach($method_versions as $key => $version) {
                if (strlen($version) > 3) {
                    $method_versions[$key] = mb_strimwidth($version, 0, 3, '');
                }
            }
            $highest_version_key = array_keys($method_versions, max($method_versions));
        }
        return $methods[$highest_version_key[0]];


    }
}

WP_CLI::add_command( 'crocoblock', 'Crocoblock_CLI' );
