<?php

/**
 * @group cookie
 */
class Library_Cookie_BrowserTest extends \Nano\TestUtils\HttpTest {

	public function testSetCookieInBrowser() {
		$this->request->enableCookies();
		$this->sendGet('/cookie/set');
		$this->sendGet('/cookie/view');

		self::assertContains('\'name1\' => \'value1\'', $this->request->getResponseBody());
		self::assertContains('\'name2\' => \'value2\'', $this->request->getResponseBody());

		self::assertContains('name1 = [value1];', $this->request->getResponseBody());
		self::assertContains('name2 = [value2];', $this->request->getResponseBody());
	}

	public function testSetHttpOnlyCookieInBrowser() {
		$this->request->enableCookies();
		$this->sendGet('/cookie/set?http=1');
		$this->sendGet('/cookie/view');

		self::assertContains('\'name1\' => \'value1\'', $this->request->getResponseBody());
		self::assertContains('\'name2\' => \'value2\'', $this->request->getResponseBody());

		self::assertContains('name1 = [value1];', $this->request->getResponseBody());
		self::assertContains('name2 = [value2];', $this->request->getResponseBody());
	}

	public function testErasingCookieInBrowser() {
		$this->request->enableCookies();
		$this->sendGet('/cookie/set');
		$this->sendGet('/cookie/erase');
		$this->sendGet('/cookie/view');

		self::assertContains('\'name1\' => \'value1\'', $this->request->getResponseBody());
		self::assertNotContains('\'name2\' => \'value2\'', $this->request->getResponseBody());

		self::assertContains('name1 = [value1];', $this->request->getResponseBody());
		self::assertContains('name2 = [];', $this->request->getResponseBody());
	}

	public function testEraseHttpOnlyCookieInBrowser() {
		$this->request->enableCookies();
		$this->sendGet('/cookie/set?http=1');
		$this->sendGet('/cookie/erase?http=1');
		$this->sendGet('/cookie/view');

		self::assertContains('\'name1\' => \'value1\'', $this->request->getResponseBody());
		self::assertNotContains('\'name2\' => \'value2\'', $this->request->getResponseBody());

		self::assertContains('name1 = [value1];', $this->request->getResponseBody());
		self::assertContains('name2 = [];', $this->request->getResponseBody());
	}

}