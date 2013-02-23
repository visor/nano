<?php

namespace Nano;

class Cli {

	const DIR           = 'scripts';
	const CLI_NAMESPACE = 'CliScript';
	const BOOTSTRAP     = 'bootstrap.php';

	/**
	 * @var boolean
	 */
	protected $loaded = false;

	/**
	 * @var \ReflectionClass[]
	 */
	protected $scripts;

	/**
	 * @var string
	 */
	protected $applicationDir;

	/**
	 * @var null|\Nano\Application
	 */
	protected $application = null;

	/**
	 * @var int
	 */
	protected $maxLength = 0;

	/**
	 * @return int
	 * @param string[] $args
	 */
	public static function main(array $args) {
		$cli = new self();
		return $cli->run($args);
	}

	/**
	 * @return boolean
	 */
	public static function isWindows() {
		return '\\' == DS;
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
		return dirName(__DIR__) . DS . 'cli.php';
	}

	public function __construct() {
		$this->scripts        = new \ArrayObject();
		$this->applicationDir = null;
	}

	/**
	 * @return int
	 * @param array $args
	 */
	public function run(array $args) {
		$this->detectApplicationDirectory();
		$this->loadScripts();

		if (0 === count($args)) {
			$this->help();
			return 0;
		}

		$name   = array_shift($args);
		$script = $this->getScriptToRun($name);
		if (null === $script) {
			echo 'Script ' . $name . ' not found', PHP_EOL;
			$this->help();
			return 1;
		}
		if ($script->needApplication() && null === $this->application) {
			echo 'This script should starts in application directory', PHP_EOL;
			$this->help();
			return 2;
		}
		return $script->run($args);
	}

	public function help() {
		echo self::getPhpBinary() . ' ' . baseName(self::getCliScriptPath()) . ' [script [params]]', PHP_EOL, PHP_EOL;
		echo 'where script is one of: ', PHP_EOL;
		foreach ($this->scripts as $name => $script) {
			$repeat           = $this->maxLength - strLen($name) + 2;
			$sciptName        = ' - ' . $name . str_repeat(' ', $repeat);
			$sciptDescription = $this->alignDescription($this->getScriptToRun($name)->getDescription(), $this->maxLength + 7);
			echo $sciptName, ' - ', $sciptDescription, PHP_EOL;
		}
		echo PHP_EOL;
	}

	/**
	 * @return \ArrayObject|\ReflectionClass[]
	 */
	public function getScripts() {
		return $this->scripts;
	}

	/**
	 * @return \ReflectionClass
	 * @param string $key
	 */
	public function getScript($key) {
		if (!$this->scripts->offsetExists($key)) {
			return null;
		}
		return $this->scripts[$key];
	}

	/**
	 * @return null|\Nano\Application
	 */
	public function getApplication() {
		return $this->application;
	}

	/**
	 * @return boolean
	 */
	protected function detectApplicationDirectory() {
		$dir   = getCwd();
		$found = false;
		do {
			if (file_exists($dir . DS . self::BOOTSTRAP)) {
				$found = true;
				$this->applicationDir = $dir;
			} else {
				$dir = dirName($dir);
			}
		} while (!$found && strLen($dir) > 1);
		if ($found) {
			$this->loadApplication();
		}
	}

	protected function loadApplication() {
		if (false === include($this->applicationDir . DS . self::BOOTSTRAP)) {
			return;
		}
		if (!(\Nano::app() instanceof \Nano\Application)) {
			return;
		}

		$this->application = \Nano::app();
	}

	protected function loadScripts() {
		$this->loadNanoScripts();
		$this->loadApplicationScripts();
		$this->scripts->kSort();
		$this->loaded = true;
	}

	protected function loadNanoScripts() {
		$scriptsRoot = dirName(__DIR__) . DS . self::DIR;
		$this->loadScriptsFromDir($scriptsRoot, true);
	}

	protected function loadApplicationScripts() {
		if (null === $this->applicationDir || null === $this->application) {
			return;
		}

		if (is_dir($this->application->rootDir . DS . self::DIR)) {
			$this->loadScriptsFromDir($this->application->rootDir . DS . self::DIR, false);
		}
		foreach ($this->application->modules as $module => $path) {
			if (is_dir($path . DS . self::DIR)) {
				$this->loadScriptsFromDir($path . DS . self::DIR, false, $module);
			}
		}
	}

	protected function loadScriptsFromDir($path, $nanoScript, $module = null) {
		$iterator = new \DirectoryIterator($path);
		foreach ($iterator as $item) {
			/** @var \DirectoryIterator $item */
			if ($item->isDir()) {
				continue;
			}
			$ext = pathInfo($item->getBaseName(), PATHINFO_EXTENSION);
			if ($ext !== 'php') {
				continue;
			}
			if (1 !== preg_match('/^[a-z\-]+$/', $item->getBaseName('.php'))) {
				continue;
			}
			$this->addScript($item->getPathName(), $nanoScript, $module);
		}
		unSet($item, $iterator);
	}

	protected function addScript($fileName, $nanoScript, $module = null) {
		$name = baseName($fileName, '.php');

		if ($nanoScript) {
			$prefix    = '';
			$className = self::CLI_NAMESPACE . '\\' . Names::common($name);
		} elseif (null === $module) {
			$prefix    = 'app.';
			$className = Names::applicationClass($name, self::CLI_NAMESPACE);
		} else {
			$prefix    = $module . '.';
			$className = Names::moduleClass($module, $name, self::CLI_NAMESPACE);
		}

		echo $className, PHP_EOL;
		if (!class_exists($className, false)) {
			include_once $fileName;

			if (!class_exists($className, false)) {
				return;
			}
		}

		$script = new \ReflectionClass($className);
		if (!$script->isSubclassOf('\Nano\Cli\Script')) {
			return;
		}
		if (!$script->isInstantiable()) {
			return;
		}

		$scriptName = $prefix . $name;
		if ($this->maxLength < ($length = strLen($scriptName))) {
			$this->maxLength = $length;
		}

		$this->scripts[$scriptName] = $script;
//		$this->scripts[$name] = $script;
	}

	/**
	 * @return \Nano\Cli\Script|null
	 * @param $name
	 */
	protected function getScriptToRun($name) {
		$key = strToLower($name);
		if (!$this->scripts->offsetExists($key)) {
			return null;
		}

		$result = $this->scripts[$key]->newInstance($key, $this);
		return $result;
	}

	/**
	 * @return string
	 * @param string $string
	 * @param int $length
	 */
	protected function alignDescription($string, $length) {
		return preg_replace('/(\r?\n|\r)/', '\\1' . str_repeat(' ', $length), $string);
	}

}