<?php

interface Nano_Config_Format {

	/**
	 * @return boolean
	 */
	public function available();

	/**
	 * @return stdClass
	 * @param string $fileName
	 */
	public function read($fileName);

	/**
	 * @return \Nano\Routes
	 * @param string $fileName
	 */
	public function readRoutes($fileName);

	/**
	 * @return boolean
	 * @param array $data
	 * @param string $fileName
	 */
	public function write(array $data, $fileName);

	/**
	 * @return boolean
	 * @param \Nano\Routes $routes
	 * @param string $fileName
	 */
	public function writeRoutes(\Nano\Routes $routes, $fileName);

}