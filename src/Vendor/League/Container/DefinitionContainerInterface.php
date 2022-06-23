<?php

declare( strict_types=1 );

namespace WazFactor\WazFrame\Vendor\League\Container;

use WazFactor\WazFrame\Vendor\League\Container\Definition\DefinitionInterface;
use WazFactor\WazFrame\Vendor\League\Container\Inflector\InflectorInterface;
use WazFactor\WazFrame\Vendor\League\Container\ServiceProvider\ServiceProviderInterface;
use WazFactor\WazFrame\Vendor\Psr\Container\ContainerInterface;

interface DefinitionContainerInterface extends ContainerInterface
{
	public function add( string $id, $concrete = null ): DefinitionInterface;
	
	public function addServiceProvider( ServiceProviderInterface $provider ): self;
	
	public function addShared( string $id, $concrete = null ): DefinitionInterface;
	
	public function extend( string $id ): DefinitionInterface;
	
	public function getNew( $id );
	
	public function inflector( string $type, callable $callback = null ): InflectorInterface;
}
