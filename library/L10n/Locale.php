<?php

class L10n_Locale {

	const DEFAULT_LOCALE = 'en_us';

	/**
	 * @var string
	 */
	protected $name, $fallBack;

	/**
	 * @var L10n_Dictionary
	 */
	protected $storage;

	/**
	 * @param string $name
	 * @param string $fallBack
	 *
	 * @throws L10n_Exception
	 */
	public function __construct($name, $fallBack = self::DEFAULT_LOCALE) {
		if ($name === $fallBack) {
			if (self::DEFAULT_LOCALE === $fallBack) {
				$fallBack = null;
			} else {
				throw new L10n_Exception('Locale name should not equals to fallback');
			}
		}

		$this->name     = $name;
		$this->fallBack = $fallBack;
		$this->storage  = new L10n_Dictionary($this);
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

	public function translate($message, array $arguments = null) {
		return null;
	}

}