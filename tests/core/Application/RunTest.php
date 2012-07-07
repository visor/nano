<?php

require_once __DIR__ . '/Abstract.php';

/**
 * @group core
 */
class Core_Application_RunTest extends Core_Application_Abstract {

	/**
	 * @var \Nano\Controller\Response
	 */
	protected $response;

	protected function setUp() {
		parent::setUp();

		$this->application
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
		;

		rename(
			$this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::CONFIG_FILE_NAME
			, $this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::CONFIG_FILE_NAME . '.bak'
		);
		rename(
			$this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::ROUTES_FILE_NAME
			, $this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::ROUTES_FILE_NAME . '.bak'
		);

		$builder = new \Nano\Application\Config\Builder($this->application);
		$builder->setSource($this->application->rootDir . DIRECTORY_SEPARATOR . 'settings');
		$builder->setDestination($this->application->rootDir . DIRECTORY_SEPARATOR . 'settings');
		$builder->build('for-test');

		$this->application->configure();

		$this->response = new \Nano\Controller\Response\Test($this->application);
		$this->application->dispatcher->setResponse($this->response);
	}

	public function testRunApplication() {
		$_SERVER['REQUEST_URI'] = '/some/prefix/response/set-body/custom.php?some-params';
		$this->application->start();
		self::assertTrue($this->response->hasBody());
		self::assertEquals('foobar', $this->response->getBody());
	}

	protected function tearDown() {
		rename(
			$this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::CONFIG_FILE_NAME . '.bak'
			, $this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::CONFIG_FILE_NAME
		);
		rename(
			$this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::ROUTES_FILE_NAME . '.bak'
			, $this->application->rootDir . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . \Nano\Application\Config::ROUTES_FILE_NAME
		);

		parent::tearDown();
	}

}
