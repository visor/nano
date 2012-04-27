<?php

class Nano_Route_Section_Root extends Nano_Route_Section {

	public function __construct() {
		parent::__construct(null);
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function sectionMatches($location) {
		return true;
	}

	/**
	 * @return string
	 * @param string $location
	 */
	public function trimSectionLocation($location) {
		return $location;
	}

	/**
	 * @return Nano_Route_Abstract|null
	 * @param string $method
	 * @param string $location
	 */
	public function getFor($method, $location) {
		if (($result = $this->findSection($method, $location)) instanceof Nano_Route_Abstract) {
			return $result;
		}

		return $this->findRoute($method, $location);
	}

}