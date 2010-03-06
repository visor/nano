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
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function append($path, $media = null, $condition = null) {
		return $this->addItem(true, false, $path, $this->getParams($media, $condition));
	}

	/**
	 * @return Assets_Styles
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function prepend($path, $media = null, $condition = null) {
		$this->addData(false, false, $path, $this->getParams($media, $condition));
		return $this;
	}

	/**
	 * @return Assets_Styles
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function appendPHP($path, $media = null, $condition = null) {
		return $this->addItem(true, true, $path, $this->getParams($media, $condition));
	}

	/**
	 * @return Assets_Styles
	 * @param string $path
	 * @param string $media
	 * @param string $condition
	 */
	public function prependPHP($path, $media = null, $condition = null) {
		return $this->addData(false, true, $path, $this->getParams($media, $condition));
	}

	/**
	 * @return string
	 * @param string $url
	 * @param array $params
	 */
	protected function tag($url, array $params) {
		$before = '';
		$after  = '';
		$media  = '';
		if (isset($params['condition'])) {
			$before = '<!--[if ' . $params['condition'] .']>';
			$after  = '<![endif]-->';
		}
		if (isset($params['media'])) {
			$media = 'media="' . $params['media'] .'" ';
		}
		return $before . '<link rel="stylesheet" type="text/css" href="' . $url . '" ' . $media . '/>' . $after;
	}

	/**
	 * @return string[string]
	 * @param string $media
	 */
	protected function getParams($media, $condition) {
		if (null === $media && null === $condition) {
			return array();
		}
		if (null === $media) {
			return array('condition' => $condition);
		}
		if (null === $condition) {
			return array('media' => $media);
		}
		return array('media' => $media, 'condition' => $condition);
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