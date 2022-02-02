<?php

/**
 * Plugin Name: WazFrame Enhanced
 * Plugin URI: https://wazfactor.com
 * Description: WazFrame fixes many of the quirkiness's of WordPress Core & Gutenberg Full Site Editing, replacing
 * them with more sensible defaults and css classes that do not use auto-generated ID's.
 * Version: 0.0.2
 * Author: Frank Wazeter
 * Author URI: https://wazfactor.com
 * License: GNU General Public License V2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wazframe
 *
 * Requires at least: 5.9
 * Requires PHP: 7.4
 *
 * WazFrame Enhanced, (C) 2021 Wazeter, Inc
 * WazFrame Enhanced is distributed under the terms of the GNU GPL.
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined('WF_PLUGIN_FILE' ) ) {
	define( 'WF_PLUGIN_FILE', __FILE__ );
}
require_once    'utils/array-get.php';
// May not be necessary, but might as well add logical props to safe-style attrs. for future plans.
require_once    'utils/safe-style-attrs.php';

require_once    'block-supports/layout.php';
