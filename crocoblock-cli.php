<?php
/*
 * Plugin Name: Crocoblock CLI
 * Plugin URI:
 * Description:
 * Version: 1.0
 * Author: Floris Boers
 * Author URI: https://onetap.online
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


if ( defined('WP_CLI') && WP_CLI ) {
	require('command.php');
}