# The WazFrame Code by WazFactor

The files in the ```src``` directory power the majority of WazFrame, using the ```WazFactor\WazFrame``` namespace and
[PSR-4](https://www.php-fig.org/psr/psr-4/) file naming conventions.

We use the [dependency injection](https://en.wikipedia.org/wiki/Dependency_injection) pattern to keep as much of the
code as de-coupled as possible, utilizing a [PSR-11](https://www.php-fig.org/psr/psr-11/)
container for registering and resolving classes in the codebase.

There are two ```Vendor``` dependencies namespaced under ```WazFactor\WazFrame\Vendor```. There is
the ```Psr\Container``` for the PSR-11 container interfaces and, most importantly,
the [Container from the PHP League](https://container.thephpleague.com/), under ```League\Container```. Inevitably,
we'll add the plugin as a ```composer package``` for convenience installs, but as of now, we've opted to host the Vendor
files directly, because they're lightweight and highly flexible. Additionally, there are no plans at this time to add in
more dependencies to the project, in the spirit of keeping it as easy to develop as possible and as free from external
dependencies that require tooling as possible.

Inevitably, we'll likely trim down unused parts of the PHP League's Container, depending on how further development
goes.

A small, custom ```Autoloader``` is used to require and load the class namespaces, rather than using the Composer
version, which is built to account for a wide variety of situations - we opted to be specific to the needs of our class
structure.

## Built to be SOLIDLY DRY, because it's the best kind of humor.

Typical WordPress plugin development is a monstrosity in duplicate code and intermixing dependencies, hooks and more all
within files that inevitably become monolithic. Much of this is hazard by the way WordPress itself works. We've been
decoupling as much of this as humanly possible to:

- Reduce code duplication
- Make debugging & feature implementation faster
- Faster loading code
- Easy extensibility and re-usability

Faster & higher quality development means a more feature rich environment for the user that doesn't interfere with their
other systems.

### Two Primary Systems that Drive the Plugin

There are two critical systems that we've implemented to make the plugin's codebase highly
efficient: ```Dependency Injection``` and ```Event Management```.

**Dependency Injection** is done via ```constructor injection``` primarily, with a few instances
of ```setter injection``` where appropriate, using a ```Container``` to handle class _registration and resolution_. As
much as possible where it makes sense, ```interfaces``` rather than concrete classes are used to handle dependencies.

Going forward, expanding on the use of ```interfaces``` for major dependencies, and ```traits``` for utility classes
commonly re-used (such as string & array utilities) will be the preferred methods.

The **Event Management** system handles all interactions with the WordPress Plugin API - one central system that handles
all the ```action and filter``` hook needs.

## The Container

PHP League's container is used for dependency injection, and comes along with two important concepts: _resolving_ and _
registering_ classes.

**Resolving** a class means asking the container to provide an instance of the class or interface.
**Registering** a class means telling the container _how_ the class should be resolved.

### What is Dependency Injection?

[Phil Bennett](https://phptherightway.com/#dependency_injection) wrote it best:
> Dependency Injection is providing a component with its dependencies either through constructor injection, method calls or the setting of properties. It is that simple.

## Resolving Classes

When a class depends on another class, the most common method here is to use ```constructor``` injection, although
classes that follow certain patterns, like the ```mediator``` pattern should opt for ```setter / method``` injection.

The most typical way to add a dependency to a class looks a little like this:

```php 
use Namespace\Service1;
use Namespace\Service2;

class HasDependencies
{
    /**
     * A class example.
     * 
     * @var Service1Class
     */
    private Service1Class $service1;
    
    /**
     * Interfaces can also be used.
     * 
     * @var Service2Interface
     */
    private Service2Interface $service2;
    
    public function __construct( Service1Class $service1, Service2Interface $service2 ) 
    {
        $this->$service1 = $service1;
        $this->$service2 = $service2;
    }
    
    public function methodRequiringService1() 
    {
        $this->$service1->do_things();
    }
}

```

**Here's a full example from the code**

```php 
<?php

namespace WazFactor\WazFrame\Admin;

use WazFactor\WazFrame\Internal\Translation\Translator;

/**
 * WordPress admin page.
 *
 */
abstract class AbstractAdminPage implements AdminPageInterface
{
	
	/**
	 * Slug used by the admin page.
	 *
	 * @var string
	 */
	protected string $slug = 'wazframe';
	
	/**
	 * Plugin translator.
	 *
	 * @var Translator
	 */
	protected Translator $translator;
	
	/**
	 * Path to the admin page templates.
	 *
	 * @var string
	 */
	protected string $template_path;
	
	/**
	 * Constructor - where dependencies are injected.
	 *
	 * TODO: Add Options API.
	 *
	 * @param Translator $translator       Translator object.
	 * @param string     $template_path    Path to template file to render.
	 */
	public function __construct( Translator $translator, string $template_path )
	{
		$this->template_path = $template_path;
		$this->translator = $translator;
	}
	
	/**
	 * Get the title of the admin page in the WordPress admin menu.
	 *
	 * @return string
	 */
	public function getMenuTitle(): string
	{
		return $this->translate( 'menu_title' );
	}
	
	/**
	 * Translates a string within the admin page context. Wraps the
	 * individual strings that'd normally be __( '').
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function translate( string $string ): string
	{
		return $this->translator->translate( 'admin_page.' . $string );
	}
	
	/**
	 * Get the title of the admin page.
	 *
	 * @return string
	 */
	public function getPageTitle(): string
	{
		return $this->translate( 'page_title' );
	}
	
	/**
	 * Renders the admin page.
	 */
	public function renderAdminPage()
	{
		$this->renderTemplate( 'admin' );
	}
	
	/**
	 * Renders given template if readable.
	 *
	 * @param string $template
	 */
	protected function renderTemplate( string $template )
	{
		$template_path = $this->template_path . '/' . $template . '.php';
		
		if ( ! is_readable( $template_path ) ) {
			return;
		}
		
		include $template_path;
	}
	
	/**
	 * Gets the slug used by the admin page.
	 *
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}
}
```

Whenever the container resolves ```HasDependencies```, it will also resolve ```service1class``` and ```service2class```,
passing the values along as constructor arguments.

## Registering Classes

In order for the class to resolve properly, it has to be _registered_ inside the same container being referenced.

The ```container``` is read-only. It has a ```get``` method to resolve classes, but it doesn't have a direct method to
register classes. ```Definition``` methods are what are actually doing the work of registration, which is done
via ```ServiceProvider``` classes. The ServiceProvider classes create a dependency map for the container to reference
and enable the classes to be lazily loaded.

## The ServiceProvider Class

Here's a feature-complete example from the code on how a ServiceProvider class looks:

```php 
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
 * When functionality that requires being run as the service provider is added to the container
 * (for example, WordPress action / filter hooks), setting up inflectors or including configuration
 * files, implement the `BootableServiceProviderInterface` from League\Container\ServiceProvider\.
 *
 * IMPORTANT: the register() method is not invoked until one of the aliases it provides is requested
 * by the container.
 *
 * In order to register the ServiceProvider with the container, pass an instance of the provider
 * to the Container, via the League\Container\Container::addServiceProvider method.
 *
 */
class EventManagerServiceProvider extends AbstractServiceProvider
{
	/**
	 * The provides method lets the container know that a service
	 * is provided by this ServiceProvider. Every service registered
	 * via this service provider must have an alias added to this
	 * array, or it will be ignored.
	 *
	 * The method must return true or false when the container invokes
	 * it with a service name (thus, allowing the container to know
	 * ahead of time what this service provider provides, enabling
	 * lazy loading).
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
	
	/**
	 * The register method is where services are defined.
	 *
	 * The getContainer() convenience getter method enables
	 * invoking any of the methods used when defining services directly.
	 * Register the classes, such as add, addShared, addArgument, etc.
	 *
	 * IMPORTANT: any alias added here MUST also return true when passed to the
	 * `provides` method, AKA it must be listed in the $services array. Otherwise,
	 * it will be ignored by the container.
	 *
	 * @return void
	 */
	public function register(): void
	{
		// gets the Container object.
		$container = $this->getContainer();
		
		/**
		 * The EventManager dependency injection relies on being passed Subscriber objects
		 * and is unique for registration methods. Add the corresponding Subscriber class,
		 * e.g. AdminPageSubscriber, along with a container reference to its properties.
		 *
		 * In order to properly have our EventManager system hook into all events with all
		 * the dependencies, we need another layer of abstraction - rather than having the
		 * ServiceProvider handle passing classes to the EventManager, we use a master
		 * 'EventManagerSubscriber' class, which sets all classes that need to subscribe
		 * to events in an array, then we use the ServiceProvider to map the necessary
		 * dependencies, as intended.
		 */
		$container->addShared( EventManager::class );
		
		// Adds subscribers to the EventManager.
		$container->addShared( EventManagerSubscriber::class )
		          ->addArgument( $this->getContainer() )
		          ->addArgument( EventManager::class );
		
		/*	$container->addShared( AbstractEventSubscriber::class )
					  ->addMethodCall( 'setEventManager', [ EventManager::class ] );*/
		
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
	
	/**
	 * In much the same way, this method has access to the container
	 * itself and can interact with it however you wish, the difference
	 * is that the boot method is invoked as soon as you register
	 * the service provider with the container meaning that everything
	 * in this method is eagerly loaded.
	 *
	 * If you wish to apply inflectors or register further service providers
	 * from this one, it must be from a bootable service provider like
	 * this one, otherwise they will be ignored.
	 *
	 * @return void
	 */
	public function boot(): void
	{
		$container = $this->getContainer();
		// class that has EventManagerAwareInterface when subscribers are passed to it.
		$container
			->inflector( EventManagerAwareInterface::class )
			->invokeMethod( 'setEventManager', [ EventManager::class ] );
		
		// Pulls each SubscriberInterface's class 'getSubscribedEvents' method into
		// the class. Without this, the hooks do not get passed.
		$container
			->inflector( SubscriberInterface::class )
			->invokeMethod( 'getSubscribedEvents', [ EventManager::class ] );
	}
}

```

## Event Management

Subscriber classes, e.g. ```AdminPageSubscriber```, describes the interactions with the WordPress Plugin API through
the ```EventManager``` class.

For example, the ```AdminPage``` class would describe construction of the AdminPage itself, but it's interactions and
dependencies related to ```action``` and ```filter``` hooks in WordPress would be done through
the ```AdminPageSubscriber```, which exclusively talks to the EventManager, whose role is to control interactions with
the ```WordPress Plugin API```.

Consequently, the ```EventManagerServiceProvider``` class describes the dependencies that the various ```Subscriber```
classes have with the ```EventManager```.

**Here's an example AdminPageSubscriber from the code**

```php 
<?php

namespace WazFactor\WazFrame\Admin;

use WazFactor\WazFrame\Internal\EventManagement\SubscriberInterface;

/**
 * Subscriber that registers plugin's admin page with WordPress.
 */
class AdminPageSubscriber implements SubscriberInterface
{
	/**
	 * The admin page.
	 *
	 * @var AdminPage
	 */
	protected AdminPage $page;
	/**
	 * Basename of the plugin
	 *
	 * @var string
	 */
	protected string $plugin_basename;
	
	/**
	 * Constructor
	 *
	 * @param AdminPage $page
	 * @param string    $plugin_basename
	 */
	public function __construct( AdminPage $page, string $plugin_basename )
	{
		$this->page = $page;
		$this->plugin_basename = $plugin_basename;
	}
	
	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public static function getSubscribedEvents(): array
	{
		return array(
			'admin_init'          => 'configure',
			'admin_menu'          => 'addAdminPage',
			'plugin_action_links' => array( 'addPluginPageLink', 10, 2 ),
		);
	}
	
	/**
	 * Adds the plugin's admin page to the options menu.
	 *
	 * Wrapper around add_submenu_page().
	 *
	 * @uses add_submenu_page()
	 */
	public function addAdminPage()
	{
		add_submenu_page(
			$this->page->getParentSlug(),
			$this->page->getPageTitle(),
			$this->page->getMenuTitle(),
			$this->page->getCapability(),
			$this->page->getSlug(),
			array( $this->page, 'renderAdminPage' )
		);
	}
	
	/**
	 * Adds link from plugins page to WazFrame admin page.
	 *
	 *
	 * @param array  $links
	 * @param string $file
	 *
	 * @return array
	 */
	public function addPluginPageLink( array $links, string $file ): array
	{
		if ( $file != $this->plugin_basename ) {
			return $links;
		}
		return $links;
	}
	
	/**
	 * Configure admin page using the settings API.
	 */
	public function configure()
	{
		$this->page->configure();
	}
}

```

Ultimately, the Subscriber classes are connecting to the ```EventManager``` class through the ```SubscriberInterface```
if all they need to do is register action and filter hooks, or ```EventManagerAwareInterface``` if they need to interact
with the Plugin API more dynamically (such as removing a filter and replacing it with a different one).

**The actual EventManager** has no knowledge of the existence of any other subscriber class directly, nor of any
dependencies. It simply handles all the possible interactions with the WordPress Plugin API.

The ```SubscriberInterface``` requires the ```getSubscribedEvents``` method.

```php 
public static function getSubscribedEvents(): array
{
	return array(
		'admin_init'          => 'configure',
		'admin_menu'          => 'addAdminPage',
		'plugin_action_links' => array( 'addPluginPageLink', 10, 2 ),
	);
}
```

The EventManager class will handle the actual registration of the hooks, there's a special class,
called ```EventManagerSubscriber```, which takes all of the Subscriber classes and depending on the interface they use,
allow the EventManager to either allow access to the class that needs Plugin API access, or simply register the hooks.

```php 
<?php

namespace WazFactor\WazFrame\Internal\EventManagement;

use WazFactor\WazFrame\Admin\AdminPageSubscriber;
use WazFactor\WazFrame\Vendor\League\Container\Container;
use WazFactor\WazFrame\Internal\Translation\TranslationSubscriber;
use WazFactor\WazFrame\Vendor\League\Container\Argument\Literal\ArrayArgument;
use WazFactor\WazFrame\Vendor\League\Container\ContainerAwareTrait;

class EventManagerSubscriber implements EventManagerAwareInterface
{
	use ContainerAwareTrait;
	
	/**
	 * The subscribers array.
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
		);
		
		$container->add( 'subscribers', new ArrayArgument( $subscribers ) );
	}
	
	public function addSubscribers( $container )
	{
		$subscribers = $container->get( 'subscribers' );
		
		foreach ( $subscribers as $subscriber ) {
			$this->event_manager->setSubscriber( $subscriber );
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
```

From there, the ```EventManagerServiceProvider``` handles all the class dependencies between the EventManager and the
other ```Subscriber``` classes that need to interact with one another (e.g. the AdminPage needing Translation access).

To add a new ```Subscriber``` class to the ```EventManagerSubscriber```, all you need to do is add a new entry into the
$subscribers array in the ```getSubscribers``` method, as shown below:

```php 
public function getSubscribers( $container )
{
		
	$subscribers = array(
		$container->get( AdminPageSubscriber::class ),
		$container->get( TranslationSubscriber::class ),
		// NEW SUBSCRIBER TO ADD
		$container->get( NewClassSubscriber::class ),
	);
		
	$container->add( 'subscribers', new ArrayArgument( $subscribers ) );
}
```

Ultimately, this functionality may change to be a bit more efficient as the number of subscribers grow, but for now this
is the most seamless way to properly register a subscriber.
