<?php


namespace WazFactor\WazFrame;


// Internal Dependencies
use WazFactor\WazFrame\Admin\{AdminPageServiceProvider, AdminPageSubscriber};
use WazFactor\WazFrame\DesignSystem\BlockSupport\BlockSupportServiceProvider;
use WazFactor\WazFrame\DesignSystem\BlockSupport\Layout\LayoutSupportSubscriber;
use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilderServiceProvider;
use WazFactor\WazFrame\Internal\EventManagement\{EventManagerServiceProvider, EventManagerSubscriber};
use WazFactor\WazFrame\Internal\Translation\TranslationServiceProvider;
use WazFactor\WazFrame\Vendor\League\Container\Container;
use WazFactor\WazFrame\Vendor\Psr\Container\ContainerInterface;

// External Dependencies


/**
 * Instantiates the Plugin in WordPress.
 *
 * //TODO Probably re-separate out the Container class into it's own file.
 */
class Plugin {
	/**
	 * Text Domain used for translating plugin strings.
	 *
	 * @var string
	 */
	const DOMAIN = 'wazframe';
	
	/**
	 * WazFrame plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.2.0';
	
	/**
	 * The plugin's dependency injection container.
	 *
	 * @var Container
	 */
	private Container $container;
	
	/**
	 * List of service provider classes
	 * to register.
	 *
	 * @var string[]
	 */
	private array $service_providers = array(
		EventManagerServiceProvider::class,
		TranslationServiceProvider::class,
		AdminPageServiceProvider::class,
		StyleBuilderServiceProvider::class,
		BlockSupportServiceProvider::class,
	);
	
	/**
	 * Flag that checks if plugin is loaded.
	 *
	 * @var bool
	 */
	private bool $loaded;
	
	/**
	 * Constructor.
	 *
	 * @param string    $file
	 */
	public function __construct ( string $file ) {
		$this->container = new Container();
		// set default `add` instances to shared &
		// Add ourselves as the shared instance of ContainerInterface.
		$this->container->defaultToShared()
		                ->add( ContainerInterface::class, $this );
		$this->setPluginVariables( $file );
		$this->loaded = false;
	}
	
	/**
	 * Set's 'global' plugin settings to the container object
	 * via factory function, enabling access across all
	 * Dependency Injection needs.
	 *
	 * @param string    $file
	 *
	 * @return void
	 */
	public function setPluginVariables ( string $file ) {
		$container = $this->container;
		$plugin_settings = array(
			'plugin_basename'      => plugin_basename( $file ),
			'plugin_domain'        => self::DOMAIN,
			'plugin_path'          => plugin_dir_path( $file ),
			'plugin_relative_path' => basename( plugin_dir_path( $file ) ),
			'plugin_url'           => plugin_dir_url( $file ),
			'plugin_version'       => self::VERSION,
			'template_path'        => plugin_dir_path( $file ) . 'templates',
			'translations_path'    => plugin_dir_path( $file ) . 'i18n/languages',
			//'subscribers'          => []
		);
		foreach ( $plugin_settings as $key => $setting ) {
			$key_name = $key;
			$callable = function () use ( $setting ) {
				return $setting;
			};
			$container->add( $key_name, $callable );
			// May not be necessary, since we resolve on ServiceProviders.
			$container->get( $key_name );
		}
	}
	
	/**
	 * Loads the plugin into WordPress.
	 *
	 */
	public function load () {
		if ( $this->isLoaded() ) {
			return;
		}
		$container = $this->container;
		// Add our service provider.
		foreach ( $this->service_providers as $service_provider ) {
			$container->addServiceProvider( new $service_provider );
		}
		/**
		 * Not all classes in the container are resolved by default,
		 * If they have not yet been resolved, we resolve them via
		 * `get` here.
		 *
		 * This will mostly be 'Subscriber' classes that interact
		 * primarily with the Event Manager, rather than being
		 * registered via ServiceProvider.
		 */
		$container->get( AdminPageSubscriber::class );
		$container->get( EventManagerSubscriber::class );
		$container->get( LayoutSupportSubscriber::class );
		$this->loaded = true;
	}
	
	/**
	 * Checks if the plugin is loaded.
	 *
	 * @return bool
	 */
	public function isLoaded (): bool {
		return $this->loaded;
	}
}