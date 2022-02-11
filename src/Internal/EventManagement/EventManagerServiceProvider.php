<?php

namespace WazFactor\WazFrame\Internal\EventManagement;

use WazFactor\WazFrame\Admin\AdminPage;
use WazFactor\WazFrame\Admin\AdminPageSubscriber;
use WazFactor\WazFrame\Internal\Translation\Translator;
use WazFactor\WazFrame\Vendor\League\Container\ServiceProvider\AbstractServiceProvider;
use WazFactor\WazFrame\Internal\Translation\TranslationSubscriber;

/**
 * ServiceProvider classes organize container Definitions and increase performance
 * (especially as the plugin grows in size). All definitions registered with a service provider
 * are lazily registered at the point where the service is retrieved. 'Definitions' are how League/Container
 * describes the dependency map internally - they contain information on how to build your classes.
 *
 * The ServiceProvider class must extend either AbstractServiceProvider from League\Container\ServiceProvider.
 * or a custom Abstract implementation that extends it.
 *
 */
class EventManagerServiceProvider extends AbstractServiceProvider
{
	/**
	 * The provides method lets the container know that a service
	 * is provided by this ServiceProvider.
	 *
	 * The method must return true or false when the container invokes
	 * it with a service name.
	 *
	 * @param string $id    An alias (e.g. 'key'), class or interface name.
	 *
	 * @return bool             True if the alias exists, else false.
	 */
	public function provides( string $id ): bool
	{
		$services = array(
			EventManager::class,
			AbstractEventSubscriber::class,
			AdminPageSubscriber::class,
			TranslationSubscriber::class,
			EventManagerSubscriber::class,
		);
		
		return in_array( $id, $services );
	}
	
	public function register(): void
	{
		// gets the Container object.
		$container = $this->getContainer();
		
		$container->addShared( EventManager::class );
		
		// Adds subscribers to the EventManager.
		$container->addShared( EventManagerSubscriber::class )
		          ->addArgument( $this->getContainer() )
		          ->addArgument( EventManager::class );
		
		
		$container->addShared( AdminPageSubscriber::class )
		          ->addArguments( [
			          AdminPage::class,
			          'plugin_basename',
			          'template_path',
			          Translator::class
		          ] );
		
		$container->addShared( TranslationSubscriber::class )
		          ->addArguments( [ 'plugin_domain', 'translations_path' ] );
	}
}