<?php

class Nano_Route_Section_Root extends Nano_Route_Section {

	public function __construct() {
		parent::__construct(null);
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	protected function sectionMatches($location) {
		return false;
	}

	/**
	 * @return string
	 * @param string $location
	 */
	protected function trimSectionLocation($location) {
		return $location;
	}

}