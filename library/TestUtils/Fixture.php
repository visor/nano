<?php

class TestUtils_Fixture {

	const FIXTURE_DIR    = 'fixtures';
	const FIXTURE_PREFIX = 'TestFixture';
	const FIXTURE_METHOD = 'dataFor';

	/**
	 * @var TestUtils_Fixture
	 */
	private static $instance = null;

	/**
	 * @var TestUtils_Fixture[]
	 */
	private static $fixtures = array();

	/**
	 * @var string
	 */
	protected $activeRecord = null;

	/**
	 * @var int
	 */
	protected $index = 0;

	/**
	 * @var array
	 */
	protected $records = array();

	/**
	 * @return TestUtils_Fixture
	 */
	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return TestUtils_Fixture
	 */
	public function __call($method, array $argumetns) {
		$type = ucFirst($method);
		if (!isset(self::$fixtures[$type])) {
			self::$fixtures[$type] = $this->loadFixture($type);
		}
		if (isset($argumetns[0])) {
			$fixture = self::$fixtures[$type];
			$fixture->load($argumetns[0], isset($argumetns[1]) ? $argumetns[1] : 1);
			return $this;
		}
		return self::$fixtures[$type];
	}

	/**
	 * @return ActiveRecord
	 * @param string $type
	 * @param int $index
	 */
	public function get($type, $index = 0) {
		return isset($this->records[$index]) ? $this->records[$index] : null;
	}

	public function last() {
		return $this->get(null, $this->index - 1);
	}

	/**
	 * @param string $type
	 * @return ActiveRecord
	 */
	public function getNew($type) {
		$this->load($type, 1);
		return $this->get($type, $this->index - 1);
	}

	/**
	 * @param string $type
	 * @param array $data
	 * @return ActiveRecord
	 */
	public function getCustom($type, array $data) {
		$this->createRecord($type, $this->index, $data);
		$this->index++;
		return $this->get($type, $this->index - 1);
	}

	/**
	 * @return TestUtils_Fixture
	 */
	public function resetAll() {
		foreach (self::$fixtures as $fixture) {
			$fixture->resetIndex();
		}
		return $this;
	}

	/**
	 * @return TestUtils_Fixture
	 */
	public function resetIndex() {
		$this->index   = 0;
		$this->records = array();
		return $this;
	}

	protected function load($type, $count = 1) {
		$method = self::FIXTURE_METHOD . $this->typeToName($type);
		if (!method_exists($this, $method)) {
			PHPUnit_Framework_Assert::fail('Unknown fixture type: ' . $type);
		}
		$max = $this->index + $count;
		for (; $this->index < $max; ++$this->index) {
			$this->createRecord($type, $this->index, $this->$method($this->index));
		}
	}

	/**
	 * @return TestUtils_Fixture
	 * @param string $type
	 */
	protected function loadFixture($type) {
		$class = self::FIXTURE_PREFIX . $this->typeToName($type);
		if (class_exists($class, false)) {
			return new $class();
		}

		$fileName = $this->getFixtureFileName($type);
		if (!file_exists($fileName)) {
			PHPUnit_Framework_Assert::fail('Unknown fixture: ' . $type);
		}
		if (!include($fileName)) {
			PHPUnit_Framework_Assert::fail('Fixture file load error: ' . $type);
		}
		return new $class();
	}

	/**
	 * @return ActiveRecord
	 * @param string $type
	 * @param int $index
	 * @param array $data
	 */
	protected function createRecord($type, $index, array $data) {
		$class  = $this->activeRecord;
		$record = new $class($data); /** @var ActiveRecord $record */
		$record->save();

		$this->records[$index] = $record;
		return $record;
	}

	/**
	 * @return string
	 * @param string $type
	 */
	protected function getFixtureFileName($type) {
		return TESTS . DS . self::FIXTURE_DIR . DS . $type . '.php';
	}

	/**
	 * @return string
	 * @param  $type
	 */
	protected function typeToName($type) {
		return Strings::typeToClassName($type);
	}

}