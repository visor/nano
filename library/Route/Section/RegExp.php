<?php

namespace Nano\Route\Section;

class RegExp extends \Nano\Route\Section\Common {

	public function __construct($location) {
		parent::__construct('/^' . $location . '/i');
	}

	/**
	 * @return boolean
	 * @param string $location
	 */
	public function sectionMatches($location) {
		$matches = array();
		if (0 === preg_match($this->location, $location, $matches)) {
			return false;
		}

		foreach ($matches as $name => $value) {
			if (is_string($name)) {
				$this->params[$name] = $value;
			}
		}
		return true;
	}

	/**
	 * @return string
	 * @param string $location
	 */
	public function trimSectionLocation($location) {
		return preg_replace($this->location, '', $location);
	}

}