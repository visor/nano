<?php

class ScriptsHelper extends Nano_Helper {

	const PLACEHOLDER = 'footer-javascript';

	/**
	 * @return ScriptsHelper
	 */
	public function invoke() {
		return $this;
	}

	public function captured() {
		return $this->helper()->placeholder(self::PLACEHOLDER);
	}

	public function captureStart() {
		$this->helper()->placeholder()->start(self::PLACEHOLDER);
	}

	public function captureEnd() {
		$this->helper()->placeholder()->stop(self::PLACEHOLDER);
	}

}