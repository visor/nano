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
		$section = $this->section
			->module('some')
			->section('/bar')
		;
		self::assertEquals($this->section->getModule(), $section->getModule());
	}

	public function testShouldPassModuleParameterIntoRoute() {
		$section = $this->section
			->module('some')
			->section('/bar')
				->get('', 'index', 'index')
		;
		self::assertEquals($this->section->getModule(), $section->getRoutes()->offsetGet('get')->offsetGet(0)->module());
	}

	public function testShouldPassSuffixParamInfoChildSection() {
		$section = $this->section
			->suffix('.html')
			->section('/bar')
		;
		self::assertEquals($this->section->getSuffix(), $section->getSuffix());
	}

	public function testShouldPassSuffixParamInfoRoute() {
		$section = $this->section
			->suffix('.html')
			->section('/bar')
				->get('index', 'index', 'index')
		;
		self::assertEquals('index.html', $section->getRoutes()->offsetGet('get')->offsetGet(0)->location());
	}

	public function testBuildStaticLocationWithoutSuffix() {
		$this->section->get('', 'index', 'index');
		self::assertInstanceOf('Nano_Route_Static', $this->section->getRoutes()->offsetGet('get')->offsetGet(0));
		self::assertEquals('', $this->section->getRoutes()->offsetGet('get')->offsetGet(0)->location());
	}

	public function testBuildStaticLocationWithStaticSuffix() {
		$this->section->suffix('.html')->get('index', 'index', 'index');
		self::assertInstanceOf('Nano_Route_Static', $this->section->getRoutes()->offsetGet('get')->offsetGet(0));
		self::assertEquals('index.html', $this->section->getRoutes()->offsetGet('get')->offsetGet(0)->location());
	}

	public function testBuildStaticLocationWithRegexpSuffix() {
		$this->section->suffix('~\.(html|rss)')->get('index', 'index', 'index');
		self::assertInstanceOf('Nano_Route_RegExp', $this->section->getRoutes()->offsetGet('get')->offsetGet(0));
		self::assertEquals('/^index\.(html|rss)$/i', $this->section->getRoutes()->offsetGet('get')->offsetGet(0)->location());
	}

	public function testBuildRegexpLocationWithRegexpSuffix() {
		$this->section->suffix('~\.(html|rss)')->get('~(index|home)', 'index', 'index');
		self::assertInstanceOf('Nano_Route_RegExp', $this->section->getRoutes()->offsetGet('get')->offsetGet(0));
		self::assertEquals('/^(index|home)\.(html|rss)$/i', $this->section->getRoutes()->offsetGet('get')->offsetGet(0)->location());
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