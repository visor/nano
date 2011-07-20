<?php

class Nano_Config_Format_Php implements Nano_Config_Format {

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
	 * @param string $sourceFileName
	 * @param string $destinationFileName
	 */
	public function merge($sourceFileName, $destinationFileName) {
	}

}