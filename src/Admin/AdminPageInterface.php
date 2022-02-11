<?php

namespace WazFactor\WazFrame\Admin;

/**
 * Interface for creating admin pages in the WordPress Dashboard.
 *
 * WordPress add_menu_page() code reference:
 * add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug,
 *                callable $function= '', string $icon_url = '', int $position = null )
 *
 * WordPress add_submenu_page() code reference:
 *
 * add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability,
 *                    string $menu_slug, callable $function = '', int $position = null )
 *
 * $position is an optional variable for both, we may include that here. Otherwise, the add_menu_page()
 * function has an $icon_url parameter (optional), while submenu page has $parent_slug (required) instead.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @author  Frank Wazeter <design@wazeter.com>
 * @package WazFrame
 * @since   0.1.0
 */
interface AdminPageInterface
{
	/**
	 * Gets the capability (permissions) required for this menu to be displayed to the user.
	 *
	 * Used for 'both add_menu_page()' & 'add_submenu_page()'. Required Parameter.
	 *
	 * WordPress param name: $capability
	 *
	 * @return string
	 */
	public function getCapability(): string;
	
	/**
	 * Gets the title to display for the admin page in the WordPress admin menu.
	 *
	 * Used for 'both add_menu_page()' & 'add_submenu_page()' Required Parameter.
	 *
	 * WordPress param name: $menu_title
	 *
	 * @return string
	 */
	public function getMenuTitle(): string;
	
	/**
	 * Gets the text to be displayed in the title tags of the page when the menu is selected.
	 *
	 * Used for 'both add_menu_page()' & 'add_submenu_page()' Required Parameter.
	 *
	 * WordPress param name: $page_title
	 *
	 * @return string
	 */
	public function getPageTitle(): string;
	
	/**
	 * Gets the slug name for the parent menu (or the file name of a standard WordPress admin page).
	 *
	 * Used for 'add_submenu_page()' only. Required Parameter.
	 *
	 * WordPress param name: $parent_slug
	 *
	 * @return string
	 */
	public function getParentSlug(): string;
	
	/**
	 * Gets the slug name to refer to this menu by. Should be unique for this menu and only include
	 * lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
	 *
	 * Used for 'both add_menu_page()' & 'add_submenu_page()' Required Parameter.
	 *
	 * WordPress param name: $menu_slug
	 *
	 * @return string
	 */
	public function getSlug(): string;
	
	/**
	 * Renders the admin page, specifically via function to be called to output the content for this page.
	 * Can be a reference to a template page, php callback function or rendered via JS (e.g. with blocks).
	 *
	 * Used for 'both add_menu_page()' & 'add_submenu_page()' Optional Parameter.
	 *
	 * WordPress param name: $function        Default value: ''
	 */
	public function renderAdminPage();
}