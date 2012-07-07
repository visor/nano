<?php

class TestUtils_Mixin_FilesTest extends \Nano\TestUtils\TestCase {

	public function testCountFilesShouldThrowExceptionWhenDirectoryNotExists() {
		$this->setExpectedException('InvalidArgumentException', ' is not directory');
		$this->files->countFiles($this, '/.gitignore');
	}

	public function testCountFilesShouldThrowExceptionWhenNotDirectoryPassed() {
		$this->setExpectedException('InvalidArgumentException', ' not exists');
		$this->files->countFiles($this, '/some');
	}

	public function testCountShouldReturnValueWhenValidArgumentPassed() {
		self::assertEquals(2, $this->files->countFiles($this, ''));
	}

	public function testGetShouldReplaceSlashesToDirectorySeparator() {
		$slash = '/' === DIRECTORY_SEPARATOR ? '\\' : '/';
		$expected = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'some' . DIRECTORY_SEPARATOR . 'folder';
		self::assertEquals($expected, $this->files->get($this, $slash . 'some' . $slash . 'folder'));
	}

	public function testGetShouldReturnFullPath() {
		$expected = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'folder';
		self::assertEquals($expected, $this->files->get($this, '\\folder'));
	}

	public function testGetShouldAppendAnotherDirToResultWhenPassed() {
		$expected = __DIR__ . DIRECTORY_SEPARATOR . 'another' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'folder';
		self::assertEquals($expected, $this->files->get($this, '\\folder', '\\another'));
	}

	public function testCleanShouldNotRemoveEmptyFile() {
		$this->files->clean($this, '/for-clear');
		self::assertFileExists($this->files->get($this, '/for-clear/empty'));
	}

	public function testCleanShouldRemoveChilds() {
		$root = $this->files->get($this, '/for-clear/');
		mkDir($root . 'some-dir');
		touch($root . 'some-file');

		self::assertEquals(3, $this->files->countFiles($this, '/for-clear'));
		$this->files->clean($this, '/for-clear');
		self::assertEquals(1, $this->files->countFiles($this, '/for-clear'));
		self::assertFileNotExists($root . 'some-dir');
		self::assertFileNotExists($root . 'some-file');
		self::assertFileExists($root . 'empty');
	}

	public function testClearShouldCreateDirectoryIfNotExists() {
		$this->files->clean($this, '/not-exists');
		self::assertFileExists($this->files->get($this, '/not-exists'));
	}

	protected function tearDown() {
		$this->files->clean($this, '/for-clear');
		$dir = $this->files->get($this, '/not-exists');
		if (is_dir($dir)) {
			rmDir($dir);
		}
	}

}