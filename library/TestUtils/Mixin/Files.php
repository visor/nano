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
			$result .= $anotherDir;
		}
		return  $result . '/_files' . $name;
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
	 */
	public function countFiles(TestUtils_TestCase $test, $dir) {
		$iterator = new DirectoryIterator($this->get($test, $dir));
		$result   = 0;
		foreach ($iterator as $item) {
			++$result;
		}
		return $result;
	}

}