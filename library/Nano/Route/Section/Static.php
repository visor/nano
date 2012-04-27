<?php

class Nano_Route_Section_Static extends Nano_Route_Section {

	public function __construct($location) {
		parent::__construct(mb_strToLower($location, 'UTF-8'));
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	protected function sectionMatches($location) {
		return 0 === strPos($location, $this->location);
	}

	/**
	 * @return string
	 * @param string $location
	 */
	protected function trimSectionLocation($location) {
		return subStr($location, mb_strLen($this->location));
	}

}