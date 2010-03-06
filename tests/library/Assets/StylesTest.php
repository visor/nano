<?php

class Assets_StylesTest extends TestUtils_TestCase {

	/**
	 * @var Assets_Styles
	 */
	private $styles = null;

	/**
	 * @var string
	 */
	private $dir = null;

	public function setUp() {
		$this->styles = new Assets_Styles();
		$this->dir    = $this->files->get($this, DS . 'styles');
	}

	public function testAdding() {
		self::markTestIncomplete();
	}

	public function testLoading() {
		self::markTestIncomplete();
	}

	public function testBuild() {
		self::markTestIncomplete();
	}

	public function testBuildWithVariables() {
		self::markTestIncomplete();
	}

	public function testBuildScript() {
		self::markTestIncomplete();
	}

	public function testGenerateTag() {
		self::markTestIncomplete();
	}

	public function tearDown() {
		$this->styles = null;
	}

}