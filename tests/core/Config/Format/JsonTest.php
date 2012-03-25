<?php

require_once __DIR__ . '/TestAbstract.php';

/**
 * @group core
 * @group config
 */
class Core_Config_Format_JsonTest extends Core_Config_Format_TestAbstract {

	/**
	 * @return Nano_Config_Format
	 */
	protected function getConfigInstance() {
		return new Nano_Config_Format_Json();
	}

	/**
	 * @return string
	 */
	protected function getConfigName() {
		return 'json';
	}

}