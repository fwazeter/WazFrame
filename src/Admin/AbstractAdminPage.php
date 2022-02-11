<?php

namespace WazFactor\WazFrame\Admin;

use WazFactor\WazFrame\Internal\Translation\Translator;

/**
 * WordPress admin page.
 * List of WordPress Parent Slugs for existing menu tabs:
 *        Dashboard: ‘index.php’
 *        Posts: ‘edit.php’
 *        Media: ‘upload.php’
 *        Pages: ‘edit.php?post_type=page’
 *        Comments: ‘edit-comments.php’
 *        Custom Post Types: ‘edit.php?post_type=your_post_type’
 *        Appearance: ‘themes.php’
 *        Plugins: ‘plugins.php’
 *        Users: ‘users.php’
 *        Tools: ‘tools.php’
 *        Settings: ‘options-general.php’
 *        Network Settings: ‘settings.php’ (for multi-site)
 *
 * TODO: Add options, settings, template paths..Translator.
 */
abstract class AbstractAdminPage implements AdminPageInterface
{
	
	/**
	 * Slug used by the admin page.
	 *
	 * @var string
	 */
	protected string $slug = 'wazframe';
	
	/**
	 * Plugin translator.
	 *
	 * @var Translator
	 */
	protected Translator $translator;
	
	/**
	 * Path to the admin page templates.
	 *
	 * @var string
	 */
	protected string $template_path;
	
	/**
	 * Constructor
	 *
	 * TODO: Add Options object, other connectors.
	 *
	 * @param Translator $translator       Translator object.
	 * @param string     $template_path    Path to template file to render.
	 */
	public function __construct( Translator $translator, string $template_path )
	{
		$this->template_path = $template_path;
		$this->translator = $translator;
	}
	
	/**
	 * Configures the admin page for using the settings API.
	 */
	public function configure()
	{
		// TODO: use SETTINGS API here.
	}
	
	/**
	 * Get the title of the admin page in the WordPress admin menu.
	 *
	 * @return string
	 */
	public function getMenuTitle(): string
	{
		return $this->translate( 'menu_title' );
	}
	
	/**
	 * Translates a string within the admin page context. Wraps the
	 * individual strings that'd normally be __( '').
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function translate( string $string ): string
	{
		return $this->translator->translate( 'admin_page.' . $string );
	}
	
	/**
	 * Get the title of the admin page.
	 *
	 * @return string
	 */
	public function getPageTitle(): string
	{
		return $this->translate( 'page_title' );
	}
	
	/**
	 * Renders the admin page.
	 */
	public function renderAdminPage()
	{
		$this->renderTemplate( 'admin' );
	}
	
	/**
	 * Renders given template if readable.
	 *
	 * @param string $template
	 */
	protected function renderTemplate( string $template )
	{
		$template_path = $this->template_path . '/' . $template . '.php';
		
		if ( ! is_readable( $template_path ) ) {
			return;
		}
		
		include $template_path;
	}
	
	/**
	 * Gets the slug used by the admin page.
	 *
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}
}