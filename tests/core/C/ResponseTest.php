<?php

/**
 * @group core
 */
class Core_C_ResponseTest extends TestUtils_TestCase {

	/**
	 * @var Nano_C_Response
	 */
	protected $response;

	protected function setUp() {
		$application = new Application();
		$application
			->withRootDir($GLOBALS['application']->rootDir)
			->withConfigurationFormat('php')
			->configure()
		;
		$this->response = new Nano_C_Response($application);
	}

	public function testSetMethodsShouldReturnSelfInstance() {
		self::assertInstanceOf('Nano_C_Response', $this->response->setBody(''));
		self::assertInstanceOf('Nano_C_Response', $this->response->appendToBody(''));
		self::assertInstanceOf('Nano_C_Response', $this->response->setVersion(''));
		self::assertInstanceOf('Nano_C_Response', $this->response->setStatus(1));
		self::assertInstanceOf('Nano_C_Response', $this->response->addHeader('', ''));
		self::assertInstanceOf('Nano_C_Response', $this->response->addHeaders(array()));
	}

	public function testDefaultStatus() {
		self::assertEquals(Nano_C_Response::STATUS_DEFAULT, $this->response->getStatus());
	}

	public function testDefaultVersion() {
		self::assertEquals(Nano_C_Response::VERSION_10, $this->response->getVersion());
	}

	public function testSettingStatus() {
		$this->response->setStatus(401);
		self::assertEquals(401, $this->getObjectProperty($this->response, 'status'));
	}

	public function testGettingStatus() {
		self::assertEquals(Nano_C_Response::STATUS_DEFAULT, $this->response->getStatus());
	}

	public function testAddingHeader() {
		$this->response->addHeader('some', 'value');
		$header = self::getObjectProperty($this->response, 'headers');
		self::assertInstanceOf('ArrayObject', $header);
		/* @var ArrayObject $header */
		self::assertTrue($header->offsetExists('some'));
		self::assertEquals('value', $header->offsetGet('some'));
	}

	public function testAddingHeadersAsArray() {
		$expected = array(
			'some'  => 'value'
			, 'foo' => 'bar'
		);
		$this->response->addHeaders($expected);

		$actual = self::getObjectProperty($this->response, 'headers');
		self::assertInstanceOf('ArrayObject', $actual);
		/* @var ArrayObject $actual */
		self::assertEquals(count($expected), $actual->count());
		self::assertEquals($expected, $actual->getArrayCopy());
	}

	public function testCheckingHeadersExists() {
		self::assertFalse($this->response->hasHeader('name'));
		$this->response->addHeader('name', 'value');
		self::assertTrue($this->response->hasHeader('name'));
	}

	public function testGettingHeaders() {
		$this->testAddingHeadersAsArray();
		self::assertEquals('value', $this->response->getHeader('some'));
		self::assertEquals('bar', $this->response->getHeader('foo'));
		self::assertNull($this->response->getHeader('some-value'));
	}

	public function testHasBodyShouldReturnFalseWhenBodyIsNull() {
		self::assertFalse($this->response->hasBody());
		$this->response->setBody('');
		self::assertTrue($this->response->hasBody());
		$this->response->setBody(null);
		self::assertFalse($this->response->hasBody());
	}

	public function testModifiingBody() {
		self::assertNull($this->response->getBody());
		$this->response->appendToBody('foo');
		self::assertEquals('foo', $this->response->getBody());
		$this->response->appendToBody('bar');
		self::assertEquals('foobar', $this->response->getBody());
		$this->response->setBody('baz');
		self::assertEquals('baz', $this->response->getBody());
	}

	protected function tearDown() {
		unSet($this->response);
	}

}