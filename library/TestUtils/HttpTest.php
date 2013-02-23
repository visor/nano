<?php

namespace Nano\TestUtils;

class HttpTest extends \Nano\TestUtils\TestCase {

	/**
	 * @var string
	 */
	protected $testId = '';

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	/**
	 * @var \HttpRequest
	 */
	protected $request;

	protected $collectCodeCoverageInformation = false;

	/**
	 * @return string
	 * @param $location
	 */
	protected function getUrl($location) {
		return 'http://' . $this->application->config->get('web')->domain . $location;
	}

	/**
	 * @return \HttpRequest
	 */
	protected function getRequest() {
		$result = new \HttpRequest();
		$result->addCookies(array('PHPUNIT_SELENIUM_TEST_ID' => $this->testId));
		return $result;
	}

	protected function sendGet($url) {
		$this->request->setUrl($this->getUrl($url));
		$this->request->setMethod(\HttpRequest::METH_GET);
		$this->request->send();
	}

	protected function sendPost($url, array $data = array()) {
		$this->request->setUrl($this->getUrl($url));
		$this->request->setMethod(\HttpRequest::METH_POST);
		if (count($data)) {
			$this->request->setPostFields($data);
		}
		$this->request->send();
	}

	protected function assertLocation($expected) {
		self::assertEquals($expected, $this->request->getResponseInfo('effective_url'));
	}

	protected function assertResponseCode($expected) {
		self::assertEquals($expected, $this->request->getResponseCode());
	}

	protected function assertBodyEquals($expected) {
		self::assertEquals($expected, $this->request->getResponseBody());
	}

	protected function assertBodyContains($expected) {
		self::assertContains($expected, $this->request->getResponseBody());
	}

	protected function setUp() {
		if (!class_exists('HttpRequest', false)) {
			throw new \PHPUnit_Framework_SkippedTestError('Required pecl_http module not installed');
		}
		if (!isSet($GLOBALS['application'])) {
			throw new \PHPUnit_Framework_SkippedTestError('Store application instance in $GLOBALS[\'application\']');
		}

		$this->application = $GLOBALS['application'];
		$this->request     = $this->getRequest();
	}

}