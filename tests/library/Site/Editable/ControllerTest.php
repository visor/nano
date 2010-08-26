<?php

/**
 * @group editable
 */
class Editable_ControllerTest extends TestUtils_ControllerTestCase {

	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	protected $controller = null;

	public static function setUpBeforeClass() {
		require_once __DIR__ . DS . '_files' . DS . 'TestEditable.php';
		TestEditable::dropTable();
		TestEditable::createTable();
	}

	public static function tearDownAfterClass() {
		TestEditable::dropTable();
	}

	public function testEditActionWithoutSavedData() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		$this->controller->expects($this->once())
			->method('getEditable')
			->will($this->returnValue(TestEditable::createNew('title')))
		;
		$this->controller->editAction();
		self::assertEquals('title', $this->controller->getForm()->title);
	}

	public function testEditActionWithSavedData() {
		$_REQUEST['title']      = 'some title';
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		Nano::helper()->request()->save();
		$this->controller->editAction();
		self::assertEquals($_REQUEST['title'], $this->controller->getForm()->title);
	}

	public function testSaveActionSuccess() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		$_REQUEST['title']      = 'some title';
		self::markTestIncomplete();
		//check in table
		//check no flash messages
	}

	public function testSaveActionFails() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		self::markTestIncomplete();
		//check in table
		//check no flash messages
	}

	public function testDeleteActionSuccess() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		self::markTestIncomplete();
		//check in table
	}

	public function testDeleteActionFails() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		self::markTestIncomplete();
		//check in table
	}

	protected function setUp() {
		Nano::db()->delete(TestEditable::NAME);
		$form = new Nano_Form(array('title'));
		$this->controller = $this->getMockForAbstractClass('Site_Editable_Controller', array(Nano::dispatcher()));
		$this->controller->expects($this->any())
			->method('getForm')
			->will($this->returnValue($form))
		;
	}

	protected function tearDown() {
		unset($this->controller);
		Nano::db()->delete(TestEditable::NAME);
	}

	private function createItems($n) {
		for ($i = 1; $i <= $n; ++$i) {
			TestEditable::createNew('title #' . sprintf('%05d', $i))->save();
		}
	}

}