<?php

/**
 * @group core
 * @group cli
 */
class Core_Cli_ScriptInfoTest extends TestUtils_TestCase {

	/**
	 * @var string
	 */
	protected $cwd, $appRoot;

	/**
	 * @var Nano_Cli
	 */
	protected $cli;

	protected function setUp() {
		$this->app->backup();
		ob_start();
		$this->setUseOutputBuffering(true);

		$this->appRoot  = dirName(__DIR__) . '/Application/_files';
		$this->cwd      = getCwd();
		$this->cli      = new Nano_Cli();
		chDir($this->appRoot);

		$this->cli->run(array());
	}

	public function testGettingDescription() {
		self::assertEquals('Test script to use into test cases @ @', $this->getScript('test-script')->getDescription());
		self::assertEquals('', $this->getScript('no-description')->getDescription());
	}

	public function testGettingName() {
		self::assertEquals('test-script', $this->getScript('test-script')->getName());
		self::assertEquals('no-description', $this->getScript('no-description')->getName());
	}

	public function testGettingApplication() {
		self::assertSame($this->cli->getApplication(), $this->getScript('test-script')->getApplication());
		self::assertSame($this->cli->getApplication(), $this->getScript('no-description')->getApplication());
	}

	public function testGettingScriptUsage() {
		$testScript     = $this->getScript('test-script');
		$noDescription = $this->getScript('no-description');
		self::assertContains($testScript->getName(), $testScript->usage());
		self::assertContains($testScript->getDescription(), $testScript->usage());

		self::assertContains($noDescription->getName(), $noDescription->usage());
		self::assertContains('$testParam', $noDescription->usage());
		self::assertContains('$optionalParam', $noDescription->usage());
	}

	public function testStopUsage() {
		Nano::setApplication(null);
		self::assertEquals(200, $this->cli->run(array('no-description')));
		self::assertContains('[script stop message]', $this->getActualOutput());
	}

	/**
	 * @return Nano_Cli_Script
	 * @param $name
	 */
	protected function getScript($name) {
		$result = $this->cli->getScript($name);
		return $result->newInstance($name, $this->cli);
	}

	protected function tearDown() {
		chDir($this->cwd);
		ob_end_clean();
		$this->app->restore();
	}

}