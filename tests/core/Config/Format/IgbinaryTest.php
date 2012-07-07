<?php

require_once __DIR__ . '/TestAbstract.php';

/**
 * @group core
 * @group config
 */
class Core_Config_Format_IgbinaryTest extends Core_Config_Format_TestAbstract {

	/**
	 * @return \Nano\Application\Config\Format
	 */
	protected function getConfigInstance() {
		return new \Nano\Application\Config\Format\Igbinary();
	}

	/**
	 * @return string
	 */
	protected function getConfigName() {
		return 'igbinary';
	}

}