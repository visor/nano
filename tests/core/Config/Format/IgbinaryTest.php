<?php

require_once TESTS . '/core/Config/Format/TestAbstract.php';

/**
 * @group framework
 * @group config
 */
class Core_Config_Format_IgbinaryTest extends Core_Config_Format_TestAbstract {

	/**
	 * @return Nano_Config_Format
	 */
	protected function getConfigInstance() {
		return new Nano_Config_Format_Igbinary();
	}

	/**
	 * @return string
	 */
	protected function getConfigName() {
		return 'igbinary';
	}

}