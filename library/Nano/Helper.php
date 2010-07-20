<?php

abstract class Nano_Helper {

	abstract public function invoke();

	/**
	 * @return string
	 * @param string $folder
	 * @param string $view
	 * @param array $variables
	 */
	protected function render($folder, $view, array $variables = array()) {
		return Nano_Render::script($folder, $view, $variables, true);
	}

}