<?php

/**
 * @group core
 */
class Core_C_TestResponseTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var \Nano\Controller\Response
	 */
	protected $response;

	protected function setUp() {
		$this->response = new \Nano\Controller\Response\Test();
		$this->response->addHeader('X-Header', 'value');
	}

	public function testSendHeaderShouldDoNothing() {
		$this->response->sendHeaders();
		self::assertEquals(null, $this->getActualOutput());
	}

	public function testSendBodyShouldDoNothing() {
		$this->response->sendBody();
		self::assertEquals(null, $this->getActualOutput());
	}

	public function testSendShouldDoNothing() {
		$this->response->send();
		self::assertEquals(null, $this->getActualOutput());
	}

	protected function tearDown() {
		unSet($this->response);
	}

}