<?php

namespace Nano\Application\Config\Format;

class Json implements \Nano\Application\Config\Format {

	/**
	 * @return boolean
	 */
	public function available() {
		return function_exists('json_encode');
	}

	/**
	 * @return stdClass
	 * @param string $fileName
	 */
	public function read($fileName) {
		$result = file_get_contents($fileName);
		$result = json_decode($result);
		return $result;
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
		$source = json_encode($data);
		file_put_contents($fileName, $source);
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