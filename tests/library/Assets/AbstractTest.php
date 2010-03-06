<?php

class Assets_Abstract_Test extends Assets_Abstract {

	protected $type = 'test';
	protected $ext  = 'test';

	protected function tag($url, array $params) {
		return $params[0];
	}

	public function publicWrite($base, $group) {
		parent::write($base, $group);
	}

}

class Assets_AbstractTest extends TestUtils_TestCase {

	/**
	 * @var Assets_Abstract
	 */
	private $asset = null;

	/**
	 * @var ReflectionClass
	 */
	private $class = null;

	public function setUp() {
		$this->asset = $this->getMock('Assets_Abstract_Test', array('write'));
		$this->files->clean($this, DS . 'output');
	}

	public function testGenerateGroupKey() {
		$file = $this->files->get($this, '/input/file1');
		$this->asset->addItem(true, false, $file, array('group1', 'p1' => 'v1'));
		$this->asset->addItem(true, false, $file, array('group2', 'v1', 'v2'));
		$this->asset->addItem(true, false, $file);
		$items = $this->asset->getItems();

		self::assertEquals(3, count($items));
		self::assertArrayHasKey('default', $items);
		self::assertArrayHasKey('group1-v1', $items);
		self::assertArrayHasKey('group2-v1-v2', $items);
	}

	public function testClean() {
		$file1 = $this->files->get($this, '/input/file1');
		$file2 = $this->files->get($this, '/input/file2');
		$file3 = $this->files->get($this, '/input/file3');
		$file4 = $this->files->get($this, '/input/file4');

		$this->asset->addItem(true, false, $file1, array('group1'));
		$this->asset->addItem(true, false, $file2, array('group2'));
		$this->asset->addItem(true, false, $file3, array('group3'));
		$this->asset->addItem(true, false, $file4, array('group4'));

		self::assertEquals(4, count($this->asset->getItems()));
		$this->asset->clean();
		self::assertEquals(0, count($this->asset->getItems()));
	}

	public function testAddOrdering() {
		$file1 = $this->files->get($this, '/input/file1');
		$file2 = $this->files->get($this, '/input/file2');
		$file3 = $this->files->get($this, '/input/file3');
		$file4 = $this->files->get($this, '/input/file4');

		$this->asset->addItem(true, false, $file1);   //append file
		$this->asset->addItem(true, true, $file2);    //append php
		$this->asset->addItem(false, false, $file3);  //prepend file
		$this->asset->addItem(false, true, $file4);   //prepend php

		$items = $this->asset->getItems();

		self::assertEquals(1, count($items));
		self::assertArrayHasKey('default', $items);
		self::assertEquals(4, count($items['default']['files']));

		$actualKeys   = array_keys($items['default']['files']);
		$expectedKeys = array(
			  $file4
			, $file3
			, $file1
			, $file2
		);
		self::assertEquals($actualKeys, $expectedKeys);
	}

	public function testExceptionWhenNoOutputFolder() {
		$asset = $this->asset;
		self::assertException(function () use ($asset) { $asset->import(); }, 'RuntimeException', 'No output folder');
	}

	public function testTagGeneration() {
		$file1 = $this->files->get($this, '/input/file1');
		$file2 = $this->files->get($this, '/input/file2');
		$file3 = $this->files->get($this, '/input/file3');
		$file4 = $this->files->get($this, '/input/file4');

		$this->asset->addItem(true, false, $file1, array('group1'));   //append file
		$this->asset->addItem(true, true, $file2, array('group2'));    //append php
		$this->asset->addItem(false, false, $file3, array('group3'));  //prepend file
		$this->asset->addItem(false, true, $file4, array('group4'));   //prepend php
		$this->asset->setOutput($this->files->get($this, DS . 'output'));

		$this->asset->expects($this->exactly(4))
			->method('write')
			->will($this->returnCallBack(array($this->asset, 'publicWrite')))
		;

		$expected = implode(PHP_EOL, array('group1', 'group2', 'group3', 'group4'));
		self::assertEquals($this->asset->import(), $expected);
	}

	/**
	 * @depends testTagGeneration
	 */
	public function testLoadingFromCache() {
		$this->testTagGeneration();
		$this->asset->expects($this->never())->method('write');

		$expected = implode(PHP_EOL, array('group1', 'group2', 'group3', 'group4'));
		self::assertEquals($this->asset->import(), $expected);
	}

	public function testGeneratingWhenModified() {
		$file1 = $this->files->get($this, '/input/file1');
		$file2 = $this->files->get($this, '/input/file2');
		$file3 = $this->files->get($this, '/input/file3');
		$file4 = $this->files->get($this, '/input/file4');

		$this->asset->addItem(true, false, $file1, array('group1'));   //append file
		$this->asset->addItem(true, true, $file2, array('group2'));    //append php
		$this->asset->addItem(false, false, $file3, array('group3'));  //prepend file
		$this->asset->addItem(false, true, $file4, array('group4'));   //prepend php
		$this->asset->setOutput($this->files->get($this, DS . 'output'));

		$this->asset->expects($this->exactly(8))
			->method('write')
			->will($this->returnCallBack(array($this->asset, 'publicWrite')))
		;

		$this->asset->import();
		touch($this->files->get($this, '/input/file1'));
		touch($this->files->get($this, '/input/file2'));
		$this->asset->import();
		touch($this->files->get($this, '/input/file3'));
		touch($this->files->get($this, '/input/file4'));
		$this->asset->import();
	}

	public function testGetting() {
		$file1 = $this->files->get($this, '/input/file1');
		$file2 = $this->files->get($this, '/input/file2');
		$file3 = $this->files->get($this, '/input/file3');
		$file4 = $this->files->get($this, '/input/file4');

		$this->asset->addItem(true, false, $file1, array('group1'));   //append file
		$this->asset->addItem(true, true, $file2, array('group2'));    //append php
		$this->asset->addItem(false, false, $file3, array('group3'));  //prepend file
		$this->asset->addItem(false, true, $file4, array('group4'));   //prepend php
		$this->asset->setOutput($this->files->get($this, DS . 'output'));

		$this->asset->expects($this->exactly(4))
			->method('write')
			->will($this->returnCallBack(array($this->asset, 'publicWrite')))
		;

		self::assertEquals('file1', $this->asset->get('group1'));
		self::assertEquals('file2', $this->asset->get('group2'));
		self::assertEquals('file3', $this->asset->get('group3'));
		self::assertEquals('file4', $this->asset->get('group4'));
		self::assertEquals('file1', $this->asset->get('group1'));
		self::assertEquals('file2', $this->asset->get('group2'));
		self::assertEquals('file3', $this->asset->get('group3'));
		self::assertEquals('file4', $this->asset->get('group4'));
	}


	public function tearDown() {
		unset($this->styles);
		$this->files->clean($this, DS . 'output');
	}

}