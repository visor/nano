<?php

/**
 * @group framework
 * @group assets
 */
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
		$this->scripts->setOutput($this->files->get($this, DS . 'output'));
		$this->files->clean($this, DS . 'output');

		touch($this->files->get($this, '/input/file1'));
		touch($this->files->get($this, '/input/file2'));
		touch($this->files->get($this, '/input/file3'));
		touch($this->files->get($this, '/input/file4'));
	}

	public function testSimpleTag() {
		$this->scripts->append($this->files->get($this, '/input/file1'));
		self::assertEquals($this->getTag('default', null), $this->scripts->import());
	}

	public function testConditionTag() {
		$this->scripts->append($this->files->get($this, '/input/file1'), 'gt IE 6');
		self::assertEquals($this->getTag('gtie6', 'gt IE 6'), $this->scripts->import());

		$this->scripts->clean();
		$this->scripts->append($this->files->get($this, '/input/file1'), 'IE 7');
		self::assertEquals($this->getTag('ie7', 'IE 7'), $this->scripts->import());

		$this->scripts->clean();
		$this->scripts->append($this->files->get($this, '/input/file1'), 'IE 7');
		$this->scripts->append($this->files->get($this, '/input/file1'), 'IE 6');

		$expected = $this->getTag('ie7', 'IE 7') . PHP_EOL . $this->getTag('ie6', 'IE 6');
		self::assertEquals($expected, $this->scripts->import());
	}

	public function tearDown() {
		$this->files->clean($this, DS . 'output');
		$this->scripts = null;
	}

	protected function getBase() {
		return md5(serialize($this->scripts->getItems()) . serialize(array()));
	}

	protected function getTag($group, $condition) {
		$url      = Nano::config('assets')->url . '/scripts/' . $this->getBase() . '/' . $group . '.js';
		$time     = fileMTime($this->files->get($this, DS . 'input') . DS . 'file1');
		$template = '%s<script type="text/javascript" src="%s"></script>%s';
		return sprintf($template,
			  $condition ? '<!--[if ' . $condition . ']>' : null
			, $url . '?' . $time
			, $condition ? '<![endif]-->' : null
		);
	}

}