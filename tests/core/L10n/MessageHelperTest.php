<?php

class Core_L10n_MessageHelperTest extends \Nano\TestUtils\TestCase {

	protected function setUp() {
		$this->app->backup();

		\Nano\Application::create()
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;
	}

	public function testTranslateHelper() {
		//todo: check Nano::t
		self::markTestIncomplete('Not implemented yet');
	}

	public function testTranslateModuleHelper() {
		//todo: check Nano::tm
		self::markTestIncomplete('Not implemented yet');
	}

	protected function tearDown() {
		$this->app->restore();
	}

}