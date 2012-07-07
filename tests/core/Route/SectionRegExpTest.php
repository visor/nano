<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SectionRegExpTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Route\Section\RegExp
	 */
	protected $section;

	protected function setUp() {
		$this->section = new \Nano\Route\Section\RegExp('profile\/(?P<id>\d+)');
	}

	public function testShouldNotMatchWhenUrlLocationNotStartsWithSectionLocation() {
		self::assertFalse($this->section->sectionMatches('profile/a'));
		self::assertFalse($this->section->sectionMatches('another/profile/1'));
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
		$this->section->get('/settings', 'profile', 'settings');
		self::assertInstanceOf('Nano\Route\StaticLocation', $this->section->getFor('get', 'profile/1/settings'));
		self::assertArrayHasKey('id', $this->section->getFor('get', 'profile/1/settings')->params());

		$params = $this->section->getFor('get', 'profile/1/settings')->params();
		self::assertEquals(1, $params['id']);
	}

	public function testShouldPassMatchedParametersIntoChildSection() {
		$this->section
			->section('/private')
				->get('/settings', 'profile', 'settings')
			->end()
		;

		self::assertInstanceOf('Nano\Route\StaticLocation', $this->section->getFor('get', 'profile/1/private/settings'));
		self::assertArrayHasKey('id', $this->section->getFor('get', 'profile/1/private/settings')->params());

		$params = $this->section->getFor('get', 'profile/1/private/settings')->params();
		self::assertEquals(1, $params['id']);
	}

	protected function tearDown() {
		unSet($this->section);
	}

}