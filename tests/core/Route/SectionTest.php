<?php

/**
 * @group core
 * @group routes
 */
class Core_Route_SectionTest extends TestUtils_TestCase {

	/**
	 * @var Nano_Route_Section_Static
	 */
	protected $section;

	protected function setUp() {
		$this->section = new Nano_Route_Section_Static('foo');
	}

	public function testCreateShouldReturnStaticSectionWhenSimleStringPassed() {
		self::assertInstanceOf('Nano_Route_Section_Static', Nano_Route_Section::create('foo'));
	}

	public function testCreateShouldReturnRegExpSectionWhenPrefixPassed() {
		self::assertInstanceOf('Nano_Route_Section_RegExp', Nano_Route_Section::create('~foo'));
	}

	public function testCreateShouldThrowExceptionWhenEmptyLocationPassed() {
		$this->setExpectedException('Nano_Exception', 'Section location should not be empty');
		$this->section->section('');
	}

	public function testCreateShouldThrowExceptionWhenNullLocationPassed() {
		$this->setExpectedException('Nano_Exception', 'Section location should not be empty');
		$this->section->section(null);
	}

	public function testAfterCreationSectionsSizeShouldBeZero() {
		self::assertEquals(0, $this->section->getSections()->count());
	}

	public function testAddSectionShouldIncrementArraySize() {
		$this->section->section('/bar');
		self::assertEquals(1, $this->section->getSections()->count());
	}

	public function testAddSectionShouldReturnCreatedSectionInstance() {
		$section = $this->section->section('foo');
		self::assertNotSame($section, $this->section);
		self::assertEquals('foo', $section->getLocation());
	}

	public function testAddSectionShouldSetupParentInstanceForCreatedSection() {
		$section = $this->section->section('foo');
		self::assertObjectHasAttribute('parent', $section);
		self::assertSame($this->section, $section->end());
	}

	public function testEndShouldReturnParentObjectAndClearProperty() {
		$section = $this->section->section('foo');
		$parent  = $section->end();

		self::assertSame($this->section, $parent);
		self::assertObjectNotHasAttribute('parent', $section);
	}

	public function testAfterCreationRoutesSizeShouldBeZero() {
		self::assertEquals(0, $this->section->getRoutes()->count());
	}

	public function testAddMethodsShouldReturnSectionInstance() {
		self::assertSame($this->section, $this->section->get('', 'index', 'index'));
		self::assertSame($this->section, $this->section->post('', 'index', 'index'));
		self::assertSame($this->section, $this->section->add('head', '', 'index', 'index'));
	}

	public function testGetShouldPutRoutesIntoGetArrayKey() {
		$this->section->get('', 'index', 'index');
		self::assertArrayHasKey('get', $this->section->getRoutes()->getArrayCopy());
	}

	public function testPostShouldPutRoutesIntoPostArrayKey() {
		$this->section->post('', 'index', 'index');
		self::assertArrayHasKey('post', $this->section->getRoutes()->getArrayCopy());
	}

	public function testGetForShouldReturnNullWhenPrefixNotMatches() {
		self::assertNull($this->section->getFor('get', 'bar'));
	}

	public function testGetForShouldReturnNullWhenPrefixMatchesButRoutesForMethodNotExists() {
		self::assertNull($this->section->getFor('get', 'foo/bar'));
	}

	public function testShouldPassModuleParameterIntoChildSection() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testShouldPassModuleParameterIntoRoute() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testShouldPassSuffixParamInfoChildSection() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testShouldPassSuffixParamInfoRoute() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testBuildStaticLocationWithoutSuffix() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testBuildStaticLocationWithStaticSuffix() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testBuildStaticLocationWithRegexpSuffix() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testBuildRegexpLocationWithRegexpSuffix() {
		self::markTestIncomplete('Not implemented yet');
	}

	public function testGetForShouldReturnNullWhenNoMatches() {
		$this->section
			->section('/bar')
				->get('', 'index', 'index')
			->end()
			->get('/baz', 'index', 'another')
		;
		self::assertNull($this->section->getFor('get', 'foo/bar/baz'));
	}

	public function testGetForShouldCheckChildSectionsFirst() {
		$this->section
			->section('/bar')
				->get('', 'index', 'index')
			->end()
			->get('/bar', 'index', 'another')
		;

		self::assertInstanceOf('Nano_Route_Static', $this->section->getFor('get', 'foo/bar'));
		self::assertEquals('index', $this->section->getFor('get', 'foo/bar')->action());
	}

	protected function tearDown() {
		unSet($this->section);
	}

}