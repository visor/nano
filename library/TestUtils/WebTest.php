<?php

namespace Nano\TestUtils;

/**
 * @property \Nano\TestUtils\Mixin\Connect $connection
 * @property \Nano\TestUtils\Mixin\Files $files
 */
class WebTest extends \PHPUnit_Extensions_SeleniumTestCase {

	/**
	 * @var string
	 */
	protected $pageUrl = '';

	/**
	 * @var \Nano\Application
	 */
	protected $application;

	protected function screenshot($suffix = null, $screen = false) {
		$baseName       = str_replace('\\', '-', get_class($this));
		$folder         = $GLOBALS['application']->rootDir . '/tests/screenshots/';
		$screenFileName = $folder . 'screen_' . $baseName . '_' . $this->getName(false);
		$windowFileName = $folder . $baseName . '_' . $this->getName(false);
		if ($suffix) {
			$screenFileName .= '_' . $suffix;
			$windowFileName .= '_' . $suffix;
		}
		$screenFileName .= '.png';
		$windowFileName .= '.png';
		$this->captureEntirePageScreenshot($windowFileName);
		if ($screen) {
			$this->captureScreenshot($screenFileName);
		}
	}

	protected function setUp() {
		if (!defined('SELENIUM_ENABLE')) {
			$this->markTestSkipped('Selenium disabled');
		}
		if (!isSet($GLOBALS['application'])) {
			$this->markTestSkipped('Store tested application instance in $GLOBALS array');
		}

		$this->addMixin('files', '\Nano\TestUtils\Mixin\Files');
		$this->addMixin('connection', '\Nano\TestUtils\Mixin\Connect');
		$this->checkConnection();

		$this->application       = $GLOBALS['application'];
		$this->coverageScriptUrl = $this->url('/');

		$this->setUpData();
		$this->setBrowserUrl($this->url('/'));
		$this->start();
		$this->windowMaximize();
		$this->open($this->url('/'));
		$this->deleteAllVisibleCookies();
		$this->createCookie('PHPUNIT_SELENIUM_TEST_ID=' . $this->testId, 'path=/');
		$this->openPage();
	}

	protected function setUpData() {}

	/**
	 * @return string
	 * @param string $path
	 *
	 */
	protected function url($path) {
		return 'http://' . $this->application->config->get('web')->domain . $this->application->config->get('web')->url . $path;
	}

	/**
	 * @return void
	 */
	protected function openPage() {
		if ($this->pageUrl) {
			$this->openAndWait($this->url($this->pageUrl));
		}
	}

	/**
	 * @return void
	 * @param string $expected
	 * @param string $message
	 */
	protected function assertTitleEquals($expected, $message = '') {
		self::assertEquals($expected, $this->getTitle(), $message);
	}

	/**
	 * @return void
	 * @param string $expected
	 * @param string $message
	 */
	protected function assertLocation($expected, $message = '') {
		self::assertEquals('http://' . $this->application->config->get('web')->domain . $expected, $this->getLocation(), $message = '');
	}

	/**
	 * @return void
	 * @param int $timeout
	 */
	protected function waitForJQueryAjax($timeout = 5000) {
		$this->waitForCondition('selenium.browserbot.getCurrentWindow().jQuery.active == 0;', $timeout);
	}

	/**
	 * @return void
	 * @param string $selector
	 * @param string $value
	 */
	protected function setValue($selector, $value) {
		$this->type('css=' . $selector, $value);
	}

	protected function addMixin($property, $className) {
		if (isset($this->$property)) {
			throw new \InvalidArgumentException('$property');
		}

		$class = new \ReflectionClass($className);
		if (!$class->isSubclassOf('\Nano\TestUtils\Mixin')) {
			throw new \InvalidArgumentException('$className');
		}
		if (!$class->isInstantiable()) {
			throw new \InvalidArgumentException('$className');
		}

		$this->$property = $class->newInstance();
	}

	protected function checkConnection() {
		$this->connection->check(self::$browsers[0]['host'], self::$browsers[0]['port'], 'Selenium RC not running on %s:%d.');
	}

}