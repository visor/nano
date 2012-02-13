<?php

/**
 * @group core
 */
class Core_RenderTest extends TestUtils_TestCase {

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var Nano_Render
	 */
	protected $renderer;

	/**
	 * @var TestController
	 */
	protected $controller;

	protected function setUp() {
		require_once $this->files->get($this, '/controllers/TestController.php');
		$this->application = new Application();

		$this->application
			->withRootDir($GLOBALS['application']->rootDir)
			->withConfigurationFormat('php')
			->withModule('module1', $this->files->get($this, '/module1'))
			->withModule('module2', $this->files->get($this, '/module2'))
			->configure()
		;

		$this->renderer = new Nano_Render($this->application);
		$this->renderer->setViewsPath($this->files->get($this, '/views'));
		$this->renderer->setModuleViewsDirName('views/default');
		$this->renderer->setLayoutsPath($this->files->get($this, '/layouts'));

		$this->controller = new TestController(new Nano_Dispatcher($this->application));
	}

	public function testGettingApplicationViewPath() {
		self::assertEquals($this->files->get($this, '/views/controller/action.php'), $this->renderer->getViewFileName('controller', 'action'));
		self::assertEquals($this->files->get($this, '/views/controller/action.php'), $this->renderer->getViewFileName('controller', 'action', null, null));
		self::assertEquals($this->files->get($this, '/views/controller/action.rss.php'), $this->renderer->getViewFileName('controller', 'action', 'rss', null));

		$this->renderer->setViewsPath($this->files->get($this, '/views/themed'));
		self::assertEquals($this->files->get($this, '/views/themed/controller/action.php'), $this->renderer->getViewFileName('controller', 'action'));
		self::assertEquals($this->files->get($this, '/views/themed/controller/action.php'), $this->renderer->getViewFileName('controller', 'action', null, null));
		self::assertEquals($this->files->get($this, '/views/themed/controller/action.rss.php'), $this->renderer->getViewFileName('controller', 'action', 'rss', null));
	}

	public function testGettingModuleViewPath() {
		self::assertEquals($this->files->get($this, '/module1/views/default/controller/action.php'), $this->renderer->getViewFileName('controller', 'action', null, 'module1'));
		self::assertEquals($this->files->get($this, '/module1/views/default/controller/action.rss.php'), $this->renderer->getViewFileName('controller', 'action', 'rss', 'module1'));
		self::assertEquals($this->files->get($this, '/module2/views/default/controller/action.php'), $this->renderer->getViewFileName('controller', 'action', null, 'module2'));

		$this->renderer->setModuleViewsDirName('views/theme1');
		self::assertEquals($this->files->get($this, '/module1/views/theme1/controller/action.php'), $this->renderer->getViewFileName('controller', 'action', null, 'module1'));
		self::assertEquals($this->files->get($this, '/module1/views/theme1/controller/action.rss.php'), $this->renderer->getViewFileName('controller', 'action', 'rss', 'module1'));
		self::assertEquals($this->files->get($this, '/module2/views/theme1/controller/action.php'), $this->renderer->getViewFileName('controller', 'action', null, 'module2'));
	}

	public function testGettingLayoutPath() {
		self::assertEquals($this->files->get($this, '/layouts/name.php'), $this->renderer->getLayoutFileName('name'));
		self::assertEquals($this->files->get($this, '/layouts/name.php'), $this->renderer->getLayoutFileName('name', null));
		self::assertEquals($this->files->get($this, '/layouts/name.rss.php'), $this->renderer->getLayoutFileName('name', 'rss'));
	}

	public function testRenderShouldThrowExceptionWhenViewNotFound() {
		$this->setExpectedException('Nano_Exception', 'View ' . $this->renderer->getViewFileName('test', 'test2') . ' not exists');

		$this->controller->layout     = null;
		$this->controller->controller = 'test';
		$this->controller->template   = 'test2';
		$this->renderer->render($this->controller);
	}

	public function testRenderShouldThrowExceptionWhenInView() {
		$this->setExpectedException('Application_Exception', 'Exception from view');

		$this->controller->layout     = null;
		$this->controller->controller = 'test';
		$this->controller->template   = 'exception';
		$this->renderer->render($this->controller);
	}

	public function testRenderShouldThrowExceptionWhenLayoutNotFound() {
		$this->setExpectedException('Nano_Exception', 'View ' . $this->renderer->getLayoutFileName('test2') . ' not exists');

		$this->controller->layout     = 'test2';
		$this->controller->controller = 'test';
		$this->controller->template   = 'test';
		$this->renderer->render($this->controller);
	}

	public function testRenderingSimpleView() {
		$this->controller->layout     = null;
		$this->controller->controller = 'test';
		$this->controller->template   = 'test';

		$this->assertEquals('test view rendered', $this->renderer->render($this->controller));
	}

	public function testRenderingViewWithVariables() {
		$this->controller->layout     = null;
		$this->controller->controller = 'test';
		$this->controller->template   = 'test-var';
		$this->controller->title      = 'Some title';
		$this->controller->array      = array('01' => 'foo', '03' => 'bar');

		$this->assertEquals('Some title. 01=foo.03=bar.', $this->renderer->render($this->controller));
	}

	public function testRenderingViewWithLayout() {
		$this->controller->layout     = 'test';
		$this->controller->controller = 'test';
		$this->controller->template   = 'test';

		$this->assertEquals('layout with content {test view rendered} rendered.', $this->renderer->render($this->controller));
	}

	public function testRenderingViewWithContext() {
		$this->controller->layout      = null;
		$this->controller->controller  = 'test';
		$this->controller->template    = 'index';
		$this->controller->context     = 'rss';
		$this->controller->rssVariable = 'RSS test';

		$this->assertEquals('RSS test', $this->renderer->render($this->controller));

		$this->controller->layout = 'test';
		$this->assertEquals('{RSS test}', $this->renderer->render($this->controller));
	}

	protected function tearDown() {
		unSet($this->controller, $this->renderer);
	}

}