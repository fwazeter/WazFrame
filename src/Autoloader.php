<?php

namespace WazFactor\WazFrame;

/**
 * Autoloads plugin classes using PSR-4 standard.
 *
 * Replaces the need to include all classes, e.g. via require_once( __DIR__ . '/class-name.php' );
 *
 * As a side benefit, classes are only loaded when they are called, cutting
 * back on the amount of code loaded at any time. If the class isn't called
 * on a particular page, it's not loaded.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @author  Frank Wazeter <design@wazeter.com
 * @package WazFrame
 * @since   0.0.1
 */
class Autoloader
{
	/**
	 * Handles autoloading of classes for the plugin.
	 *
	 * @param string $class    The class to autoload.
	 */
	public static function autoload( string $class )
	{
		// if the string position isn't 0, return.
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}
		
		// returns the string value of class, minus length of namespace.
		// e.g. WazFrame\Plugin = '\Plugin'
		$class = substr( $class, strlen( __NAMESPACE__ ) );
		// replaces '\' with '/' or null character with ''.
		$file = dirname( __FILE__ )
			. str_replace( array( '\\', "\0" ), array( '/', '' ), $class )
			. '.php';
		
		if ( is_file( $file ) ) {
			require $file;
		}
	}
	
	/**
	 * Registers Autoloader as an SPL autoloader.
	 *
	 * @param bool $prepend    Whether to prepend class.
	 */
	public static function register( bool $prepend = false )
	{
		spl_autoload_register( array( new self(), 'autoLoad' ), true, $prepend );
	}
}