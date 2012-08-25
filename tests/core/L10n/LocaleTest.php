<?php

class Core_L10n_LocaleTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\L10n\Locale
	 */
	protected $en;

	protected function setUp() {
		$this->en = new \Nano\L10n\Locale(Nano\L10n\Locale::DEFAULT_LOCALE);

		$this->app->backup();
		\Nano\Application::create()
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->configure()
		;
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
		self::assertNull($this->en->translate(null, 'some-file', 'some-id'));
	}

	public function testTranslateMethodShouldLoadMessageFile() {
		self::assertEquals('message 1', $this->en->translate(null, 'test', 'm1'));
	}

	public function testTranslateShouldReplaceVariables() {
		self::assertEquals('format 2 string', $this->en->translate(null, 'test', 'f1', array('{d}' => '2', '{s}' => 'string')));
	}

	protected function tearDown() {
		unSet($this->en);
		$this->app->restore();
	}

}