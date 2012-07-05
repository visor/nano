<?php

namespace Nano\Route\Section;

class StaticLocation extends \Nano\Route\Section\Common {

	public function __construct($location) {
		parent::__construct(mb_strToLower($location, 'UTF-8'));
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function sectionMatches($location) {
		return 0 === strPos($location, $this->location);
	}

	/**
	 * @return string
	 * @param string $location
	 */
	public function trimSectionLocation($location) {
		return subStr($location, mb_strLen($this->location));
	}

}