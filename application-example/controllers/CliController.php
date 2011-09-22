<?php

class CliController extends Nano_C_Cli {

	/**
	 * Alias for cli.help
	 */
	public function indexAction() {
		$this->helpAction();
	}

	/**
	 * Displays list of existed
	 * cli controllers and actions
	 */
	public function helpAction() {
		foreach ($this->getCliControllers() as $controller => $class) {
			/**
			 * @var ReflectionClass $class
			 */
			$actions = $this->getControllerActions($class);
			foreach ($actions as $action => $description) {
				echo $controller, (self::DEFAULT_ACTION == $action ? '' : '.' . $action), ' - ', $description, PHP_EOL;
			}
		}
	}

	/**
	 * Creates new configuration based on specified parents
	 * @return void
	 */
	public function createConfigAction() {
		$usage = 'cli.php cli.create-config name parent1[ parent2[ parent3]]';
		if (0 == count($this->args)) {
			echo 'Please pass new configuration name and it\'s parent(s)', PHP_EOL, $usage, PHP_EOL;
			return;
		}
		if (1 == count($this->args)) {
			echo 'Please pass new configuration parent(s) or string NONE if no parents', PHP_EOL, $usage, PHP_EOL;
		}
		$name    = $this->args[0];
		$parents = $this->args;
		$new     = ROOT . DS . 'scripts' . DS . 'setup' . DS . $name;
		array_shift($parents);

		echo 'Creating new setup directory', PHP_EOL;
		echo "\t", $new, PHP_EOL;
		mkDir($new, 0755, true);

		if (in_array('NONE', $parents)) {
			echo "\t\t", 'no parents', PHP_EOL;
			$parents = array();
		} else {
			file_put_contents($new . DS . Nano_Config_Builder::PARENTS_FILE, '<?php return ' . var_export($parents, true) . ';');
			echo "\t\t", Nano_Config_Builder::PARENTS_FILE, PHP_EOL;
		}

		foreach ($parents as $parent) {
			$i = new DirectoryIterator(ROOT . DS . 'scripts' . DS . 'setup' . DS . $parent);
			foreach ($i as /** @var DirectoryIterator $file */$file) {
				if ($file->isDir() || $file->isDir() || !$file->isReadable()) {
					continue;
				}
				if (Nano_Config_Builder::PARENTS_FILE == $file->getBaseName()) {
					continue;
				}
				if ('php' !== pathInfo($file->getBaseName(), PATHINFO_EXTENSION)) {
					continue;
				}
				$newFile = $new . DS . $file->getBaseName();
				if (file_exists($newFile)) {
					continue;
				}

				file_put_contents($newFile, '<?php return array(' . PHP_EOL . ');');
				echo "\t\t", $file->getBaseName(), PHP_EOL;
			}
		}
		echo 'Done', PHP_EOL;
	}

	/**
	 * Compiles application settings
	 *
	 * @return void
	 */
	public function setupAction() {
		$usage = 'cli.php cli.setup name';
		if (0 == count($this->args)) {
			echo 'Please pass configuration name to setup', PHP_EOL, $usage, PHP_EOL;
			return;
		}

		$path    = getCwd();
		$builder = new Nano_Config_Builder();
		$builder->setSource($path . DIRECTORY_SEPARATOR . 'settings');
		$builder->setDestination($path . DIRECTORY_SEPARATOR . 'settings');
		$builder->clean();

		require $path . DIRECTORY_SEPARATOR . 'bootstrap.php';

		$builder->build($this->args[0]);
	}

	/**
	 * Creates a new module with default structure
	 * @return void
	 */
	public function moduleAction() {
		$usage = 'cli.php module-name [module-path]';
		if (0 === count($this->args)) {
			echo $usage, PHP_EOL;
			return;
		}

		$moduleName = $this->args[0];
		$modulePath = isSet($this->args[1]) ? $this->args[1] : MODULES;
		$rootFolder = $modulePath . DS . $moduleName;

		if (!file_exists($rootFolder)) {
			if (false === mkDir($rootFolder, 0755, true)) {
				echo 'ERROR: Cannot create module folder', PHP_EOL;
				return;
			}
		}
		$folders    = array(
			$rootFolder . DS . 'controllers'
			, $rootFolder . DS . 'helpers'
			, $rootFolder . DS . 'library'
			, $rootFolder . DS . 'models'
			, $rootFolder . DS . 'plugins'
			, $rootFolder . DS . 'views'
		);
		foreach ($folders as $folder) {
			if (file_exists($folder)) {
				continue;
			}
			echo 'Creating ', $moduleName, DS, baseName($folder), ' ';
			if (false === mkDir($folder, 0755, true)) {
				echo 'fails';
			} else {
				echo 'done';
			}
			echo PHP_EOL;
		}
	}

	/**
	 * @return array
	 */
	protected function getCliControllers() {
		$result = array('cli' => new ReflectionClass(__CLASS__));
//		$pathes = array(CONTROLLERS);
//		foreach ($this->dispatcher()->application()->getModules() as $path) {
//			$pathes[] = $path;
//		}
//		foreach ($pathes as $path) {
//			foreach (new DirectoryIterator($path) as $item) {
//				/**
//				 * @var DirectoryIterator $item
//				 */
//				if ($item->isDot() || $item->isDir()) {
//					continue;
//				}
//				if (0 === preg_match('/Controller.php$/', $item->getBaseName())) {
//					continue;
//				}
//				$name       = baseName($item->getBaseName('.php'));
//				$controller = $this->convert(subStr($name, 0, -10));
//				$class      = new ReflectionClass($name);
//				if (!$class->isSubclassOf('Nano_C_Cli')) {
//					continue;
//				}
//				$result[$controller] = $class;
//			}
//		}
		ksort($result);
		return $result;
	}

	/**
	 * @return array
	 */
	protected function getControllerActions(ReflectionClass $class) {
		$result = array();
		foreach ($class->getMethods() as $method) {
			/**
			 * @var ReflectionMethod $method
			 */
			if (!$method->isPublic() || $method->isStatic()) {
				continue;
			}
			if (0 === preg_match('/Action$/', $method->getName())) {
				continue;
			}
			$action = $this->convert(subStr($method->getName(), 0, -6));
			$result[$action] = $this->extractDescription($method->getDocComment());
		}
		ksort($result);
		return $result;
	}

	/**
	 * @return string
	 * @param string $name
	 */
	protected function convert($name) {
		return strToLower(preg_replace('/(.)([A-Z])/', '\\1-\\2', $name));
	}

	protected function extractDescription($comment) {
		$result = preg_replace('/((^\/\*\*\s*)|(\s*\*\/$))/m', '', $comment);
		$result = preg_replace('/(^\s*\*\s*)/m', '', $result);
		$result = preg_replace('/(\r?\n|\r)/', ' ', $result);
		$result = trim($result);
		return $result;
	}

}