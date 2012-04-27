<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SectionRootTest extends TestUtils_TestCase {

	public function testSectionMatchesShouldReturnTrue() {
		$section = new Nano_Route_Section_Root();
		self::assertTrue($section->sectionMatches('some'));
		self::assertTrue($section->sectionMatches(null));
	}

	public function testTrimSectionShouldReturnParameter() {
		$section = new Nano_Route_Section_Root();
		self::assertEquals('some', $section->trimSectionLocation('some'));
	}

	public function testGetForShouldCheckChildSectionsFirst() {
		$section = new Nano_Route_Section_Root();
		$section
			->section('/bar')
				->get('', 'index', 'index')
			->end()
			->get('/bar', 'index', 'another')
		;

		self::assertInstanceOf('Nano_Route_Static', $section->getFor('get', '/bar'));
		self::assertEquals('index', $section->getFor('get', '/bar')->action());
	}

	public function testShouldNotCallMatchLocationMethod() {
		$mock = $this->getMock('Nano_Route_Section_Root', array('sectionMatches'));
		$mock->expects($this->never())->method('sectionMatches')->will($this->returnValue(true));
		$mock->getFor('get', 'some');
	}

	public function testShouldNotCallTrimLocationMethod() {
		$mock = $this->getMock('Nano_Route_Section_Root', array('sectionMatches'));
		$mock->expects($this->never())->method('trimSectionLocation')->will($this->returnValue(true));
		$mock->getFor('get', 'some');
	}

}