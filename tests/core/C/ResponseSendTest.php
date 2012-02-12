<?php

/**
 * @group core
 */
class Core_C_ResponseSendTest extends TestUtils_WebTest {

	/**
	 * @var boolean
	 */
	protected $clearDbAfterTest = false;

	/**
	 * @var boolean
	 */
	protected $clearLogAfterTest = false;

	public function testSendingResponseBody() {
		$this->open($this->url('/response/set-body'));
		self::assertEquals('foobar', $this->getBodyText());
	}

	public function testRenderingBody() {
		$this->open($this->url('/response/render-body'));
		self::assertEquals('foo-bar', $this->getBodyText());
	}

	public function testHeaders() {
		if (!class_exists('HttpRequest')) {
			self::markTestSkipped('Please install pecl_http extension');
		}

		$request = new HttpRequest($this->url('/response/header'));
		$request->send();

		self::assertArrayHasKey('X-Test-Controller', $request->getResponseMessage()->getHeaders());
		self::assertEquals('response', $request->getResponseHeader('X-Test-Controller'));

		//this is to collect coverage only
		$this->open($this->url('/response/header'));
	}

}