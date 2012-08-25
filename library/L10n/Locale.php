<?php

namespace Nano\L10n;

class Locale {

	const DEFAULT_LOCALE = 'en_us';

	/**
	 * @var string
	 */
	protected $name, $fallBack;

	/**
	 * @var Dictionary
	 */
	protected $storage;

	/**
	 * @param string $name
	 * @param string $fallBack
	 *
	 * @throws \Nano\L10n\Exception
	 */
	public function __construct($name, $fallBack = self::DEFAULT_LOCALE) {
		if ($name === $fallBack) {
			if (self::DEFAULT_LOCALE === $fallBack) {
				$fallBack = null;
			} else {
				throw new Exception('Locale name should not equals to fallback');
			}
		}

		$this->name     = $name;
		$this->fallBack = $fallBack;
		$this->storage  = new Dictionary($this);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getFallBack() {
		return $this->fallBack;
	}

	/**
	 * @return null|string
	 * @param null|string $module
	 * @param string $baseName
	 * @param string $id
	 * @param array $arguments
	 */
	public function translate($module, $baseName, $id, array $arguments = null) {
		if (!$this->storage->isLoaded($baseName, $module)) {
			if (!$this->storage->loadMessages($baseName, $module)) {
				return null;
			}
		}

		$message = $this->storage->getMessage($id, $baseName, $module);
		if (null === $arguments || array() === $arguments) {
			return $message;
		}

		//todo: add support for plural messages one|two|many or one|two
		return strTr($message, $arguments);
	}

}