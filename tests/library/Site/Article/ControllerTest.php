<?php

/**
 * @group articles
 */
class Article_ControllerTest extends TestUtils_ControllerTestCase {

	/**
	 * @var TestArticleController
	 */
	private $controller = null;

	public static function setUpBeforeClass() {
		require_once __DIR__ . DS . '_files' . DS . 'TestArticle.php';
		require_once __DIR__ . DS . '_files' . DS . 'TestArticleController.php';
		TestArticle::dropTable();
		TestArticle::createTable();
	}

	public static function tearDownAfterClass() {
		TestArticle::dropTable();
	}

	public function testIndexAction() {
		self::assertNull($this->invokeControllerAction($this->controller, 'index'));
		self::assertType('PagerHelper', $this->controller->pager);
		self::assertType('PDOStatement', $this->controller->items);
		self::assertEquals(1, $this->controller->pager->getCurrentPage());
		self::assertEquals(0, $this->controller->pager->getTotalPages());
		self::assertEquals(Site_Article_Controller::ITEMS_PER_PAGE, $this->controller->pager->getLimit());
		self::assertEquals(0, $this->controller->pager->getOffset());

		$this->createItems(2 * Site_Article_Controller::ITEMS_PER_PAGE + 1);

		self::assertNull($this->invokeControllerAction($this->controller, 'index'));
		self::assertType('PagerHelper', $this->controller->pager);
		self::assertType('PDOStatement', $this->controller->items);
		self::assertEquals(1, $this->controller->pager->getCurrentPage());
		self::assertEquals(3, $this->controller->pager->getTotalPages());
		self::assertEquals(Site_Article_Controller::ITEMS_PER_PAGE, $this->controller->pager->getLimit());
		self::assertEquals(0, $this->controller->pager->getOffset());

		$_REQUEST['page'] = 2;
		self::assertNull($this->invokeControllerAction($this->controller, 'index'));
		self::assertType('PagerHelper', $this->controller->pager);
		self::assertType('PDOStatement', $this->controller->items);
		self::assertEquals(2, $this->controller->pager->getCurrentPage());
		self::assertEquals(3, $this->controller->pager->getTotalPages());
		self::assertEquals(Site_Article_Controller::ITEMS_PER_PAGE, $this->controller->pager->getLimit());
		self::assertEquals(Site_Article_Controller::ITEMS_PER_PAGE, $this->controller->pager->getOffset());
	}

	public function testPublishAction() {
		self::markTestIncomplete();
	}

	public function testUnpublishAction() {
		self::markTestIncomplete();
	}

	protected function setUp() {
		Nano::db()->delete(TestArticle::NAME);
		$this->controller = new TestArticleController(Nano::dispatcher());
		$this->controller->markRendered();
	}

	protected function tearDown() {
		unset($this->controller);
		Nano::db()->delete(TestArticle::NAME);
	}

	private function createItems($n) {
		for ($i = 1; $i <= $n; ++$i) {
			$text = sprintf('%05d', $i);
			$item = TestArticle::createNew('title ' . $text, 'body ' . $text);
			$date = Date::create('+' . $i . 'days');
			$item->publish($date);
			$item->save();
		}
	}

}