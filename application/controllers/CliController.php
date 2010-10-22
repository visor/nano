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
				echo sprintf('%s.%s - %s', $controller, $action, $description), PHP_EOL;
			}
		}
	}

	/**
	 * @return array
	 */
	protected function getCliControllers() {
		$result = array();
		$pathes = array(CONTROLLERS);
		foreach (Nano::modules() as $path) {
			$pathes[] = $path;
		}
		foreach ($pathes as $path) {
			foreach (new DirectoryIterator($path) as $item) {
				/**
				 * @var DirectoryIterator $item
				 */
				if ($item->isDot() || $item->isDir()) {
					continue;
				}
				if (0 === preg_match('/Controller.php$/', $item->getBaseName())) {
					continue;
				}
				$name       = baseName($item->getBaseName('.php'));
				$controller = $this->convert(subStr($name, 0, -10));
				$class      = new ReflectionClass($name);
				if (!$class->isSubclassOf('Nano_C_Cli')) {
					continue;
				}
				$result[$controller] = $class;
			}
		}
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
			if (!$method->isPublic()) {
				continue;
			}
			if ($method->isStatic()) {
				continue;
			}
			if (0 === preg_match('/Action$/', $method->getName())) {
				continue;
			}
			$action = $this->convert(subStr($method->getName(), 0, -6));
			$result[$action] = $this->extractDescription($method->getDocComment());
		}
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