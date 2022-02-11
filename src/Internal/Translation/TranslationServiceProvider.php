<?php

namespace WazFactor\WazFrame\Internal\Translation;

use WazFactor\WazFrame\Vendor\League\Container\ServiceProvider\AbstractServiceProvider;

class TranslationServiceProvider extends AbstractServiceProvider
{
	
	public function provides( string $id ): bool
	{
		$services = array(
			Translator::class,
		);
		
		return in_array( $id, $services );
	}
	
	public function register(): void
	{
		$container = $this->getContainer();
		
		$container->addShared( Translator::class )
		          ->addArgument( 'plugin_domain' );
	}
}