<?php

class TestUtils_Mixin_Files extends TestUtils_Mixin {

	/**
	 * @return string
	 * @param Nano_TestUtils_TestCase $test
	 * @param string $name
	 */
	public function get(PHPUnit_Framework_TestCase $test, $name) {
		$class = new ReflectionClass($test);
		return dirName($class->getFileName()) . '/_files' . $name;
	}

	public function clean(PHPUnit_Framework_TestCase $test, $dir, $fullPath = false) {
		if (false === $fullPath) {
			$dir = $this->get($test, $dir);
		}
		$i = new DirectoryIterator($dir);
		foreach ($i as $file) {
			if ($file->isDot()) {
				continue;
			}
			if ($file->isDir()) {
				$this->clean($test, $file->getPathName(), true);
				rmDir($file->getPathName());
				continue;
			}
			unlink($file->getPathName());
		}
		unset($i, $file);
	}

}