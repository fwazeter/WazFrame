<?php

namespace WazFactor\WazFrame\Admin;

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
 */
class AdminPage extends AbstractAdminPage
{
	
	
	public function getCapability(): string
	{
		return 'install_plugins';
	}
	
	public function getParentSlug(): string
	{
		return 'themes.php';
	}
}
