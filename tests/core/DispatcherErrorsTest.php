<?php

/**
 * @group core
 */
class Core_DispatcherErrorsTest extends TestUtils_HttpTest {

	/**
	 * @var Nano_Dispatcher
	 */
	private $dispatcher;

	protected function setUp() {
		parent::setUp();
		$this->application->dispatcher->setResponse(new Nano_C_Response_Test($this->application));
		$this->dispatcher = $this->application->dispatcher;
	}

	public function testGetControllerShouldThrowWhenClassNotExists() {
		$this->setExpectedException('Nano_Exception_NotFound', 'Controller class not found');

		$this->dispatcher->getController(new Nano_Route_Static('test', 'std-class', 'index', null));
	}

	public function testGetControllerShouldThrowWhenNotControllerClassRequired() {
		$this->setExpectedException('Nano_Exception_InternalError', 'Not a controller class: NotController');

		require_once __DIR__ . '/_files/controllers/NotController.php';
		$this->dispatcher->getController(Nano_Route_Abstract::create('', 'not', 'test'));
	}

	public function testGetControllerShouldThrowWhenAbstractClassRequired() {
		$this->setExpectedException('Nano_Exception_InternalError', 'Not a controller class: AbstractController');

		require_once __DIR__ . '/_files/controllers/AbstractController.php';
		$this->dispatcher->getController(Nano_Route_Abstract::create('', 'abstract', 'test'));
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