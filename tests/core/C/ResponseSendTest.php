<?php

/**
 * @group core
 */
class Core_C_ResponseSendTest extends TestUtils_HttpTest {

	public function testSendingResponseBody() {
		$this->sendGet('/response/set-body');
		self::assertEquals('foobar', $this->request->getResponseBody());
	}

	public function testRenderingBody() {
		$this->sendGet('/response/render-body');
		self::assertEquals('foo-bar', $this->request->getResponseBody());
	}

	public function testHeaders() {
		$this->sendGet('/response/header');

		self::assertArrayHasKey('X-Test-Controller', $this->request->getResponseMessage()->getHeaders());
		self::assertEquals('response', $this->request->getResponseHeader('X-Test-Controller'));
	}

}