<?php

namespace WazFactor\WazFrame\Internal\EventManagement;
/**
 * Interface that allows a class to subscribe to action or filter hooks inside
 * the WordPress Plugin API. We use the "subscriber" terminology here as a way to
 * make the descriptive language richer. That said, because the context is exclusively
 * within WordPress, the actual interface name refers to the Plugin API & the callable
 * "getHooks" is easier to read for a WordPress developer.
 *
 * When the EventManager adds a Subscriber, it gets all WordPress events that it
 * wants to listen to, then adds the subscriber as a listener for each of them.
 *
 * NOTE: WordPress action & filter hooks are the same under the hood, add_action is simply
 * a decorator around add_filter.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @package WazFrame
 * @since   0.0.1
 */
interface SubscriberInterface
{
	/**
	 * Returns an array of events that the subscriber will listen to.
	 *
	 * Array key = event name. Acceptable values are:
	 * -Method Name.
	 * -Array with method name & priority.
	 * -Array with method name, priority & number of accepted arguments.
	 *
	 * When an array is supplied, the method name is always at index 0.
	 *
	 * Example Valid Inputs:
	 *
	 * -Method Name Only:
	 * array( 'hook_name'    => 'method_name' );
	 *
	 * -Array with method name & priority.
	 * array( 'hook_name'    => array( 'method_name', $priority ) );
	 *
	 * -Array with method name, priority & number of accepted arguments.
	 * array( 'hook_name'    => array( 'method_name', $priority, $accepted_args ) );
	 *
	 * -Subscribing multiple methods to a hook.
	 * array( 'hook_name'    => array(     array( 'method_name', $priority, $accepted_args ) ),
	 *                                array( 'method_name_2', $priority_2, $accepted_args_2 ) );
	 *
	 * @return array
	 */
	public static function getSubscribedEvents(): array;
}