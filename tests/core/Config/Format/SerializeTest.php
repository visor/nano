<?php

require_once __DIR__ . '/TestAbstract.php';

/**
 * @group core
 * @group config
 */
class Core_Config_Format_SerializeTest extends Core_Config_Format_TestAbstract {

	/**
	 * @return \Nano\Application\Config\Format
	 */
	protected function getConfigInstance() {
		return new \Nano\Application\Config\Format\Serialize();
	}

	/**
	 * @return string
	 */
	protected function getConfigName() {
		return 'serialize';
	}

}