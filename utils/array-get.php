<?php
/**
 * DUPLICATION OF WordPress Core private function.
 * Making a copy here to abstract from functions intended to be private & not used
 * in themes & plugins here.
 *
 * Accesses an array in depth based on a path of keys.
 *
 * It is the PHP equivalent of JavaScript's `lodash.get()` and mirroring it may help other components
 * retain some symmetry between client and server implementations.
 *
 * Example usage:
 *
 *     $array = array(
 *         'a' => array(
 *             'b' => array(
 *                 'c' => 1,
 *             ),
 *         ),
 *     );
 *     _wp_array_get( $array, array( 'a', 'b', 'c' ) );
 *
 * @url https://github.com/WordPress/wordpress-develop/blob/5.8.1/src/wp-includes/functions.php#L4739-L4757
 *
 * @param array $array An array from which we want to retrieve some information.
 * @param array $path An array of keys describing the path with which to retrieve information.
 * @param mixed $default The return value if the path does not exist within the array,
 *                       or if `$array` or `$path` are not arrays.
 *
 * @return mixed The value from the path specified.
 */
function wf_wp_array_get( array $array, array $path, $default = null ) {
	// Confirm $path is valid.
	if ( ! is_array( $path ) || 0 === count( $path ) ) {
		return $default;
	}

	foreach ( $path as $path_element ) {
		if (
			! is_array( $array ) ||
			( ! is_string( $path_element ) && ! is_integer( $path_element ) && ! is_null( $path_element ) ) ||
			! array_key_exists( $path_element, $array )
		) {
			return $default;
		}
		$array = $array[ $path_element ];
	}

	return $array;
}