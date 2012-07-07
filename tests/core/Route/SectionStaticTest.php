<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SectionStaticTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Route\Section\StaticLocation
	 */
	protected $section;

	protected function setUp() {
		$this->section = new \Nano\Route\Section\StaticLocation('foo');
	}

	public function testShouldMatchesWhenUrlLocationStartsWithSectionLocation() {
		self::assertTrue($this->section->sectionMatches('foo/bar'));
		self::assertTrue($this->section->sectionMatches('foobar'));
	}

	public function testShouldRemovePrefixFromUrlLocation() {
		self::assertEquals('/bar', $this->section->trimSectionLocation('foo/bar'));
		self::assertEquals('bar', $this->section->trimSectionLocation('foobar'));
	}

	protected function tearDown() {
		unSet($this->section);
	}

}