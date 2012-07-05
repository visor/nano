<?php

namespace Nano\L10n;

class Dictionary {

	/**
	 * @var Locale
	 */
	protected $locale;

	protected $messages;

	/**
	 * @param Locale $locale
	 */
	public function __construct(Locale $locale) {
		$this->locale = $locale;
	}

	/**
	 * @return string|null
	 * @param string $id
	 * @param string $baseName
	 * @param string|null $module
	 */
	public function getMessage($id, $baseName, $module) {
		$key = $this->generateKey($baseName, $module);
		if (isSet($this->messages[$key][$id])) {
			return $this->messages[$key][$id];
		}
		return null;
	}

	/**
	 * @return boolean
	 * @param string $baseName
	 * @param string|null $module
	 */
	public function loadMessages($baseName, $module) {
		$key = $this->generateKey($baseName, $module);
		if (isSet($this->messages[$key])) {
			return true;
		}

		$fileName = $this->getMessageFileName($this->locale->getName(), $baseName, $module);
		if (null === $fileName) {
			$fileName = $this->getMessageFileName($this->locale->getFallBack(), $baseName, $module);
		}
		if (null === $fileName) {
			return false;
		}

		$this->messages[$key] = \Nano::app()->configFormat->read($fileName);
		return true;
	}

	/**
	 * @return string
	 * @param string $baseName
	 * @param string|null $module
	 */
	protected function generateKey($baseName, $module) {
		return strToLower((null === $module ? '' : $module . '-') . $baseName);
	}

	protected function getMessageFileName($locale, $baseName, $module) {
		$name   = $this->generateKey($baseName, $module);
		$path   = DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . $locale;
		$result = \Nano::app()->rootDir . $path . DIRECTORY_SEPARATOR . $name;

		if (null === $module) {
			return file_exists($result) ? $result : null;
		}

		if (file_exists($result)) {
			return $result;
		}

		$result = \Nano::app()->modules->getPath($module, $path . DIRECTORY_SEPARATOR . $baseName);
		if (file_exists($result)) {
			return $result;
		}
		return null;
	}

}