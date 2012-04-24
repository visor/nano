<?php

/**
 * @group core
 */
class Core_C_RedirectTest extends TestUtils_TestCase {

	/**
	 * @var Nano_C_Redirect
	 */
	protected $redirect;

	protected function setUp() {
		$this->app->backup();
		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir(dirName(dirName(dirName(__DIR__))) . DS . 'application-example')
			->configure()
		;
		$this->redirect = new Nano_C_Redirect(new Nano_C_Response($application));
	}

	public function testSettingResponseLocation() {
		$this->redirect->to('/some/location');
		self::assertEquals('/some/location', $this->getResponse()->getHeader('Location'));
		self::assertEquals(302, $this->getResponse()->getStatus());
	}

	public function testPermanentRedirectShouldHas301Status() {
		$this->redirect->to('/some/location')->permanent();
		self::assertEquals('/some/location', $this->getResponse()->getHeader('Location'));
		self::assertEquals(301, $this->getResponse()->getStatus());
	}

	public function testHomeRedirectShouldHasRootLocation() {
		$this->redirect->home();
		self::assertEquals('/', $this->getResponse()->getHeader('Location'));
		self::assertEquals(302, $this->getResponse()->getStatus());
	}

	public function testBackRedirectShouldHasRootLocationIfNoReferer() {
		unSet($_SERVER['HTTP_REFERER']);
		$this->redirect->back();

		self::assertEquals('/', $this->getResponse()->getHeader('Location'));
		self::assertEquals(302, $this->getResponse()->getStatus());
	}

	public function testBackRedirectShouldHasRefererLocationIfExistsAndSameDomain() {
		$_SERVER['HTTP_REFERER'] = 'http://example.com/foo';
		$_SERVER['HTTP_HOST']  = 'example.com';
		$this->redirect->back();

		self::assertEquals($_SERVER['HTTP_REFERER'], $this->getResponse()->getHeader('Location'));
		self::assertEquals(302, $this->getResponse()->getStatus());
	}

	public function testBackRedirectShouldHasRootLocationIfRefererFromAnotherDomain() {
		$_SERVER['HTTP_REFERER'] = 'http://example.com/foo';
		unSet($_SERVER['HTTP_HOST']);
		$this->redirect->back();

		self::assertEquals('/', $this->getResponse()->getHeader('Location'));
		self::assertEquals(302, $this->getResponse()->getStatus());
	}

	public function testControllerHelperShouldCreateRedirectInstance() {
		$controller = new ResponseTestController();
		$controller->setResponse(new Nano_C_Response_Test());

		self::assertInstanceOf('Nano_C_Redirect', $controller->redirect());
	}

	public function testControllerHelperShouldAddLocationWhenParameterPassed() {
		$controller = new ResponseTestController();
		$controller->setResponse(new Nano_C_Response_Test());
		$controller->redirect('/some/location');

		self::assertTrue($controller->response()->hasHeader('Location'));
		self::assertEquals('/some/location', $controller->response()->getHeader('Location'));
	}

	public function testControllerHelperShouldMarkControlerRendered() {
		$controller = new ResponseTestController();
		$controller->setResponse(new Nano_C_Response_Test());
		$controller->redirect();
		self::assertTrue(self::getObjectProperty($controller, 'rendered'));
	}

	/**
	 * @return Nano_C_Response
	 */
	protected function getResponse() {
		return self::getObjectProperty($this->redirect, 'response');
	}

	protected function tearDown() {
		unSet($this->redirect);
		$this->app->restore();
	}

}