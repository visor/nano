<?php

/**
 * @group core
 */
class Core_DispatcherErrorsTest extends TestUtils_HttpTest {

	/**
	 * @var \Nano\Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		parent::setUp();

		$this->application->dispatcher->setResponse(new Nano_C_Response_Test($this->application));
		$this->dispatcher = $this->application->dispatcher;
	}

	public function testGetControllerShouldThrowWhenClassNotExists() {
		$this->setExpectedException('\Nano\Exception\NotFound', 'Controller class not found');

		$this->dispatcher->getController(new \Nano\Route\StaticLocation('test', 'std-class', 'index', null));
	}

	public function testGetControllerShouldThrowWhenNotControllerClassRequired() {
		$this->setExpectedException('\Nano\Exception\InternalError', 'Not a controller class: App\Controller\Invalid');

		require_once __DIR__ . '/_files/controllers/Invalid.php';
		$this->dispatcher->getController(\Nano\Route\Common::create('', 'invalid', 'test'));
	}

	public function testGetControllerShouldThrowWhenAbstractClassRequired() {
		$this->setExpectedException('\Nano\Exception\InternalError', 'Not a controller class: App\Controller\AbstractController');

		require_once __DIR__ . '/_files/controllers/AbstractController.php';
		$this->dispatcher->getController(\Nano\Route\Common::create('', 'abstract-controller', 'test'));
	}

	public function testDispatchShouldGenerateNotFoundErrorWhenNoRoutesMatched() {
		$this->sendGet('/page-not-found');

		self::assertEquals(Nano_C_Response::STATUS_NOT_FOUND, $this->request->getResponseCode());
		self::assertContains('Route not found for: page-not-found', $this->request->getResponseBody());
	}

	protected function tearDown() {
		unSet($this->dispatcher);
		parent::tearDown();
	}

}