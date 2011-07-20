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
	 * @param string $sourceFileName
	 * @param string $destinationFileName
	 */
	public function merge($sourceFileName, $destinationFileName) {
	}

}