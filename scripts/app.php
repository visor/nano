<?php

namespace CliScript;

/**
 * @description Creates empty application directory structure
 *
 * @param optional $appDir Directory where to create application (default — current working directory)
 */
class App extends \Nano_Cli_Script {

	/**
	 * @var string
	 */
	protected $path, $defaults;

	/**
	 * @return boolean
	 */
	public function needApplication() {
		return false;
	}

	/**
	 * @return void
	 * @param string[] $args
	 */
	public function run(array $args) {
		$this->path = isSet($args[0]) ? $args[0] : getCwd();
		if (!file_exists($this->path)) {
			mkDir($this->path, 0755, true);
		}
		if (!is_dir($this->path)) {
			$this->stop($this->path . ' is not directory', 1);
		}
		if (!is_writable($this->path)) {
			$this->stop('Cannot write into directory ' . $this->path, 1);
		}

		echo 'Creating application skeleton in ' . $this->path, PHP_EOL;
		$this->defaults = __DIR__ . DIRECTORY_SEPARATOR . 'app';
		$this->createDirectoryStructure();
		$this->createBootstrap();
		$this->createDefaultConfiguration();
		echo 'Done.', PHP_EOL;
	}

	protected function createDirectoryStructure() {
		$this->mkDir(\Nano\Application::CONTROLLER_DIR_NAME);
		$this->mkDir(\Nano\Application::HELPERS_DIR_NAME);
		$this->mkDir(\Nano\Render::LAYOUT_DIR);
		$this->mkDir(\Nano\Application::LIBRARY_DIR_NAME);
		$this->mkDir('messages');
		$this->mkDir('migrate');
		$this->mkDir(\Nano\Application::MODELS_DIR_NAME);
		$this->mkDir(\Nano\Application::MODULES_DIR_NAME);
		$this->mkDir(\Nano\Application::PLUGINS_DIR_NAME);
		$this->mkDir(\Nano\Application::PUBLIC_DIR_NAME);
		$this->mkDir(\Nano\Application::PUBLIC_DIR_NAME . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'styles');
		$this->mkDir(\Nano\Application::PUBLIC_DIR_NAME . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'scripts');
		$this->mkDir(\Nano\Application::PUBLIC_DIR_NAME . DIRECTORY_SEPARATOR . 'images');
		$this->mkDir('resources' . DIRECTORY_SEPARATOR . 'scripts');
		$this->mkDir('resources' . DIRECTORY_SEPARATOR . 'styles');
		$this->mkDir('scripts');
		$this->mkDir('settings');
		$this->mkDir(\Nano\Render::VIEW_DIR);
	}

	protected function createBootstrap() {
		$bootstrapPhp = <<<PHP
<?php

require_once '\Nano\Application.php';

\$application = new \Nano\Application;
\$application
	->withConfigurationFormat('php')
	->withRootDir(__DIR__)
	// put your configuration here
	->configure()
;
PHP;
		$indexPhp = <<<PHP
<?php

require './../bootstrap.php';

/** @var \Nano\Application \$application */
\$application->start();
PHP;

		echo '  Creating bootstrap.php';
		file_put_contents($this->path . DIRECTORY_SEPARATOR . 'bootstrap.php', $bootstrapPhp);
		echo PHP_EOL;

		echo '  Creating public/index.php';
		file_put_contents($this->path . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php', $indexPhp);
		echo PHP_EOL;
	}

	protected function createDefaultConfiguration() {
		$this->mkDir('settings' . DIRECTORY_SEPARATOR . 'default');

		$path = $this->path . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
		echo '  Creating default routes';
		$routesPhp = <<<PHP
<?php
/** @var \$routes Nano_Routes */

\$routes
	->get('', 'index', 'index')
;
PHP;
		file_put_contents($path . 'routes.php', $routesPhp);
		echo PHP_EOL;

		echo '  Creating default configuration';
		$webPhp = <<<PHP
<?php return array(
	  'root'           => ROOT . DS . 'public'
	, 'url'            => ''
	, 'index'          => 'index.php'
	, 'domain'         => 'example.com'
	, 'public'         => true
	, 'error'          => null
	, 'errorReporting' => true
);
PHP;
		file_put_contents($path . 'web.php', $webPhp);
		echo PHP_EOL;
	}

	/**
	 * @param string $name
	 */
	protected function mkDir($name) {
		echo '  Creating ' . $name . ' folder';
		mkDir($this->path . DIRECTORY_SEPARATOR . $name, 0755, true);
		echo PHP_EOL;
	}

}