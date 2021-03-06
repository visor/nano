<?php

namespace Nano\Route\Section;

class Root extends \Nano\Route\Section\Common {

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
	 * @return \Nano\Route\Common|null
	 * @param string $method
	 * @param string $location
	 */
	public function getFor($method, $location) {
		if (($result = $this->findSection($method, $location)) instanceof \Nano\Route\Common) {
			return $result;
		}

		return $this->findRoute($method, $location);
	}

}