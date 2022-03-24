<?php
/**
 * The EventManager is a mediator class that manages events using the WordPress Plugin API.
 */

namespace WazFactor\WazFrame\Internal\EventManagement;

/**
 * Any class that needs to communicate with the core WordPress Plugin API needs to register
 * the EventManager in their Container via its respective configuration file.
 *
 * We use the 'callback' terminology here because verbiage like addHook doesn't describe the action
 * appropriately. What we're really doing isn't adding a hook to the Plugin API, but adding a callback
 * function that the plugin API can call later.
 *
 * WordPress action & filter hooks both use add_filter. Action hooks are simply a wrapper around
 * the filter hook and act as a decorator. Callbacks get added to the 'wp_filter' array in WordPress,
 * which the Plugin API uses as storage.
 *
 * 'add' & '_add' are the two methods that enable adding an event subscriber.
 *
 * For full copyright and license information, view the LICENSE file distributed with the source code.
 *
 * @author  Frank Wazeter <design@wazeter.com>
 * @package WazFrame
 * @since   0.1.0
 */
class EventManager
{
	/**
	 * Allows a subscriber to interact with the WordPress Plugin API.
	 *
	 * Registers all hooks that the subscriber wants to register with the WordPress
	 * Plugin API (action, filter hooks).
	 *
	 * @param SubscriberInterface $subscriber
	 *
	 * @return void
	 */
	public function setSubscriber( SubscriberInterface $subscriber )
	{
		// If the subscriber needs to depend on the event manager.
		if ( $subscriber instanceof EventManagerAwareInterface ) {
			$subscriber->setEventManager( $this );
		}
		
		foreach ( $subscriber->getSubscribedEvents() as $tag => $parameters ) {
			$this->_add( $subscriber, $tag, $parameters );
		}
	}
	
	public function unSetSubscriber( RemoveSubscriberInterface $subscriber )
	{
		foreach ( $subscriber->getUnSubscribedEvents() as $tag => $functions ) {
			if (is_string( $functions ) ) {
				$this->_remove( $subscriber, $tag, $functions );
			} elseif ( is_array( $functions ) ) {
				foreach( $functions as $parameters ) {
					$this->_remove( $subscriber, $tag, $parameters );
				}
			}
		}
	}
	
	
	/**
	 * Adds a callback function to a specific hook in the WordPress Plugin API.
	 * This is a wrapper around add_filter, just like WordPress's add_action() function.
	 *
	 * Example equivalent in WordPress: add_action( 'init', 'callback_name', 10, 2 );
	 *
	 * @uses add_filter()
	 *
	 * @param string   $tag         The WordPress hook name to subscribe to.
	 * @param callable $callback    The callback method to subscribe.
	 * @param int      $priority    Load Priority in WordPress. Default is 10.
	 * @param int      $args        The number of accepted arguments for the callback method. Default is 1.
	 */
	public function add( string $tag, callable $callback, int $priority = 10, int $args = 1 )
	{
		add_filter( $tag, $callback, $priority, $args );
	}
	
	/**
	 * Gets the name of the hook that the WordPress plugin API is executing. Returns
	 * false if it isn't executing a hook.
	 *
	 * @uses current_filter()
	 *
	 * @return string|bool   Returns the name of the hook if it's executing, False if it isn't executing a hook.
	 */
	public function get()
	{
		return current_filter();
	}
	
	/**
	 * Checks WordPress Plugin API to see if the given hook has the given callback.
	 *
	 * The priority of the callback will be returned or false. If no callback
	 * is given, will return true or false if there's any callback registered
	 * to the hook.
	 *
	 * @uses has_filter()
	 *
	 * @param string $tag         The WordPress Hook to check.
	 * @param mixed  $callback    The callback function to check with WordPress Plugin API.
	 *
	 * @return bool|int
	 */
	public function has( string $tag, $callback = false )
	{
		return has_filter( $tag, $callback );
	}
	
	/**
	 * Executes all the functions registered with the hook with the given name.
	 *
	 * Calls the callback functions that have been added to an action hook,
	 * specifying arguments in an array. 'do_action()' in WordPress is identical,
	 * but the args passed to the functions hooked to $hook_name are supplied
	 * via an array in this version, matching how registering callbacks.
	 *
	 * @uses do_action_ref_array()
	 *
	 * @param string $tag     The hook name to execute.
	 * @param mixed  $args    An array typically, args supplied to functions hooked to $hook_name.
	 */
	public function execute( string $tag, $args = null )
	{
		// Separate $tag from the args (e.g. callback, $priority, $accepted_args).
		$args = array_slice( func_get_args(), 1 );
		
		// We use 'do_action_ref_array' so that we can mock our flexible function design.
		do_action_ref_array( $tag, $args );
	}
	
