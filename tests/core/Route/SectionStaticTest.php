<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SectionStaticTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Route_Section_Static
	 */
	protected $section;

	protected function setUp() {
		$this->section = new Nano_Route_Section_Static('foo');
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