<?php

namespace WazFactor\WazFrame\Internal\EventManagement;

use WazFactor\WazFrame\Admin\AdminPageSubscriber;
use WazFactor\WazFrame\DesignSystem\BlockSupport\Layout\LayoutSupportSubscriber;
use WazFactor\WazFrame\Vendor\League\Container\Container;
use WazFactor\WazFrame\Internal\Translation\TranslationSubscriber;
use WazFactor\WazFrame\Vendor\League\Container\Argument\Literal\ArrayArgument;
use WazFactor\WazFrame\Vendor\League\Container\ContainerAwareTrait;

class EventManagerSubscriber implements EventManagerAwareInterface
{
	use ContainerAwareTrait;
	
	/**
	 * The Event Manager object.
	 *
	 * @var EventManager
	 */
	private EventManager $event_manager;
	
	
	public function __construct( Container $container, EventManager $event_manager )
	{
		$this->event_manager = $event_manager;
		$this->container = $container;
		$container = $this->getContainer();
		
		$this->getSubscribers( $container );
		$this->addSubscribers( $container );
	}
	
	public function getSubscribers( $container )
	{
		
		$subscribers = array(
			$container->get( AdminPageSubscriber::class ),
			$container->get( TranslationSubscriber::class ),
			$container->get( LayoutSupportSubscriber::class ),
		);
		
		$container->add( 'subscribers', new ArrayArgument( $subscribers ) );
	}
	
	public function addSubscribers( $container )
	{
		$subscribers = $container->get( 'subscribers' );
		
		foreach ( $subscribers as $subscriber ) {
			$this->event_manager->setSubscriber( $subscriber );
			if ( $subscriber instanceof RemoveSubscriberInterface ) {
				$this->event_manager->unSetSubscriber( $subscriber );
			}
		}
	}
	
	/**
	 * Sets the event manager.
	 *
	 * @param EventManager $event_manager
	 */
	public function setEventManager( EventManager $event_manager )
	{
		$this->event_manager = $event_manager;
	}
}