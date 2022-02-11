<?php
/**
 * Class of utilities for dealing with arrays.
 */

namespace WazFactor\WazFrame\Utilities;

/**
 * A class of utilities for dealing with arrays.
 */
trait ArrayUtil
{
	/**
	 * Get a value from a nested array by specifying the entire key hierarchy with '::' as separator.
	 *
	 * E.g. for [ 'foo' => [ 'bar' => [ 'fizz' => 'buzz' ] ] ] the value for key 'foo::bar::fizz' is 'buzz'.
	 *
	 * Replicates private utility in WordPress, _wp_array_get
	 *
	 * @param array  $array      The array to get the value from.
	 * @param string $key        The complete key hierarchy using '::' as separator.
	 * @param mixed  $default    The value to return if th ekey doesn't exist in the array.
	 *
	 * @throws \Exception   $array is not an array.
	 * @return mixed    The retrieved value or the supplied default value.
	 */
	public static function getArrayKeyValue( array $array, string $key, $default = null )
	{
		$key_stack = explode( '::', $key );
		$sub_key = array_shift( $key_stack );
		
		if ( isset( $array[ $sub_key ] ) ) {
			$value = $array[ $sub_key ];
			
			if ( count( $key_stack ) ) {
				foreach ( $key_stack as $sub_key ) {
					if ( is_array( $value ) && isset( $value[ $sub_key ] ) ) {
						$value = $value[ $sub_key ];
					} else {
						$value = $default;
						break;
					}
				}
			}
		} else {
			$value = $default;
		}
		
		return $value;
	}
	
	/**
	 * Checks if a given key exists in an array and its value can be evaluated as 'true'.
	 *
	 * @param array  $array    The array to check.
	 * @param string $key      The key for the value to check.
	 *
	 * @return bool True if the key exists in the array and the value can be evaluated as 'true'.
	 */
	public static function hasArrayKey( array $array, string $key ): bool
	{
		return isset( $array[ $key ] ) && $array[ $key ];
	}
	
	/**
	 * Gets value for a given key from an array, or default value if the key doesn't exist in the array.
	 *
	 * @param array  $array      The array to get the value from.
	 * @param string $key        The key to use to retrieve the value.
	 * @param null   $default    The default value to return if the key doesn't exist in the array.
	 *
	 * @return mixed|null   The value for the key or the default value passed.
	 */
	public static function getValueOrDefault( array $array, string $key, $default = null )
	{
		return $array[ $key ] ?? $default;
	}
}
