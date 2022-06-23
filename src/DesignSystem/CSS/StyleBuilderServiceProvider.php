<?php

namespace WazFactor\WazFrame\DesignSystem\CSS;

use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Center\Center;
use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Center\IntrinsicCenter;
use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Cluster\Cluster;
use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Stack\Stack;
use WazFactor\WazFrame\Vendor\League\Container\ServiceProvider\AbstractServiceProvider;

class StyleBuilderServiceProvider extends AbstractServiceProvider
{
	
	public function provides( string $id ): bool
	{
		$services = array(
			StyleBuilder::class,
			Center::class,
			IntrinsicCenter::class,
			Cluster::class,
			Stack::class,
		);
		
		return in_array( $id, $services );
	}
	
	public function register(): void
	{
		$container = $this->getContainer();
		
		$container->addShared( StyleBuilder::class );
		
		$container->addShared( Center::class )
		          ->addArgument(StyleBuilder::class);
		
		$container->addShared( IntrinsicCenter::class )
		          ->addArgument(StyleBuilder::class);
		
		$container->addShared( Cluster::class )
		          ->addArgument(StyleBuilder::class);
		
		$container->addShared( Stack::class )
		          ->addArgument(StyleBuilder::class);
	}
}