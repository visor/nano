<?php

/**
 * @group framework
 * @group assets
 */
class Assets_StylesTest extends TestUtils_TestCase {

	/**
	 * @var Assets_Styles
	 */
	private $styles = null;

	public function setUp() {
		Nano::config()->set('cdn', (object)array('servers' => array()));

		$this->styles = new Assets_Styles();

		$this->styles->setOutput($this->files->get($this, DS . 'output'));
		$this->files->clean($this, DS . 'output');

		touch($this->files->get($this, '/input/file1'));
		touch($this->files->get($this, '/input/file2'));
		touch($this->files->get($this, '/input/file3'));
		touch($this->files->get($this, '/input/file4'));
	}

	public function testSimpleTag() {
		$this->styles->append($this->files->get($this, '/input/file1'));
		self::assertEquals($this->getTag('default', null, null), $this->styles->import());
	}

	public function testMediaTag() {
		$this->styles->append($this->files->get($this, '/input/file1'), 'print');
		self::assertEquals($this->getTag('print', 'print', null), $this->styles->import());
	}

	public function testConditionTag() {
		$this->styles->append($this->files->get($this, '/input/file1'), null, 'IE 7');
		self::assertEquals($this->getTag('ie7', null, 'IE 7'), $this->styles->import());
	}

	public function testComplexTag() {
		$this->styles->append($this->files->get($this, '/input/file1'));
		$this->styles->append($this->files->get($this, '/input/file2'), 'print');
		$this->styles->append($this->files->get($this, '/input/file3'), null, 'IE 7');
		$this->styles->append($this->files->get($this, '/input/file3'), 'print', 'IE 7');

		$expected =     $this->getTag('default',   null,    null)
			. PHP_EOL . $this->getTag('print',     'print', null)
			. PHP_EOL . $this->getTag('ie7',       null,    'IE 7')
			. PHP_EOL . $this->getTag('print-ie7', 'print', 'IE 7')
		;
		self::assertEquals($expected, $this->styles->import());
	}

	public function tearDown() {
		unset($this->styles);
		$this->files->clean($this, DS . 'output');
	}

	protected function getBase() {
		return md5(serialize($this->styles->getItems()) . serialize(array()));
	}

	protected function getTag($name, $media, $condition) {
		$url      = Nano::config('assets')->url . '/styles/' . $this->getBase() . '/' . $name . '.css';
		$time     = fileMTime($this->files->get($this, DS . 'input') . DS . 'file1');
		$template = '%s<link rel="stylesheet" type="text/css" href="%s" %s/>%s';
		return sprintf($template,
			  $condition ? '<!--[if ' . $condition . ']>' : null
			, $url . '?' . $time
			, $media ? 'media="' . $media .'" ' : null
			, $condition ? '<![endif]-->' : null
		);
	}
}