<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 * @group core-application
 */
class Core_Application_StartupTest extends Core_Application_Abstract {

	/**
	 * @var Core_Application_FakePlugin
	 */
	protected $plugin;

	protected function setUp() {
		include_once $this->files->get($this, '/FakePlugin.php');
		parent::setUp();

		$this->plugin = new Core_Application_FakePlugin();
		$this->application
			->usingConfigurationFormat('php')
			->withRootDir($this->files->get($this, ''))
			->withPublicDir($this->files->get($this, '/config'))
			->withModulesDir($this->files->get($this, '/application-modules'))
			->withSharedModulesDir($this->files->get($this, '/shared-modules'))
			->withModule('module1')
			->withModule('module2')
			->withModule('module3')
			->withPlugin($this->plugin)
		;
	}

	public function testLoaderShouldUseApplicationConfiguration() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testDispatcherShouldUseApplicationConfiguration() {
		self::markTestIncomplete('Not implemented yet');
	}

}