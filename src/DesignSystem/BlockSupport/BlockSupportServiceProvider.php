<?php

namespace WazFactor\WazFrame\DesignSystem\BlockSupport;

use WazFactor\WazFrame\DesignSystem\BlockSupport\Layout\LayoutSupport;
use WazFactor\WazFrame\DesignSystem\BlockSupport\Layout\LayoutStyles;
use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilder;
use WazFactor\WazFrame\Vendor\League\Container\ServiceProvider\AbstractServiceProvider;

class BlockSupportServiceProvider extends AbstractServiceProvider
{
	
	public function provides( string $id ): bool
	{
		$services = array(
			BlockSupport::class,
			LayoutSupport::class,
			LayoutStyles::class,
			StyleBuilder::class,
		);
		
		return in_array( $id, $services );
	}
	
	public function register(): void
	{
		$container = $this->getContainer();
		
		$container->addShared(\WP_Block_Supports::class );
		
		$container->addShared( BlockSupport::class );
		
		$container->addShared( LayoutSupport::class )
					->addArgument( LayoutStyles::class )
		            ->addArgument( BlockSupport::class );
		
		$container->addShared( LayoutStyles::class )
					->addArgument( StyleBuilder::class );
	}
}