<?php
/*
 * Plugin Name: WazFrame
 * Plugin URI: https://wazfactor.com/wazframe
 * Description: Easy CSS management for WordPress Full Site Editing & Block Themes. Set your own defaults for auto-generated CSS & core block css.
 * Version: 0.1.0
 * Author: Frank Wazeter
 * Author URI: https://wazeter.com
 * Text Domain: wazframe
 * Domain Path: /i18n/languages
 * Requires at least: 5.9
 * Requires PHP: 7.4
 *
 * @package WazFrame
 */

use WazFactor\WazFrame\Autoloader;
use WazFactor\WazFrame\Plugin;

defined( 'ABSPATH' ) || exit;


require_once dirname( __FILE__ ) . '/src/Autoloader.php';
Autoloader::register();

$load_plugin = new Plugin( __FILE__ );
add_action( 'after_setup_theme', array( $load_plugin, 'load' ) );
