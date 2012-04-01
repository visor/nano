<?php

class TestUtils_HttpTest extends TestUtils_TestCase {

	/**
	 * @var string
	 */
	protected $testId = '';

	/**
	 * @var Application
	 */
	protected $application;

	/**
	 * @var HttpRequest
	 */
	protected $request;

	protected $collectCodeCoverageInformation = false;

	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->testId = md5(uniqid(rand(), TRUE));
	}

	public function run(PHPUnit_Framework_TestResult $result = NULL) {
		if (null === $result) {
			$result = $this->createResult();
		}

		$this->setTestResultObject($result);
		$this->collectCodeCoverageInformation = $result->getCollectCodeCoverageInformation();
		$result->run($this);
		if ($this->collectCodeCoverageInformation) {
			$result->getCodeCoverage()->append($this->getCodeCoverage(), $this);
		}
		return $result;
	}

	/**
	 * @return string
	 * @param $location
	 */
	protected function getUrl($location) {
		return 'http://' . $this->application->config->get('web')->domain . $location;
	}

	/**
	 * @return HttpRequest
	 */
	protected function getRequest() {
		$result = new HttpRequest();
		$result->addCookies(array('PHPUNIT_SELENIUM_TEST_ID' => $this->testId));
		return $result;
	}

	protected function sendGet($url) {
		$this->request->setUrl($this->getUrl($url));
		$this->request->setMethod(HttpRequest::METH_GET);
		$this->request->send();
	}

	protected function setUp() {
		if (!class_exists('HttpRequest')) {
			self::markTestSkipped('Please install pecl_http extension');
		}
		if (!isSet($GLOBALS['application'])) {
			$this->markTestSkipped('Store tested application instance in $GLOBALS array');
		}

		$this->application = $GLOBALS['application'];
		$this->request = $this->getRequest();
	}

//	protected function tearDown() {
//		unSet($this->application, $this->request);
//	}

	/**
	 * @return array
	 * @throws Exception
	 */
	protected function getCodeCoverage() {
		$url    = $this->getUrl('/?PHPUNIT_SELENIUM_TEST_ID=' . $this->testId);
		$buffer = @file_get_contents($url);

		if (false === $buffer) {
			return array();
		}

		$coverageData = unSerialize($buffer);
		if (is_array($coverageData)) {
			return $this->matchLocalAndRemotePaths($coverageData);
		}

		throw new Exception('Empty or invalid code coverage data received from url "' . $url . '"');
	}

	/**
	 * @param  array $coverage
	 * @return array
	 * @author Mattis Stordalen Flister <mattis@xait.no>
	 */
	protected function matchLocalAndRemotePaths(array $coverage) {
		$coverageWithLocalPaths = array();

		foreach ($coverage as $originalRemotePath => $data) {
			$remotePath = $originalRemotePath;
			$separator  = $this->findDirectorySeparator($remotePath);

			while (!($localpath = PHPUnit_Util_Filesystem::fileExistsInIncludePath($remotePath)) && strpos($remotePath, $separator) !== FALSE) {
				$remotePath = substr($remotePath, strpos($remotePath, $separator) + 1);
			}

			if ($localpath && md5_file($localpath) == $data['md5']) {
				$coverageWithLocalPaths[$localpath] = $data['coverage'];
			}
		}

		return $coverageWithLocalPaths;
	}

	/**
	 * @param  string $path
	 * @return string
	 * @author Mattis Stordalen Flister <mattis@xait.no>
	 */
	protected function findDirectorySeparator($path) {
		if (strpos($path, '/') !== FALSE) {
			return '/';
		}

		return '\\';
	}

}