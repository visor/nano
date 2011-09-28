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
		$this->mkDir('controllers');
		$this->mkDir('helpers');
		$this->mkDir('layouts');
		$this->mkDir('messages');
		$this->mkDir('migrate');
		$this->mkDir('models');
		$this->mkDir('modules');
		$this->mkDir('plugins');
		$this->mkDir('public');
		$this->mkDir('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'styles');
		$this->mkDir('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'scripts');
		$this->mkDir('public' . DIRECTORY_SEPARATOR . 'images');
		$this->mkDir('resources' . DIRECTORY_SEPARATOR . 'scripts');
		$this->mkDir('resources' . DIRECTORY_SEPARATOR . 'styles');
		$this->mkDir('scripts');
		$this->mkDir('settings');
		$this->mkDir('views');
	}

	protected function createBootstrap() {
		$bootstrapPhp = <<<PHP
<?php

require_once 'Application.php';

Application::create()
	->usingConfigurationFormat('php')
	->withRootDir(__DIR__)
	->configure()
;
PHP;
		$indexPhp = <<<PHP
<?php

require './../bootstrap.php';

Application::current()->start();
PHP;

		echo '  Creating bootstrap.php';
		copy($this->defaults . DIRECTORY_SEPARATOR . 'bootstrap.php', $this->path . DIRECTORY_SEPARATOR . 'bootstrap.php');
		echo PHP_EOL;

		echo '  Creating public/index.php';
		copy($this->defaults . DIRECTORY_SEPARATOR . 'index.php', $this->path . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php');
		echo PHP_EOL;
	}

	protected function createDefaultConfiguration() {
		$this->mkDir('settings' . DIRECTORY_SEPARATOR . 'default');

		$path = $this->path . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
		echo '  Creating default routes';
		copy($this->defaults . DIRECTORY_SEPARATOR . 'routes.php', $path . 'routes.php');
		echo PHP_EOL;

		echo '  Creating default configuration';
		copy($this->defaults . DIRECTORY_SEPARATOR . 'config-assets.php', $path . 'assets.php');
		copy($this->defaults . DIRECTORY_SEPARATOR . 'config-db.php', $path . 'db.php');
		copy($this->defaults . DIRECTORY_SEPARATOR . 'config-web.php', $path . 'web.php');
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