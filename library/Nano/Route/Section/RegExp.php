<?php

class Nano_Route_Section_RegExp extends Nano_Route_Section {

	public function __construct($location) {
		parent::__construct('/^' . $location . '/i');
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function sectionMatches($location) {
		return (1 === preg_match($this->location, $location));
	}

	/**
	 * @return string
	 * @param string $location
	 */
	public function trimSectionLocation($location) {
		return preg_replace($this->location, '', $location);
	}

}