	/**
	 * Filters the given value by applying all changes associated with the hook with
	 * the given name to the given value. Returns the filtered value.
	 *
	 * The WordPress Plugin API is filtering a value by applying changes to it,
	 * not applying hooks to a given value.
	 *
	 * The 'apply_filters' function in WordPress is identical, but the args passed
	 * to the functions hooked to $hook_name are supplied as an array instead, therefore
	 * using this function is more appropriate with our logic.
	 *
	 * @uses apply_filters_ref_array()
	 *
	 * @param string $tag      The hook name in WordPress.
	 * @param mixed  $value    Our submitted parameters (e.g. callback, $priority, $accepted_args).
	 *
	 * @return mixed
	 */
	public function filter( string $tag, $value )
	{
		// Remove $tag name from the arguments.
		$args = array_slice( func_get_args(), 1 );
		
		// We use 'apply_filters_ref_array' so that we can mock our flexible function design.
		return apply_filters_ref_array( $tag, $args );
	}
	
	/**
	 * Removes a callback function from a specific hook of the WordPress Plugin API. This is a
	 * wrapper around remove_filter. IMPORTANT: The WordPress Plugin API will only
	 * remove a hook if the callback and priority match the registered hook.
	 *
	 * Example traditional equivalent: remove_filter( 'init', 'callback_method_name', 10, 2 );
	 *
	 * @uses remove_filter()
	 *
	 * @param string   $tag         The WordPress hook name to subscribe to.
	 * @param callable $callback    The callback method or function to subscribe.
	 * @param int      $priority    Load priority in WordPress
	 *
	 * @return bool
	 */
	public function remove( string $tag, callable $callback, int $priority = 10 ): bool
	{
		return remove_filter( $tag, $callback, $priority );
	}
	
	/**
	 * Adds the provided subscriber's callback method to a specific hook in the WordPress Plugin API.
	 *
	 * Determines the kind of registration that needs to be performed with WordPress by evaluating
	 * whether the passed $parameters are a string or an array, & sets the correct values accordingly.
	 *
	 * If the $priority (always index 1) or $args (index 2) are blank, then the `add` method defaults
	 * are used.
	 *
	 * //TODO Can probably change this to ...$parameters syntax.
	 *
	 * @param SubscriberInterface $subscriber    The subscriber interface object.
	 * @param string              $tag           The WordPress Hook to subscribe to.
	 * @param mixed               $parameters    The parameters to subscribe (either string or an array)
	 *
	 * @return void
	 */
	private function _add( SubscriberInterface $subscriber, string $tag, $parameters )
	{
		if ( is_string( $parameters ) ) {
			$this->add( $tag, array( $subscriber, $parameters ) );
		} elseif ( is_array( $parameters ) && isset( $parameters[0] ) ) {
			$this->add( $tag, array( $subscriber, $parameters[0] ),
				$parameters[1] ?? 10,
				$parameters[2] ?? 1 );
		}
	}
	
	/**
	 * Removes the provided subscriber's callback method on a specific hook in the WordPress Plugin API.
	 *
	 * When removing a callback from a WordPress hook, we do not need to pass the accepted args, but
	 * the priority levels must match.
	 *
	 * @param RemoveSubscriberInterface $subscriber             The Subscriber Interface.
	 * @param string              $tag                    The WordPress hook name to subscribe to.
	 * @param mixed               $params                 The parameters to subscribe ( callback function, (int)
	 *                                                    priority, (int) accepted args.
	 */
	private function _remove( RemoveSubscriberInterface $subscriber, string $tag, $params )
	{
		if ( is_string( $params ) ) {
			if ( function_exists( $params ) ) {
				$this->remove( $tag, $params );
			}
		} elseif ( is_array( $params ) && isset( $params[0] ) ) {
			if ( function_exists( $params[0] ) ) {
				$this->remove(
					$tag,
					$params[0],
					$params[1] ?? 10
				);
			}
		}
	}
}