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
		$application = new Application();
		$application
			->withConfigurationFormat('php')
			->withRootDir(dirName(dirName(dirName(__DIR__))) . DS . 'application-example')
			->configure()
		;
		$application->message->load('test');
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

	public function testWithMessageAsText() {
		$this->redirect->withMessage('simple text');
		self::assertEquals('simple text', Nano_C_Redirect::getMessage());
	}

	public function testWithMessageAsId() {
		$this->redirect->withMessage('m1', true);
		self::assertEquals('message 1', Nano_C_Redirect::getMessage());
	}

	public function testWithMessageAsIdAndParams() {
		$this->redirect->withMessage('f1', array(1, 'foo'));
		self::assertEquals('format 01 foo', Nano_C_Redirect::getMessage());
	}

	public function testGetMessageShouldReturnNullWhenoNotExists() {
		unSet($_SESSION[Nano_C_Redirect::PARAM_MESSAGE]);
		self::assertNull(Nano_C_Redirect::getMessage());
	}

	protected function tearDown() {
		unSet($this->redirect);
	}

	/**
	 * @return Nano_C_Response
	 */
	protected function getResponse() {
		return self::getObjectProperty($this->redirect, 'response');
	}

}