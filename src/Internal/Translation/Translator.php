<?php

namespace WazFactor\WazFrame\Internal\Translation;

/**
 * Translator that translates strings using the WordPress translation API.
 *
 * Rather than wrapping every string in __( 'string', text_domain ) this simply wraps
 * the string entries and translates them using the API.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @author  Frank Wazeter <design@wazeter.com>
 * @package WazFrame
 * @since   0.0.1
 */
class Translator
{
	/**
	 * Text domain to be used.
	 *
	 * @var string
	 */
	private string $plugin_domain;
	
	/**
	 * Constructor
	 *
	 * @param string $plugin_domain
	 */
	public function __construct( string $plugin_domain )
	{
		$this->plugin_domain = $plugin_domain;
	}
	
	/**
	 * Translates the given string.
	 *
	 * @param string
	 *
	 * @return string
	 */
	public function translate( $string ): string
	{
		return __( $string, $this->plugin_domain );
	}
}