<?php

/**
 * @group framework
 * @group assets
 */
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
		Nano::helper()->setDispatcher(Application::current()->getDispatcher());

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
		$this->asset->clean();
		$this->asset->addItem(true, false, $file1, array('group1'));   //append file
		$this->asset->addItem(true, true, $file2, array('group2'));    //append php
		$this->asset->addItem(false, false, $file3, array('group3'));  //prepend file
		$this->asset->addItem(false, true, $file4, array('group4'));   //prepend php

		$this->asset->import();

		$this->asset->clean();
		$this->asset->addItem(true, false, $file1, array('group1'));   //append file
		$this->asset->addItem(true, true, $file2, array('group2'));    //append php
		$this->asset->addItem(false, false, $file3, array('group3'));  //prepend file
		$this->asset->addItem(false, true, $file4, array('group4'));   //prepend php

		touch($this->files->get($this, '/input/file3'));
		touch($this->files->get($this, '/input/file4'));
		$this->asset->import();
	}

	public function testDisplay() {
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

		$headers = array();
		self::assertEquals('file1', $this->asset->display('group1', $headers));
		self::assertEquals('file2', $this->asset->display('group2', $headers));
		self::assertEquals('file3', $this->asset->display('group3', $headers));
		self::assertEquals('file4', $this->asset->display('group4', $headers));
		self::assertEquals('file1', $this->asset->display('group1', $headers));
		self::assertEquals('file2', $this->asset->display('group2', $headers));
		self::assertEquals('file3', $this->asset->display('group3', $headers));
		self::assertEquals('file4', $this->asset->display('group4', $headers));
	}

	/**
	 * @paranoid
	 */
	public function testGettingType() {
		self::assertEquals(self::getObjectProperty($this->asset, 'type'), $this->asset->getType());
		self::assertEquals('test', $this->asset->getType());
	}

	public function testAddingSameFileShouldBeIgnored() {
		$file1 = $this->files->get($this, '/input/file1');
		$this->asset->addItem(true, false,  $file1, array('group1'));
		$this->asset->addItem(true, true,   $file1, array('group1'));
		$this->asset->addItem(false, true,  $file1, array('group1'));
		$this->asset->addItem(false, false, $file1, array('group1'));

		$items = self::getObjectProperty($this->asset, 'items');
		self::assertArrayHasKey('group1', $items);
		self::assertArrayHasKey('files', $items['group1']);
		self::assertEquals(1, count($items['group1']['files']));
	}

	public function testGroupTimeShouldBeUpdatedWhenNewestFileAdded() {
		$file1 = $this->files->get($this, '/input/file1');
		$file2 = $this->files->get($this, '/input/file2');

		touch($file1, Date::create('-1 day')->format('U'));
		touch($file2);
		self::assertNotEquals(fileMTime($file1), fileMTime($file2));

		$this->asset->addItem(true, false,  $file1, array('group1'));

		$items = self::getObjectProperty($this->asset, 'items');
		self::assertArrayHasKey('group1', $items);
		self::assertArrayHasKey('time', $items['group1']);
		self::assertEquals(fileMTime($file1), $items['group1']['time']);

		$this->asset->addItem(true, false,  $file2, array('group1'));
		$items = self::getObjectProperty($this->asset, 'items');
		self::assertArrayHasKey('group1', $items);
		self::assertArrayHasKey('time', $items['group1']);
		self::assertEquals(fileMTime($file2), $items['group1']['time']);
	}

	public function tearDown() {
		unset($this->styles);
		$this->files->clean($this, DS . 'output');
	}

}

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