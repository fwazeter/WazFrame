<?php

namespace WazFactor\WazFrame\Internal\EventManagement;

/**
 * The EventManagerAwareInterface is used by subscribers that need to
 * access the EventManager.
 *
 * Implements both the EventManagerAware & Subscriber Interfaces to give a child
 * class access to both subscribing callbacks & interacting with the WordPress Plugin API.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @author  Frank Wazeter <design@wazeter.com>
 * @package WazFrame
 * @since   0.1.0
 */
abstract class AbstractEventSubscriber implements EventManagerAwareInterface, SubscriberInterface
{
	
	/**
	 * The WordPress Plugin API manager.
	 *
	 * @var EventManager
	 */
	protected EventManager $event_manager;
	
	/**
	 * Instantiates the EventManager.
	 */
	public function setEventManager( EventManager $event_manager )
	{
		$this->event_manager = $event_manager;
	}
}