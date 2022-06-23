<?php
/**
 * Class of utilities for dealing with strings.
 */

namespace WazFactor\WazFrame\Utilities;

/**
 * Class for dealing with strings.
 */
trait StringUtil
{
	/**
	 * Checks to see if a string starts with another.
	 *
	 * @param string $string            The string we want to check.
	 * @param string $starts_with       The string we're looking for at the start of $string.
	 * @param bool   $case_sensitive    Whether the comparison should be case-sensitive.
	 *
	 * @return bool     True if the $string starts with $starts_with, else false.
	 */
	public static function startsWith( string $string, string $starts_with, bool $case_sensitive = true ): bool
	{
		$length = strlen( $starts_with );
		if ( $length > strlen( $string ) ) {
			return false;
		}
		
		$string = substr( $string, 0, $length );
		
		if ( $case_sensitive ) {
			return strcmp( $string, $starts_with ) === 0;
		}
		
		return strcasecmp( $string, $starts_with ) === 0;
	}
	
	/**
	 * Checks to see if a string ends with another
	 *
	 * @param string $string            The string we want to check.
	 * @param string $ends_with         The string we're looking for at the end of the string.
	 * @param bool   $case_sensitive    Indicates whether the comparison should be case-sensitive.
	 *
	 * @return bool     True if the $string ends with $ends_with.
	 */
	public static function endsWith( string $string, string $ends_with, bool $case_sensitive = true ): bool
	{
		$length = strlen( $ends_with );
		if ( $length > strlen( $string ) ) {
			return false;
		}
		
		$string = substr( $string, -$length );
		
		if ( $case_sensitive ) {
			return strcmp( $string, $ends_with ) === 0;
		}
		
		return strcasecmp( $string, $ends_with ) === 0;
	}
}