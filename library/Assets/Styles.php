. '<?php

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
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function append($path, $media = null, $condition = null) {
		return $this->addItem(true, false, $path, $condition, $this->getMedia($media));
	}

	/**
	 * @return Assets_Styles
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function prepend($path, $media = null, $condition = null) {
		$this->addData(false, false, $path, $condition, $this->getMedia($media));
		return $this;
	}

	/**
	 * @return Assets_Styles
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function appendPHP($path, $media = null, $condition = null) {
		return $this->addItem(true, true, $path, $condition, $this->getMedia($media));
	}

	/**
	 * @return Assets_Styles
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function prependPHP($path, $media = null, $condition = null) {
		return $this->addData(false, true, $path, $condition, $this->getMedia($media));
	}

	/**
	 * @return string
	 * @param string $url
	 * @param array $item
	 * @param string $group
	 */
	protected function tag($url, array $item, $group) {
		if (isset($item['params']['media'])) {
			return '<link rel="stylesheet" type="text/css" href="' . $url . '" media="' . $item['params']['media'] .'" />';
		}
		return '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
	}

	/**
	 * @return string[string]
	 * @param string $media
	 */
	protected function getMedia($media) {
		if (null === $media) {
			return array();
		}
		return array('media' => $media);
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