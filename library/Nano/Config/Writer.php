<?php

interface Nano_Config_Writer {

	/**
	 * @return boolean
	 * @param string $fileName
	 * @param array $data
	 */
	public function write($fileName, array $data);

}