<?php

/**
 * @package           Magic Login API
 * @author            David Strom and Manish Kumar
 * @copyright         2019 DragLabs
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Magic Login API
 * Plugin URI:        https://draglabs.com/wp_magic_login_api
 * Description:       Enter your email address, and send you an email with a magic link to login without a password.
 * Version:           1.1.2
 * Author:            David Strom and Manish Kumar
 * Author URI:        https://draglabs.com/
 * Text Domain:       magic-login-mail-api
 * License:           GPL v2 or later   
 * License URI: 	  http://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.4
 * Requires PHP:      7.4
 */


/*
	Copyright (c) 2021- David Strom (email : david@draglabs.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

!defined('MAGICLOGINAPI_PATH') && define('MAGICLOGINAPI_PATH', plugin_dir_path(__FILE__));

/**
 * Write an entry to a log file in the uploads directory.
 * 
 * @since x.1.6
 * 
 * @param mixed $entry String or array of the information to write to the log.
 * @param string $file Optional. The file basename for the .log file.
 * @param string $mode Optional. The type of write. See 'mode' at https://www.php.net/manual/en/function.fopen.php.
 * @return boolean|int Number of bytes written to the lof file, false otherwise.
 */
if (!function_exists('magiclogin_log')) {
	function magiclogin_log($msg = 'something went wrong', $type = 'error'){
		$pluginlog = plugin_dir_path(__FILE__) . 'logs/' . $type . '.log';
		if (!file_exists(plugin_dir_path(__FILE__) . 'logs/')) {
			mkdir(plugin_dir_path(__FILE__) . 'logs/', 0777, true);
		}
		$logDate = date('d-M-Y h:i:s A (e)');
		$message = "[ $logDate ] : " . ucwords($type . " : "). strtolower($msg) . PHP_EOL;
		error_log($message, 3, $pluginlog);
	}
}

register_activation_hook( __FILE__, function(){
	if ( empty(get_option('magicloginapi_token_settings_options')) ) {
		update_option('magicloginapi_token_settings_options', [
			"single_use" => "true",
			"life_span" => 5,
			"invalidates_on_creation" => "true",
			"invalidates_others_on_use" => "true"
		]);
	}
});

require "lib/core.php";
require "admin/admin-setting.php";

if (!class_exists('MagicLoginMail')) {
	require_once(dirname(__FILE__) . '/lib/class-magicloginmail.php');
}

if (!class_exists('MagicLoginAPI')) {
	require_once(dirname(__FILE__) . '/lib/class-magicloginapi.php');
}