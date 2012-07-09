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
	 * @return string|null
	 * @param string $message
	 * @param string $baseName
	 * @param string $module
	 * @param array  $arguments
	 */
	public function translate($message, $baseName, $module, array $arguments = null) {
		return null;
	}

}