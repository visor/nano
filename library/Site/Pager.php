<?php

class Site_Pager {

	const PER_PAGE = 20;

	/**
	 * @return Site_Pager_Abstract
	 * @param string $type
	 * @param string $urlTemplate
	 * @param int $currentPage
	 * @param int $totalItems
	 * @param int $itemsPerPage
	 */
	public static function factory($type, $urlTemplate, $currentPage, $totalItems, $itemsPerPage = self::PER_PAGE) {
		$name = __CLASS__ . '_' . Strings::ucFirst($type);
		if (!class_exists($name)) {
			throw new Nano_Exception('Class ' . $name . ' not found');
		}
		return new $name($urlTemplate, $currentPage, $totalItems, $itemsPerPage);
	}

}