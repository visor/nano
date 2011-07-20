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
	 * @return boolean
	 * @param array $data
	 * @param string $fileName
	 */
	public function write(array $data, $fileName);

	/**
	 * @return boolean
	 * @param string $sourceFileName
	 * @param string $destinationFileName
	 */
	public function merge($sourceFileName, $destinationFileName);

}