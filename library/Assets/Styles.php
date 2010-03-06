<?php

/**
 * @method Assets_Styles php() php(string $file, string $group)
 * @method Assets_Styles variable() variable(string $name, string $value)
 */
class Assets_Styles extends Assets_Abstract {

	/**
	 * @var string
	 */
	protected $type = 'styles';

	/**
	 * @var string
	 */
	protected $ext = 'css';

	/**
	 * @return Assets_Styles
	 * @param string $value
	 * @param string $media
	 * @param string $condition
	 */
	public function append($value, $media = null, $condition = null) {
		$this->addData(true, $this->getGroup($media, $condition), $value);
		return $this;
	}

	/**
	 * @return Assets_Styles
	 * @param string $value
	 * @param string $media
	 * @param string $condition
	 */
	public function prepend($value, $media = null, $condition = null) {
		$this->addData(false, $this->getGroup($media, $condition), $value);
		return $this;
	}

	/**
	 * @return string
	 * @param string $name
	 */
	public function tag($name = self::DEFAULT_NAME) {
	}

	protected function getGroup($media, $condition) {
		if (null === $media && null === $condition) {
			return self::DEFAULT_GROUP;
		}
		if (null === $condition) {
			return parent::getGroup($media);
		}

		$condition = strToLower($condition);
		$condition = str_replace(' ', '', $condition);
		if (null === $media) {
			return parent::getGroup($condition);
		}
		return parent::getGroup($media, $condition);
	}

	/**
	 * @return string
	 * @param string $string
	 */
	protected function postProcessing($string) {
		$result = $string;
		$result = preg_replace('/(\/\*[^\*]*\*\/)/', '', $result);
		$result = preg_replace('/(\r?\n|\r)+/', "\n", $result);
		$result = preg_replace('/(\/\/[^\n]*\n)/', '', $result);
		$result = preg_replace('/\s+/', ' ', $result);
		$result = str_replace(' { ', '{', $result);
		$result = str_replace(' } ', '}', $result);
		$result = str_replace(' : ', ':', $result);
		$result = str_replace(': ', ':', $result);
		$result = str_replace('; ', ';', $result);
		$result = str_replace(', ', ',', $result);

		return $result;
	}

}