<?php

require_once TESTS . '/core/Config/Format/TestAbstract.php';

/**
 * @group core
 * @group config
 */
class Core_Config_Format_PhpTest extends Core_Config_Format_TestAbstract {

	/**
	 * @return Nano_Config_Format
	 */
	protected function getConfigInstance() {
		return new Nano_Config_Format_Php();
	}

	/**
	 * @return string
	 */
	protected function getConfigName() {
		return 'format.php';
	}

}