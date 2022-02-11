<?php

namespace WazFactor\WazFrame\Admin;

use WazFactor\WazFrame\Internal\Translation\Translator;
use WazFactor\WazFrame\Vendor\League\Container\ServiceProvider\AbstractServiceProvider;


class AdminPageServiceProvider extends AbstractServiceProvider
{
	
	public function provides( string $id ): bool
	{
		$services = array(
			AdminPage::class,
			Translator::class,
		);
		
		return in_array( $id, $services );
	}
	
	public function register(): void
	{
		$container = $this->getContainer();
		
		$container->addShared( AdminPage::class )
		          ->addArgument( Translator::class )
		          ->addArgument( 'template_path' );
	}
}