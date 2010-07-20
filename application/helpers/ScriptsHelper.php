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
		return Nano::helper()->placeholder(self::PLACEHOLDER);
	}

	public function captureStart() {
		Nano::helper()->placeholder()->start(self::PLACEHOLDER);
	}

	public function captureEnd() {
		Nano::helper()->placeholder()->stop(self::PLACEHOLDER);
	}

}