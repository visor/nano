<?php

class Nano_Cli {

	const DIR = 'scripts';

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
			$sciptName        = ' - ' . $name . str_repeat(' ', $this->maxLength - strLen($name) + 2);
			$sciptDescription = $this->alignDescription($this->getScriptToRun($name)->getDescription(), $this->maxLength + 7);
			echo $sciptName, ' - ', $sciptDescription, PHP_EOL;
		}
		echo PHP_EOL;
	}

	/**
	 * @return ArrayObject|ReflectionClass[]
	 */
	public function getScripts() {
		return $this->scripts;
	}

	/**
	 * @return ReflectionClass
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
			if (file_exists($dir . DIRECTORY_SEPARATOR . self::BOOTSTRAP)) {
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
		$application = null;
		if (false === include($this->applicationDir . DIRECTORY_SEPARATOR . self::BOOTSTRAP)) {
			return;
		}
		if ($application === null) {
			return;
		}
		if (!($application instanceof \Nano\Application)) {
			return;
		}

		$this->application = $application;
	}

	protected function loadScripts() {
		$this->loadNanoScripts();
		$this->loadApplicationScripts();
		$this->scripts->kSort();
		$this->loaded = true;
	}

	protected function loadNanoScripts() {
		$nanoRoot = dirName(dirName(__DIR__));
		$scriptsRoot = $nanoRoot . DIRECTORY_SEPARATOR . self::DIR;
		$this->loadScriptsFromDir($scriptsRoot);
	}

	protected function loadApplicationScripts() {
		if (null === $this->applicationDir || null === $this->application) {
			return;
		}

		if (is_dir($this->application->rootDir . DIRECTORY_SEPARATOR . self::DIR)) {
			$this->loadScriptsFromDir($this->application->rootDir . DIRECTORY_SEPARATOR . self::DIR);
		}
		foreach ($this->application->modules as $path) {
			if (is_dir($path . DIRECTORY_SEPARATOR . self::DIR)) {
				$this->loadScriptsFromDir($path . DIRECTORY_SEPARATOR . self::DIR);
			}
		}
	}

	protected function loadScriptsFromDir($path) {
		$iterator = new DirectoryIterator($path);
		foreach ($iterator as $item) {
			/** @var DirectoryIterator $item */
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
			$this->addScript($item->getPathName());
		}
		unSet($item, $iterator);
	}

	protected function addScript($fileName) {
		$name      = baseName($fileName, '.php');
		$className = 'CliScript\\' . Nano::stringToName($name);
		if (!class_exists($className, false)) {
			include_once $fileName;

			if (!class_exists($className, false)) {
				return;
			}
		}

		$script = new ReflectionClass($className);
		if (!$script->isSubclassOf('Nano_Cli_Script')) {
			return;
		}
		if (!$script->isInstantiable()) {
			return;
		}
		if ($this->maxLength < ($length = strLen($name))) {
			$this->maxLength = $length;
		}
		$this->scripts[$name] = $script;
	}

	/**
	 * @return Nano_Cli_Script|null
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