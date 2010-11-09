<?php

/**
 * @group framework
 */
class SitePagerSimpleTest extends TestUtils_TestCase {

	public function testGenerateEmptyList() {
		self::assertEquals(array(), self::getObjectProperty($this->createPager(1, 9), 'pages'));
	}

	public function testGenerateWithoutSpacesPages() {
		self::assertEquals(array(1, 2),       self::getObjectProperty($this->createPager(1,   15), 'pages'));
		self::assertEquals(array(1, 2, 3),    self::getObjectProperty($this->createPager(1,   25), 'pages'));
		self::assertEquals(array(1, 2, 3, 4), self::getObjectProperty($this->createPager(1,   35), 'pages'));
		for ($i = 1; $i <= 5; ++$i) {
			self::assertEquals(array(1, 2, 3, 4, 5), self::getObjectProperty($this->createPager($i, 45), 'pages'));
		}
	}

	public function testGeneratePages() {
		self::assertEquals(array(1, 2, 3, null, 10),             self::getObjectProperty($this->createPager(1,  100), 'pages'));
		self::assertEquals(array(1, 2, 3, 4, null, 10),          self::getObjectProperty($this->createPager(2,  100), 'pages'));
		self::assertEquals(array(1, 2, 3, 4, 5, null, 10),       self::getObjectProperty($this->createPager(3,  100), 'pages'));
		self::assertEquals(array(1, 2, 3, 4, 5, 6, null, 10),    self::getObjectProperty($this->createPager(4,  100), 'pages'));
		self::assertEquals(array(1, 2, 3, 4, 5, 6, 7, null, 10), self::getObjectProperty($this->createPager(5,  100), 'pages'));
		self::assertEquals(array(1, null, 4, 5, 6, 7, 8, 9, 10), self::getObjectProperty($this->createPager(6,  100), 'pages'));
		self::assertEquals(array(1, null, 5, 6, 7, 8, 9, 10),    self::getObjectProperty($this->createPager(7,  100), 'pages'));
		self::assertEquals(array(1, null, 6, 7, 8, 9, 10),       self::getObjectProperty($this->createPager(8,  100), 'pages'));
		self::assertEquals(array(1, null, 7, 8, 9, 10),          self::getObjectProperty($this->createPager(9,  100), 'pages'));
		self::assertEquals(array(1, null, 8, 9, 10),             self::getObjectProperty($this->createPager(10, 100), 'pages'));

		self::assertEquals(array(1, null, 4, 5, 6, 7, 8, null, 11), self::getObjectProperty($this->createPager(6,  110), 'pages'));
	}

	/**
	 * @return Site_Pager_Abstract
	 * @param string $urlTemplate
	 * @param int $currentPage
	 * @param int $totalItems
	 * @param int $itemsPerPage
	 */
	protected function createPager($currentPage, $totalItems) {
		return new Site_Pager_Simple('?page=%d', $currentPage, $totalItems, 10);
	}

}