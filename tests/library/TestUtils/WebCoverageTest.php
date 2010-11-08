<?php

/**
 * @group test-utils
 */
class TestUtils_WebCoverageTest extends TestUtils_WebTest {

	public function testTestIndex() {
		$this->pageUrl = '/test-index';
		$this->openPage();
		self::assertContains('application' . DS .'views' . DS . 'test' . DS .'index.php', $this->getBodyText());
	}

	public function testTestTest() {
		$this->pageUrl = '/test-test';
		$this->openPage();
		self::assertEquals('test view rendered', $this->getBodyText());
	}

	public function testTestVar() {
		$this->pageUrl = '/test-var';
		$this->openPage();
		self::assertEquals('Some title. 01=foo.03=bar.', $this->getBodyText());
	}

}
