<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SectionRegExpTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Route_Section_Static
	 */
	protected $section;

	protected function setUp() {
		$this->section = new Nano_Route_Section_RegExp('profile\/(?P<id>\d+)');
	}

	public function testShouldMatchesWhenUrlLocationStartsWithSectionLocation() {
		self::assertTrue($this->section->sectionMatches('profile/1'));
		self::assertTrue($this->section->sectionMatches('profile/2/settings'));
	}

	public function testShouldRemovePrefixFromUrlLocation() {
		self::assertEquals('', $this->section->trimSectionLocation('profile/1'));
		self::assertEquals('/settings', $this->section->trimSectionLocation('/settings'));
	}

	public function testShouldPassMatchedParametersIntoRoute() {
		self::markTestIncomplete('Not implemented yet');
	}

	protected function tearDown() {
		unSet($this->section);
	}

}