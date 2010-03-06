<?php

class Assets_ScriptTest extends TestUtils_TestCase {

	/**
	 * @var Assets_Scripts
	 */
	private $scripts = null;

	/**
	 * @var string
	 */
	private $dir = null;

	public function setUp() {
		$this->scripts = new Assets_Scripts();
		$this->dir     = $this->files->get($this, DS . 'scripts');
	}

	public function testAdding() {
		$this->scripts->append('file01');
		$this->scripts->prepend('file02');
		$this->scripts->append('file03');
		$this->scripts->prepend('file04');

		$data     = $this->scripts->getData();
		$actual   = $data[Assets_Abstract::DEFAULT_NAME];
		$expected = array(
			array(
				  'file'   => 'file04'
				, 'params' => null
				, 'script' => false
			)
			, array(
				  'file'   => 'file02'
				, 'params' => null
				, 'script' => false
			)
			, array(
				  'file'   => 'file01'
				, 'params' => null
				, 'script' => false
			)
			, array(
				  'file'   => 'file03'
				, 'params' => null
				, 'script' => false
			)
		);
		self::assertEquals($expected, $actual);
	}

	public function testLoading() {
		$this->scripts->load('test-load');

		$data     = $this->scripts->getData();
		$actual   = $data[Assets_Abstract::DEFAULT_NAME];
		$expected = array(
			array(
				  'file'   => 'file04'
				, 'params' => null
				, 'script' => false
			)
			, array(
				  'file'   => 'file02'
				, 'params' => null
				, 'script' => false
			)
			, array(
				  'file'   => 'file01'
				, 'params' => null
				, 'script' => false
			)
			, array(
				  'file'   => 'file03'
				, 'params' => null
				, 'script' => false
			)
		);
		self::assertEquals($expected, $actual);
	}

	public function testBuild() {
		$this->scripts->append($this->file('test01.js'));
		$this->scripts->append($this->file('test02.js'));

		$name = 'test';
		$this->scripts->build($name);
		$file = $this->dir . '/' . $name . '/' . Assets_Abstract::DEFAULT_NAME . '.js';
		self::assertFileExists($file);
		self::assertEquals("var var1 = 'test01';\nvar var2 = 'test02';\n", file_get_contents($file));
	}

	public function testBuildWithVariables() {
		$this->scripts->variable('v1', 'var1');
		$this->scripts->variable('v2', 'var2');
		$this->scripts->append($this->file('test03.js'));
		$this->scripts->append($this->file('test04.js'));

		$name = 'test-var';
		$this->scripts->build($name);
		$file = $this->dir . '/' . $name . '/' . Assets_Abstract::DEFAULT_NAME . '.js';
		self::assertFileExists($file);
		self::assertEquals("var var1 = 'test03var2';\nvar var2 = 'test04var1';\n", file_get_contents($file));
	}

	public function testBuildScript() {
		$this->scripts->php($this->file('test05.php'));
		$name = 'test-php';
		$this->scripts->build($name);
		$file = $this->dir . '/' . $name . '/' . Assets_Abstract::DEFAULT_NAME . '.js';
		self::assertFileExists($file);
		self::assertEquals('//code executed', file_get_contents($file));
	}

	public function testGenerateTag() {
		self::markTestIncomplete();
	}

	public function tearDown() {
		$this->scripts = null;
	}

	/**
	 * @return string
	 * @param string $name
	 */
	protected function file($name) {
		return dirName(__FILE__) . '/_files/' . $name;
	}

}