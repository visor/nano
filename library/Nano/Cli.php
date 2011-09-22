<?php

class Nano_Cli {

	const DIR       = 'scripts';
	const BOOTSTRAP = 'bootstrap.php';

	/**
	 * @var boolean
	 */
	protected $loaded = false;

	/**
	 * @var ReflectionClass[]
	 */
	protected $scripts;

	/**
	 * @var string
	 */
	protected $applicationDir;

	/**
	 * @var null
	 */
	protected $application = null;

	/**
	 * @return void
	 * @param string[] $args
	 */
	public static function main(array $args) {
		$cli = new self();
		$cli->run($args);
	}

	/**
	 * @return boolean
	 */
	public static function isWindows() {
		return '\\' == DIRECTORY_SEPARATOR;
	}

	/**
	 * @return string
	 */
	public static function getPhpBinary() {
//		if (self::isWindows()) {
//			return $_ENV['_'];
//		}
		return trim(`which php`);
	}

	/**
	 * @return string
	 */
	public static function getCliScriptPath() {
		return dirName(dirName(__DIR__)) . DIRECTORY_SEPARATOR . 'cli.php';
	}

	public function __construct() {
		$this->scripts        = new ArrayObject();
		$this->applicationDir = null;
	}

	public function run(array $args) {
		$this->detectApplicationDirectory();
		$this->loadScripts();

		if (0 === count($args)) {
			$this->help();
			exit(0);
		}

		$script = array_shift($args);
		$script = $this->getScriptToRun($script);
		$script->run($args);
	}

	public function help() {
		echo self::getPhpBinary() . ' ' . baseName(self::getCliScriptPath()) . ' [script [params]]', PHP_EOL, PHP_EOL;
		echo 'where script is one of: ', PHP_EOL;
		foreach ($this->scripts as $name => $script) {
			echo ' - ', $name, PHP_EOL;
		}
		echo PHP_EOL;
	}

	/**
	 * @return boolean
	 */
	protected function detectApplicationDirectory() {
		$dir   = getCwd();
		$found = false;
		do {
			if (file_exists($dir . DIRECTORY_SEPARATOR . self::BOOTSTRAP)) {
				$found = true;
				$this->applicationDir = $dir;
			} else {
				$dir = dirName($dir);
			}
		} while (!$found && strLen($dir) > 1);
	}

	protected function loadScripts() {
		$this->loadNanoScripts();
		$this->loadApplicationScripts();
		$this->loaded = true;
	}

	protected function loadNanoScripts() {
		$nanoRoot    = dirName(dirName(__DIR__));
		$scriptsRoot = $nanoRoot . DIRECTORY_SEPARATOR . self::DIR;
		$this->loadScriptsFromDir($scriptsRoot);
	}

	protected function loadApplicationScripts() {
		if (null === $this->applicationDir) {
			return;
		}

		include $this->applicationDir . DIRECTORY_SEPARATOR . self::BOOTSTRAP;
		if (!Application::current()) {
			return;
		}

		$this->loadScriptsFromDir(Application::current()->getRootDir() . DIRECTORY_SEPARATOR . self::DIR);
	}

	protected function loadScriptsFromDir($path) {
		$iterator = new DirectoryIterator($path);
		foreach ($iterator as $item) { /** @var DirectoryIterator $item */
			if ($item->isDir()) {
				continue;
			}
			$ext = pathInfo($item->getBaseName(), PATHINFO_EXTENSION);
			if ($ext !== 'php') {
				continue;
			}
			$this->addScript($item->getPathName());
		}
		unSet($item, $iterator);
	}

	protected function addScript($fileName) {
		$name      = baseName($fileName, '.php');
		$className = 'CliScript\\' . Nano::stringToName($name);
		include $fileName;
		if (!class_exists($className, false)) {
			return;
		}
		$script = new ReflectionClass($className);
		if (!$script->isSubclassOf('Nano_Cli_Script')) {
			return;
		}
		if (!$script->isInstantiable()) {
			return;
		}
		$this->scripts[$name] = $script;
	}

	/**
	 * @param $name
	 * @return Nano_Cli_Script
	 */
	protected function getScriptToRun($name) {
		$key = strToLower($name);
		if (!$this->scripts->offsetExists($key)) {
			$this->help('Script ' . $name . ' not found');
			exit(1);
		}

		$result = $this->scripts[$key]->newInstance($this);
		return $result;
	}

}