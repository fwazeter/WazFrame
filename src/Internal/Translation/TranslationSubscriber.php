<?php

namespace WazFactor\WazFrame\Internal\Translation;

use WazFactor\WazFrame\Internal\EventManagement\AbstractEventSubscriber;

class TranslationSubscriber extends AbstractEventSubscriber
{
	
	/**
	 * Domain of the plugin translation.
	 *
	 * @var string
	 */
	protected string $plugin_domain;
	
	/**
	 * Relative path to the plugin translation files.
	 *
	 * @var string
	 */
	protected string $translations_path;
	
	/**
	 * Constructor
	 *
	 * @param string $plugin_domain
	 * @param string $translations_path
	 */
	public function __construct( string $plugin_domain, string $translations_path )
	{
		$this->plugin_domain = $plugin_domain;
		$this->translations_path = $translations_path; /*rtrim( $translations_path, '/' );*/
	}
	
	/**
	 * WP Hooks to subscribe to.
	 */
	public static function getSubscribedEvents(): array
	{
		return array(
			'init'                   => 'registerTranslations',
			'load_textdomain_mofile' => array( 'getDefaultTranslation', 10, 2 ),
		);
	}
	
	
	/**
	 * Ensure that we load the "en_US" translation if there's no readable translation file.
	 *
	 * This is necessary since we're using placeholder values for text, instead of english text.
	 *
	 * @param string $mofile_path
	 * @param string $plugin_domain
	 *
	 * @return string
	 */
	public function getDefaultTranslation( string $mofile_path, string $plugin_domain ): string
	{
		if ( $plugin_domain !== $this->plugin_domain || false === stripos( $mofile_path, $this->translations_path ) ) {
			return $mofile_path;
		}
		if ( ! is_readable( $mofile_path ) ) {
			$mofile_path = preg_replace(
				'/' . $this->plugin_domain . '-[a-z]{2}_[A-Z]{2}/',
				$this->plugin_domain
				. '-en_US',
				$mofile_path
			);
		}
		return $mofile_path;
	}
	
	/**
	 * Registers the plugin's translation files with WordPress.
	 *
	 * @uses load_textdomain()
	 * @return void
	 */
	public function registerTranslations()
	{
		load_textdomain(
			$this->plugin_domain,
			$this->translations_path
			. '/'
			. $this->plugin_domain
			. '-'
			. $this->event_manager->filter( 'plugin_locale', determine_locale(), $this->plugin_domain )
			. '.mo'
		);
	}
}