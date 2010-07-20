<?php

class TestUtils_ControllerTestCase extends TestUtils_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @var KomSindrom_ModelsFixture
	 */
	private $_modelsFixture = null;

	protected function setUp() {
		Nano_Db::clean();
		$this->fixture()->createAllTestData();
		$this->dispatcher = new Nano_Dispatcher();
	}

	protected function tearDown() {
		Nano_Db::clean();
		Nano_Db::close();
	}

	/**
	 * @return string
	 * @param string $controller
	 * @param string $action
	 */
	protected function runAction($controller, $action) {
		return $this->dispatcher->clean()->run(Nano_Route::create('', $controller, $action));
	}

	/**
	 * @return stdClass
	 * @param string $controller
	 * @param string $action
	 */
	protected function runJSONP($controller, $action) {
		return $this->extractJSONP($this->runAction($controller, $action));
	}

	/**
	 * @return stdClass
	 * @param string $html
	 */
	protected function extractJSONP($html) {
		$data = $html;
		$data = preg_replace('~^\s*<html>\s*<head>\s*<script type="text/javascript">\s*window\.name=\'~', '', $data);
		$data = preg_replace('~\';\s*</script>\s*</head>\s*<body>\s*</body>\s*</html>\s*$~', '', $data);
		return json_decode($data);
	}

	/**
	 * @return void
	 */
	protected function prepareRequestArray() {
		$_REQUEST = array_merge($_GET, $_POST, $_COOKIE, $_SESSION);
	}

	/**
	 * @return KomSindrom_ModelsFixture
	 */
	protected function fixture() {
		if (null === $this->_modelsFixture) {
			$this->_modelsFixture = $this->getMockForAbstractClass('KomSindrom_ModelsFixture');
		}
		return $this->_modelsFixture;
	}

}