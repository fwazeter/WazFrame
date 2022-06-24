<?php


namespace WazFactor\WazFrame\DesignSystem\CSS;


/**
 * Creates CSS declaration blocks, properties & styles to be used
 * for either inline styles or automatic entry into other stylesheets.
 *
 * @author Frank Wazeter
 * @since  0.1.0
 */
class StyleBuilder {
	/**
	 * Creates a CSS Style declaration block to be used either on it's own or
	 * as part of a stylesheet.
	 *
	 * Selector must include any additional conditions, e.g. "v-stack * + *"
	 * Properties can either be a single string or an array.
	 *
	 * @param string          $selector      The CSS Selector for declaration block.
	 * @param array|string    $properties    The properties, either an array or string to add to block.
	 *
	 * @return string                       The final Declaration block to return.
	 *
	 */
	public function buildDeclarationBlock ( string $selector, $properties ): string {
		// Cleans the selector in case of an extra { added to input.
		$clean_selector = str_replace( '{', '', $selector );
		// Assembles the declaration block.
		$declaration_block = "$clean_selector {\n";
		$declaration_block .= $this->set( $properties, true ) . " \n";
		$declaration_block .= "} \n";
		
		return $declaration_block;
	}
	
	/**
	 * Takes input from an array and returns the corresponding style
	 * properties as a string.
	 *
	 * If an array is given, provide the whole style in one line, such as:
	 *      $properties = array('margin-right: 0px', 'margin-left: 0px').
	 *
	 *      If an extra semi-colon is added at the end, this is cleaned.
	 *
	 * If an associative array is provided, then the key becomes the property
	 * name and the value becomes the style value.
	 *
	 * Note: in WordPress, if we add the array|string type to $properties type hint
	 * it will fail to render and pass an error. Also, if we pass mixed same result.
	 *
	 * @param array|string    $properties    The Properties to add to style. An Array or string.
	 * @param bool            $addSpace      Adds Spacing after each new line. Default false.
	 *
	 * @return string
	 */
	public function set ( $properties, bool $addSpace = false ): string {
		$style = '';
		if ( is_string( $properties ) ) {
			
			$style .= $this->buildStyle( $properties );
		} elseif ( is_array( $properties ) ) {
			
			// Checks for sequential keys of an array, if true, it's associative.
			if ( array_keys( $properties ) !== range( 0, count( $properties ) - 1 ) ) {
				foreach ( $properties as $property => $value ) {
					if ( $addSpace ) {
						$style .= $this->buildStyle( $property, $value ) . "\n";
					} else {
						$style .= $this->buildStyle( $property, $value );
					}
				}
			} else {
				foreach ( $properties as $property ) {
					if ( $addSpace ) {
						$style .= $this->buildStyle( $property ) . "\n";
					} else {
						$style .= $this->buildStyle( $property );
					}
				}
			}
		}
		
		return $style;
	}
	
	/**
	 * Takes a string and transforms it into CSS syntax for declaration block
	 * property: value; pairs.
	 *
	 * Has basic cleaning mechanism for : & ; typos, but this needs to be refined more, likely
	 * with a preg_replace or the like with regex.
	 *
	 *
	 * @param string         $property    Accepts the given property to transform into style format.
	 * @param string|null    $value       Accepts a value property, default = null means to clean the string.
	 *
	 * @return string
	 */
	public function buildStyle ( string $property, string $value = null ): string {
		$clean_property = '';
		$clean_value    = '';
		// if we were passed both a $property & $value, we want to clean them.
		if ( $value !== null ) {
			$clean_property = str_replace( ':', '', $property );
			$clean_value    = str_replace( ';', '', $value );
		} else {
			// if the passed $value is null, that means we were supplied a single string.
			list( $key, $value ) = explode( ':', $property );
			// filter the string into $key => $value, for basic cleaning.
			$result[ $key ] = $value;
			foreach ( $result as $key => $value ) {
				$clean_property = str_replace( ';', '', $key );
				$clean_value    = str_replace( ';', '', $value );
			}
		}
		
		return "$clean_property: $clean_value; ";
	}
}