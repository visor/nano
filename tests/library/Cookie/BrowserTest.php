<?php

/**
 * @group cookie
 */
class Library_Cookie_BrowserTest extends TestUtils_WebTest {

	public function testSetCookieInBrowser() {
		$this->open($this->url('/cookie/set'));
		$this->clickAndWait('id=gotoView');

		self::assertContains('name1=value1', $this->getText('id=cookie-value'));
		self::assertContains('name2=value2', $this->getText('id=cookie-value'));

		self::assertContains('name1 = [value1];', $this->getText('id=values'));
		self::assertContains('name2 = [value2];', $this->getText('id=values'));
	}

	public function testSetHttpOnlyCookieInBrowser() {
		$this->open($this->url('/cookie/set?http=1'));
		$this->clickAndWait('id=gotoView');

		self::assertNotContains('name1', $this->getText('id=cookie-value'));
		self::assertNotContains('name2', $this->getText('id=cookie-value'));

		self::assertContains('name1 = [value1];', $this->getText('id=values'));
		self::assertContains('name2 = [value2];', $this->getText('id=values'));
	}

	public function testErasingCookieInBrowser() {
		$this->open($this->url('/cookie/set'));
		$this->clickAndWait('id=gotoErase');
		$this->clickAndWait('id=gotoView');

		self::assertContains('name1=value1', $this->getText('id=cookie-value'));
		self::assertNotContains('name2', $this->getText('id=cookie-value'));

		self::assertContains('name1 = [value1];', $this->getText('id=values'));
		self::assertContains('name2 = [];', $this->getText('id=values'));
	}

	public function testEraseHttpOnlyCookieInBrowser() {
		$this->open($this->url('/cookie/set?http=1'));
		$this->clickAndWait('id=gotoEraseHttp');
		$this->clickAndWait('id=gotoView');

		self::assertNotContains('name1', $this->getText('id=cookie-value'));
		self::assertNotContains('name2', $this->getText('id=cookie-value'));

		self::assertContains('name1 = [value1];', $this->getText('id=values'));
		self::assertContains('name2 = [];', $this->getText('id=values'));
	}

}