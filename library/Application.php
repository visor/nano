<?php

namespace Nano;

require __DIR__ . '/Nano.php';
require __DIR__ . '/Names.php';
require __DIR__ . '/Loader.php';
require __DIR__ . '/Util/TypedRegistry.php';
require __DIR__ . '/Util/Classes.php';
require __DIR__ . '/Application/ErrorHandler.php';

/**
 * @property string $rootDir
 * @property string $publicDir
 * @property string $modulesDir
 * @property string $sharedModulesDir
 * @property string $nanoRootDir
 *
 * @property \Nano\Application\Config\Format $configFormat
 * @property \Nano\Application\Config $config
 * @property \Nano\Loader $loader
 * @property \Nano\Application\Dispatcher $dispatcher
 * @property \Nano\Application\Modules $modules
 * @property \SplObjectStorage $plugins
 * @property \Nano\Event\Manager $eventManager
 * @property \Nano\HelperBroker $helper
 * @property \Nano\L10n\Locale $locale
 */
class Application extends Util\TypedRegistry {

	const PUBLIC_DIR_NAME     = 'public';
	const MODULES_DIR_NAME    = 'modules';
	const CONTROLLER_DIR_NAME = 'controllers';
	const LIBRARY_DIR_NAME    = 'library';
	const MODELS_DIR_NAME     = 'models';
	const HELPERS_DIR_NAME    = 'helpers';
	const PLUGINS_DIR_NAME    = 'plugins';

	/**
	 * @var Application\ErrorHandler;
	 */
	protected $errorHandler;

	/**
	 * @return \Nano\Application
	 */
	public static function create() {
		return new static();
	}

	public function __construct() {
		parent::__construct();

		$this
			->readOnly('configFormat')
			->readOnly('nanoRootDir', dirName(__DIR__))
			->readOnly('loader',       new Loader)
			->readOnly('modules',      new Application\Modules)
			->readOnly('plugins',      new \SplObjectStorage)
			->readOnly('helper',       new HelperBroker)
			->readOnly('dispatcher',   new Application\Dispatcher)
			->readOnly('eventManager', new Event\Manager)
			->readOnly('rootDir')
			->readOnly('publicDir')
			->readOnly('modulesDir')
			->readOnly('sharedModulesDir')

			->ensure('configFormat', 'Nano\Application\Config\Format')
			->ensure('config',       'Nano\Application\Config')
			->ensure('modules',      'Nano\Application\Modules')
			->ensure('dispatcher',   'Nano\Application\Dispatcher')
			->ensure('helper',       'Nano\HelperBroker')
			->ensure('eventManager', 'Nano\Event\Manager')
			->ensure('plugins',      'SplObjectStorage')
		;
	}

	/**
	 * @return \Nano\Application
	 *
	 * @throws \Nano\Application\Exception\InvalidConfiguration
	 */
	public function configure() {
		if (!$this->offsetExists('configFormat')) {
			throw new Application\Exception\InvalidConfiguration('Configuration format not specified');
		}

		\Nano::setApplication($this);

		if (!$this->offsetExists('rootDir')) {
			$this->withRootDir(getCwd());
		}
		if (!$this->offsetExists('publicDir')) {
			$this->withPublicDir($this->rootDir . DIRECTORY_SEPARATOR . self::PUBLIC_DIR_NAME);
		}
		if (!$this->offsetExists('modulesDir')) {
			$this->withModulesDir($this->rootDir . DIRECTORY_SEPARATOR . self::MODULES_DIR_NAME);
		}
		if (!$this->offsetExists('sharedModulesDir')) {
			$this->withSharedModulesDir($this->nanoRootDir . DIRECTORY_SEPARATOR . self::MODULES_DIR_NAME);
		}

		if ('cli' !== php_sapi_name()) {
			$this->errorHandler = new Application\ErrorHandler;
		}

		$this->readOnly('config', new Application\Config($this->rootDir . DIRECTORY_SEPARATOR . 'settings', $this->configFormat));

		if (isSet($this->config->get('web')->locale)) {
			$this->readOnly('locale', new \Nano\L10n\Locale($this->config->get('web')->locale));
		}

		$this->setupErrorReporting();
		return $this;
	}

	/**
	 * @return \Nano\Application
	 * @param string $value
	 */
	public function withConfigurationFormat($value) {
		$this->offsetSet('configFormat', Application\Config::format($value));
		return $this;
	}

	/**
	 * @return \Nano\Application
	 * @param string $value
	 */
	public function withRootDir($value) {
		$this->offsetSet('rootDir', $value);
		return $this;
	}

	/**
	 * @return \Nano\Application
	 * @param string $value
	 */
	public function withPublicDir($value) {
		$this->offsetSet('publicDir', $value);
		return $this;
	}

	/**
	 * @return \Nano\Application
	 * @param string $value
	 */
	public function withModulesDir($value) {
		$this->offsetSet('modulesDir', $value);
		return $this;
	}

	/**
	 * @return \Nano\Application
	 * @param string $value
	 */
	public function withSharedModulesDir($value) {
		$this->offsetSet('sharedModulesDir', $value);
		return $this;
	}

	/**
	 * @return \Nano\Application
	 *
	 * @param string $name
	 * @param string $path
	 *
	 * @throws \Nano\Application\Exception\ModuleNotFound
	 */
	public function withModule($name, $path = null) {
		if (null === $path) {
			if ($this->offsetExists('sharedModulesDir') && is_dir($this->sharedModulesDir . DIRECTORY_SEPARATOR . $name)) {
				$path = $this->sharedModulesDir . DIRECTORY_SEPARATOR . $name;
			} elseif ($this->offsetExists('modulesDir') && is_dir($this->modulesDir . DIRECTORY_SEPARATOR . $name)) {
				$path = $this->modulesDir . DIRECTORY_SEPARATOR . $name;
			} else {
				throw new Application\Exception\ModuleNotFound($name);
			}
		}

		$this->modules->append($name, $path);
		return $this;
	}

	/**
	 * @return \Nano\Application
	 * @param \Nano\Controller\Plugin $value
	 */
	public function withPlugin(\Nano\Controller\Plugin $value) {
		$this->plugins->attach($value);
		return $this;
	}

	public function start() {
		$urlPrefix = $this->config->get('web')->url;
		$url       = $_SERVER['REQUEST_URI'];
		if (false !== strPos($url, '?')) {
			$url = subStr($url, 0, strPos($url, '?'));
		}
		if ($urlPrefix && 0 === strPos($url, $urlPrefix)) {
			$url = subStr($url, strLen($urlPrefix));
		}
		if ($this->config->get('web')->index) {
			$url = preg_replace('/' . preg_quote($this->config->get('web')->index) . '$/', '', $url);
		}
		$url = trim(rawUrlDecode($url), '/');
		$this->dispatcher->dispatch($this->config->routes(), $url);
	}

	public function errorHandler() {
		return $this->errorHandler;
	}

	protected function setupErrorReporting() {
//		if ($this->config->exists('web') && isSet($this->config->get('web')->errorReporting) && true === $this->config->get('web')->errorReporting) {
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', true);
//		} else {
//			error_reporting(0);
//			ini_set('display_errors', false);
//		}
	}

}