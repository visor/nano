<?php

/**
 * @method Assets_Scripts php() php(string $file, string $group)
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
	 * @param string $value
	 */
	public function append($value, $condition = null) {
		$group = null === $condition ? self::DEFAULT_GROUP : $condition;
		$this->addData(true, $group, $value);
		return $this;
	}

	/**
	 * @return Assets_Scripts
	 * @param string $value
	 */
	public function prepend($value, $condition = null) {
		$group = null === $condition ? self::DEFAULT_GROUP : $condition;
		$this->addData(false, $group, $value);
		return $this;
	}

	/**
	 * @return string
	 * @param string $name
	 */
	public function tag($name = self::DEFAULT_NAME) {
	}

}