<?php

class TestUtils_ControllerTestCase extends TestUtils_TestCase {

	/**
	 * @var Nano_Dispatcher
	 */
	protected $dispatcher;

	/**
	 * @return string
	 * @param string|Nano_C $controller
	 * @param string $action
	 */
	public function invokeControllerAction($controller, $action) {
		$instance = ($controller instanceof Nano_C) ? $controller : new $controller(Nano::dispatcher()); /* @var $instance Nano_C */
		return $instance->run($action);
	}

	/**
	 * @return string
	 * @param string $controller
	 * @param string $action
	 * @param string $pattern
	 * @param string $url
	 */
	public function runAction($controller, $action, $pattern = null, $url = null) {
		$this->dispatcher->clean();
		$route = Nano_Route::create($pattern, $controller, $action);
		if (null !== $url) {
			$this->dispatcher->test($route, $url);
		}
		return $this->dispatcher->run($route);
	}

	/**
	 * @return stdClass
	 * @param string $controller
	 * @param string $action
	 */
	public function runJSONP($controller, $action) {
		return $this->extractJSONP($this->runAction($controller, $action));
	}

	/**
	 * @return stdClass
	 * @param string $html
	 */
	public function extractJSONP($html) {
		$data = $html;
		$data = preg_replace('~^\s*<html>\s*<head>\s*<script type="text/javascript">\s*window\.name=\'~', '', $data);
		$data = preg_replace('~\';\s*</script>\s*</head>\s*<body>\s*</body>\s*</html>\s*$~', '', $data);
		return json_decode($data);
	}

	/**
	 * @return void
	 */
	public function prepareRequestArray() {
		$_REQUEST = array_merge($_GET, $_POST, $_COOKIE, $_SESSION);
	}

	protected function setUp() {
		Nano_Db::clean();
		$this->dispatcher = new Nano_Dispatcher();
	}

	protected function tearDown() {
		Nano_Db::clean();
		Nano_Db::close();
	}

}