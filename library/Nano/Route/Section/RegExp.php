<?php

class Nano_Route_Section_RegExp extends Nano_Route_Section {

	public function __construct($location) {
		parent::__construct('/' . mb_strToLower($location, 'UTF-8') . '/i');
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