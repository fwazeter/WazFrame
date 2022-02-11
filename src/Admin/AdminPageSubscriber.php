<?php

namespace WazFactor\WazFrame\Admin;

use WazFactor\WazFrame\Internal\EventManagement\SubscriberInterface;

/**
 * Subscriber that registers plugin's admin page with WordPress.
 */
class AdminPageSubscriber implements SubscriberInterface
{
	/**
	 * The admin page.
	 *
	 * @var AdminPage
	 */
	protected AdminPage $page;
	/**
	 * Basename of the plugin
	 *
	 * @var string
	 */
	protected string $plugin_basename;
	
	/**
	 * Constructor
	 *
	 * @param AdminPage $page
	 * @param string    $plugin_basename
	 */
	public function __construct( AdminPage $page, string $plugin_basename )
	{
		$this->page = $page;
		$this->plugin_basename = $plugin_basename;
	}
	
	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return array(
			'admin_init'          => 'configure',
			'admin_menu'          => 'addAdminPage',
			'plugin_action_links' => array( 'addPluginPageLink', 10, 2 ),
		);
	}
	
	/**
	 * Adds the plugin's admin page to the options menu.
	 *
	 * Wrapper around add_submenu_page().
	 *
	 * @uses add_submenu_page()
	 */
	public function addAdminPage()
	{
		add_submenu_page(
			$this->page->getParentSlug(),
			$this->page->getPageTitle(),
			$this->page->getMenuTitle(),
			$this->page->getCapability(),
			$this->page->getSlug(),
			array( $this->page, 'renderAdminPage' )
		);
	}
	
	/**
	 * Adds link from plugins page to WazFrame admin page.
	 *
	 *
	 * @param array  $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function addPluginPageLink( array $links, string $file ): array
	{
		if ( $file != $this->plugin_basename ) {
			return $links;
		}
		// below for multisite.
		/*array_unshift($links, sprintf(
				'<a href="%s">%s</a>',
				$this->page->getPageURL(),
				$this->page->getPluginsPageTitle()
			)
		);*/
		return $links;
	}
	
	/**
	 * Configure admin page using the settings API.
	 */
	public function configure()
	{
		$this->page->configure();
	}
}