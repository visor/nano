<?php

class Core_L10n_LocaleTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\L10n\Locale
	 */
	protected $en;

	protected function setUp() {
		$this->en = new \Nano\L10n\Locale(Nano\L10n\Locale::DEFAULT_LOCALE);
	}

	public function testShouldStoreLocaleName() {
		$locale = new \Nano\L10n\Locale('ru');
		self::assertEquals('ru', $locale->getName());
	}

	public function testShouldUseDefaultLocaleAsFallBackWhenNotPassed() {
		$locale = new \Nano\L10n\Locale('ru');
		self::assertEquals(\Nano\L10n\Locale::DEFAULT_LOCALE, $locale->getFallBack());
	}

	public function testShouldThrowExceptionWhenFallbackEqualsToLocale() {
		$this->setExpectedException('Nano\L10n\Exception', 'Locale name should not equals to fallback');
		new \Nano\L10n\Locale('ru', 'ru');
	}

	public function testShouldNotUseFallbackWhenLocaleIsDefault() {
		self::assertNull($this->en->getFallBack());
	}

	public function testTranslateMethodShouldReturnNullWhenMessageNotExists() {
		self::assertNull($this->en->translate('some-id'));
	}

	protected function tearDown() {
		unSet($this->en);
	}

}