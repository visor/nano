<?php

class TestUtils_Mixin_Files extends TestUtils_Mixin {

	const EMPTY_FILE = 'empty';

	/**
	 * @return string
	 * @param TestUtils_TestCase $test
	 * @param string $name
	 * @param string|null $anotherDir
	 */
	public function get(TestUtils_TestCase $test, $name, $anotherDir = null) {
		$class  = new ReflectionClass($test);
		$name   = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $name);
		$result = dirName($class->getFileName());
		if (null !== $anotherDir) {
			$result .= str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $anotherDir);
		}
		return  $result . DIRECTORY_SEPARATOR . '_files' . $name;
	}

	public function clean(TestUtils_TestCase $test, $dir, $fullPath = false) {
		if (false === $fullPath) {
			$dir = $this->get($test, $dir);
		}
		if (!file_exists($dir)) {
			mkDir($dir, 0755, true);
			return true;
		}
		$i = new DirectoryIterator($dir);
		$result = true;
		foreach ($i as $file) { /** @var DirectoryIterator $file */
			if ($file->isDot()) {
				continue;
			}
			if (self::EMPTY_FILE == $file->getBaseName()) {
				$result = false;
				continue;
			}
			if ($file->isDir()) {
				if ($this->clean($test, $file->getPathName(), true)) {
					rmDir($file->getPathName());
				}
				continue;
			}
			unLink($file->getPathName());
		}
		unset($i, $file);
		return $result;
	}

	/**
	 * @return int
	 * @param TestUtils_TestCase $test
	 * @param string $dir
	 *
	 * @throws InvalidArgumentException
	 */
	public function countFiles(TestUtils_TestCase $test, $dir) {
		$realPath = $this->get($test, $dir);
		if (!file_exists($realPath)) {
			throw new InvalidArgumentException($realPath . ' not exists');
		}
		if (!is_dir($realPath)) {
			throw new InvalidArgumentException($realPath . ' is not directory');
		}

		$iterator = new DirectoryIterator($realPath);
		$result   = 0;
		foreach ($iterator as $item) {
			if ($item->isDot()) {
				continue;
			}
			++$result;
		}
		return $result;
	}

}