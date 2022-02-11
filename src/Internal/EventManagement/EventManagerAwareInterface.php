<?php

namespace WazFactor\WazFrame\Internal\EventManagement;

/**
 * The EventManager class handles all interaction between classes and the WordPress Plugin API.
 *
 * This interface is used for classes who need to be aware of the Event Manager and use its methods,
 * rather than simply 'subscribing' to a hook via the PluginAPIInterface. An example implementation
 * would be a class that needs to remove (unsubscribe) a hook from WordPress and replace it with
 * another functionality.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @author  Frank Wazeter <design@wazeter.com>
 * @package WazFrame
 * @since   0.0.1
 */
interface EventManagerAwareInterface
{
	/**
	 * Sets the event manager.
	 *
	 * @param EventManager $event_manager
	 */
	public function setEventManager( EventManager $event_manager );
}