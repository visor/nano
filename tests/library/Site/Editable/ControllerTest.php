<?php

/**
 * @group editable
 * @group framework
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
		self::assertEquals(0, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));

		$this->controller->expects($this->once())
			->method('getEditable')
			->will($this->returnValue(new TestEditable(null, true)))
		;
		$this->controller->expects($this->once())
			->method('getData')
			->will($this->returnValue(array('title' => 'title')))
		;
		$this->controller->saveAction();

		self::assertEquals(1, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
		$row = Nano::db()->getRow('select * from ' . TestEditable::NAME);
		self::assertObjectHasAttribute('title', $row);
		self::assertEquals('title', $row->title);
		//check in table
	}

	public function testSaveActionFails() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		self::assertEquals(0, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
		$this->controller->getForm()->addValidator('title', new Nano_Validator_False);
		$this->controller->expects($this->once())
			->method('getData')
			->will($this->returnValue(array()))
		;
		$this->controller->saveAction();
		self::assertEquals(0, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
		//check in table
	}

	public function testDeleteActionSuccess() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';
		$editable = new TestEditable(null, true);
		$editable->title = 'some title';
		$editable->save();

		self::assertEquals(1, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
		$this->controller->expects($this->any())
			->method('getEditable')
			->will($this->returnValue(new TestEditable($editable->id)))
		;
		$this->controller->deleteAction();
		self::assertEquals(0, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
	}

	public function testDeleteActionFails() {
		$_SERVER['REQUEST_URI'] = 'http://example.com';

		$editable = $this->getMock('TestEditable', array('delete'), array(null, true));
		$editable->title = 'some title';
		$editable->save();
		$editable->expects($this->any())
			->method('delete')
			->will($this->returnValue(false))
		;

		self::assertEquals(1, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
		$this->controller->expects($this->any())
			->method('getEditable')
			->will($this->returnValue($editable))
		;
		$this->controller->deleteAction();
		self::assertEquals(1, Nano::db()->getCell('select count(*) from ' . TestEditable::NAME));
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