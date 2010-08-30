<?php

class SitePagerAbstractTest extends TestUtils_TestCase {

	public function testInvalidCurrentPage() {
		self::assertEquals(1, $this->createPager('?page=%d', 'string value', 100, 10)->getCurrentPage());
		self::assertEquals(1, $this->createPager('?page=%d', 0, 100, 10)->getCurrentPage());
		self::assertEquals(10, $this->createPager('?page=%d', 1000, 100, 10)->getCurrentPage());
	}

	public function testCalculateTotalPages() {
		self::assertEquals(1, $this->createPager('?page=%d', 1, 1, 10)->getTotalPages());
		self::assertEquals(10, $this->createPager('?page=%d', 1, 100, 10)->getTotalPages());
	}

	public function testCalculateOffset() {
		self::assertEquals(0, $this->createPager('?page=%d', 1, 1, 10)->getOffset());
		self::assertEquals(10, $this->createPager('?page=%d', 2, 100, 10)->getOffset());
	}

	public function testUrl() {
		$pager = $this->createPager('?page=%d', 2, 100, 10);
		self::assertEquals('?page=1', $pager->getPageUrl(1));
		self::assertEquals('?page=1', $pager->getPageUrl('some string'));
		self::assertEquals('?page=10', $pager->getPageUrl(1000));
	}

	/**
	 * @return Site_Pager_Abstract
	 * @param string $urlTemplate
	 * @param int $currentPage
	 * @param int $totalItems
	 * @param int $itemsPerPage
	 */
	protected function createPager($urlTemplate, $currentPage, $totalItems, $itemsPerPage) {
		return $this->getMockForAbstractClass('Site_Pager_Abstract', array($urlTemplate, $currentPage, $totalItems, $itemsPerPage));
	}

}