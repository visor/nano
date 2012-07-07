<?php

namespace Nano\Application\Config\Format;

class Php implements \Nano\Application\Config\Format {

	/**
	 * @return boolean
	 */
	public function available() {
		return true;
	}

	/**
	 * @return stdClass
	 * @param string $fileName
	 */
	public function read($fileName) {
		return include($fileName);
	}

	/**
	 * @return \Nano\Routes
	 * @param string $fileName
	 */
	public function readRoutes($fileName) {
		$result = file_get_contents($fileName);
		$result = unSerialize($result);
		return $result;
	}

	/**
	 * @return boolean
	 * @param array $data
	 * @param string $fileName
	 */
	public function write(array $data, $fileName) {
		$source = var_export(json_decode(json_encode($data)), true);
		$source = str_replace('stdClass::__set_state(', '(object)(', $source);
		$source = preg_replace('/=>[\s\t\r\n]+\(object\)/', '=> (object)', $source);
		$source = preg_replace('/=>[\s\t\r\n]+array/', '=> array', $source);
		file_put_contents($fileName, '<?php return ' . $source . ';');
		return true;
	}

	/**
	 * @return boolean
	 * @param \Nano\Routes $routes
	 * @param string $fileName
	 */
	public function writeRoutes(\Nano\Routes $routes, $fileName) {
		$source = serialize($routes);
		file_put_contents($fileName, $source);
		return true;
	}

}