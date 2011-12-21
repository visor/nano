<?php

/**
 * @group framework
 * @group assets
 */
class Assets_FactoryTest extends TestUtils_TestCase {

	public function setUp() {
		$this->clearAssetsFactory();
	}

	public function testFactoryShouldReturnSingletonInstanceForStyles() {
		self::assertInstanceOf('Assets_Styles', Assets::style());
		self::assertSame(Assets::style(), Assets::style());
	}

	public function testFactoryShouldReturnSingletonInstanceForScripts() {
		self::assertInstanceOf('Assets_Scripts', Assets::script());
		self::assertSame(Assets::script(), Assets::script());
	}

	public function testFactoryShouldClearAllFilesInOutputFolderWhenClearingCahce() {
		Assets::script()->setOutput($this->files->get($this, ''));
		Assets::style()->setOutput($this->files->get($this, ''));

		touch($this->files->get($this, '/scripts/script.js'));
		touch($this->files->get($this, '/styles/style.css'));

		ob_start();
		Assets::clearCache(array('.gitignore'), true);
		$output = ob_get_contents();
		ob_end_clean();

		self::assertContains('/scripts/script.js', $output);
		self::assertContains('/styles/style.css', $output);
		self::assertFileNotExists($this->files->get($this, '/scripts/script.js'));
		self::assertFileNotExists($this->files->get($this, '/styles/style.css'));
	}

	protected function tearDown() {
		$this->clearAssetsFactory();
		if (ob_get_level() > 0 && ob_get_length()) {
			ob_end_clean();
		}
	}

	protected function clearAssetsFactory() {
		self::setObjectProperty('Assets', 'style', null);
		self::setObjectProperty('Assets', 'script', null);
	}
}