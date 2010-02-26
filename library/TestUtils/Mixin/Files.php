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

}