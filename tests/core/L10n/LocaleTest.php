<?php

class Core_L10n_LocaleTest extends TestUtils_TestCase {

	/**
	 * @var L10n_Locale
	 */
	protected $en;

	protected function setUp() {
		$this->en = new L10n_Locale(L10n_Locale::DEFAULT_LOCALE);
	}

	public function testShouldStoreLocaleName() {
		$locale = new L10n_Locale('ru');
		self::assertEquals('ru', $locale->getName());
	}

	public function testShouldUseDefaultLocaleAsFallBackWhenNotPassed() {
		$locale = new L10n_Locale('ru');
		self::assertEquals(L10n_Locale::DEFAULT_LOCALE, $locale->getFallBack());
	}

	public function testShouldThrowExceptionWhenFallbackEqualsToLocale() {
		$this->setExpectedException('L10n_Exception', 'Locale name should not equals to fallback');
		new L10n_Locale('ru', 'ru');
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