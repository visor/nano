<?php

class PagerHelper extends Nano_Helper {

	/**
	 * @retrun PagerHelper
	 */
	public function invoke() {
		return $this;
	}

	/**
	 * @return Site_Pager_Abstract
	 * @param string $type
	 * @param string $urlTemplate
	 * @param int $currentPage
	 * @param int $totalItems
	 * @param int $itemsPerPage
	 */
	public function show($type, $urlTemplate, $currentPage, $totalItems, $itemsPerPage = Site_Pager::PER_PAGE) {
		return Site_Pager::factory($type, $urlTemplate, $currentPage, $totalItems, $itemsPerPage);
	}

}