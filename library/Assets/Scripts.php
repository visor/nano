<?php

/**
 * @method Assets_Scripts variable() variable(string $name, string $value)
 */
class Assets_Scripts extends Assets_Abstract {

	/**
	 * @var string
	 */
	protected $type = 'scripts';

	/**
	 * @var string
	 */
	protected $ext = 'js';

	/**
	 * @return Assets_Scripts
	 * @param string $path
	 * @param string $condition
	 */
	public function append($path, $condition = null) {
		return $this->addItem(true, false, $path, $condition);
	}

	/**
	 * @return Assets_Scripts
	 * @param string $path
	 * @param string $condition
	 */
	public function prepend($path, $condition = null) {
		return $this->addItem(false, false, $path, $condition);
	}

	/**
	 * @return Assets_Scripts
	 * @param string $path
	 * @param string $condition
	 */
	public function appendPHP($path, $condition = null) {
		return $this->addItem(true, true, $path, $condition);
	}

	/**
	 * @return Assets_Scripts
	 * @param string $path
	 * @param string $condition
	 */
	public function prependPHP($path, $condition = null) {
		return $this->addItem(false, true, $path, $condition);
	}

	/**
	 * @return string
	 * @param string $url
	 * @param array $item
	 * @param string $group
	 */
	protected function tag($url, array $item, $group) {
		return '<script type="text/javascript>" src="' . $url . '"></script>';
	}

}