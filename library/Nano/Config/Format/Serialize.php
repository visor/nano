<?php

class Nano_Config_Format_Serialize implements Nano_Config_Format {

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
		$result = file_get_contents($fileName);
		$result = unSerialize($result);
		return $result;
	}

	/**
	 * @return Nano_Routes
	 * @param string $fileName
	 */
	public function readRoutes($fileName) {
		return $this->read($fileName);
	}

	/**
	 * @return boolean
	 * @param array $data
	 * @param string $fileName
	 */
	public function write(array $data, $fileName) {
		$source = serialize(json_decode(json_encode($data)));
		file_put_contents($fileName, $source);
		return true;
	}

	/**
	 * @return boolean
	 * @param Nano_Routes $routes
	 * @param string $fileName
	 */
	public function writeRoutes(Nano_Routes $routes, $fileName) {
		$source = serialize($routes);
		file_put_contents($fileName, $source);
		return true;
	}

}