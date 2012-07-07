<?php

class Core_L10n_DictionaryTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\L10n\Dictionary
	 */
	protected $dictionary;

	protected function setUp() {
		$this->app->backup();

		\Nano\Application::create()
			->withConfigurationFormat('php')
			->withRootDir($GLOBALS['application']->rootDir)
			->withModule('some', __DIR__ . '/_files')
			->configure()
		;
		$this->dictionary = new \Nano\L10n\Dictionary(new \Nano\L10n\Locale('ru'));
	}

	public function testShouldLoadDefaultLanguageFileWhenLanguageFileNotExists() {
		self::assertTrue($this->dictionary->loadMessages('control-panel', null));
		self::assertEquals('Site Control Panel', $this->dictionary->getMessage('cp-title', 'control-panel', null));
	}

	public function testShouldLoadLanguageFileFromApplicationWhenExists() {
		self::assertTrue($this->dictionary->loadMessages('article', null));
		self::assertEquals('Статья добавлена', $this->dictionary->getMessage('article-create-success', 'article', null));
	}

	public function testShouldLoadLanguageFileFromApplicationWhenExistsAndModulePassed() {
		self::assertTrue($this->dictionary->loadMessages('default', 'some'));
		self::assertEquals('Какой-то другой текст', $this->dictionary->getMessage('id', 'default', 'some'));
	}

	public function testShouldLoadLanguageFileFromModuleWhenApplicationFileNotExists() {
		self::assertTrue($this->dictionary->loadMessages('another', 'some'));
		self::assertEquals('Какой-то текст', $this->dictionary->getMessage('id', 'another', 'some'));
	}

	public function testShouldReturnFalseWhenNoLanguageFileExists() {
		self::assertFalse($this->dictionary->loadMessages('not-exists', null));
		self::assertFalse($this->dictionary->loadMessages('not-exists', 'some'));
	}

	public function testShouldLoadMessageFileOnce() {
		$file = \Nano::app()->rootDir . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . 'ru' . DIRECTORY_SEPARATOR . 'article';
		$mock = $this->getMock('Nano\L10n\Dictionary', array('getMessageFileName'), array(new \Nano\L10n\Locale('ru')));
		$mock->expects($this->once())->method('getMessageFileName')->withAnyParameters()->will($this->returnValue($file));

		self::assertTrue($mock->loadMessages('article', null));
		self::assertTrue($mock->loadMessages('article', null));
	}

	public function testShouldReturnNullWhenMessageNotExists() {
		self::assertNull($this->dictionary->getMessage('id', 'not-exists', null));
		self::assertNull($this->dictionary->getMessage('id', 'not-exists', 'some'));
	}

	protected function tearDown() {
		unSet($this->dictionary);

		$this->app->restore();
	}

}