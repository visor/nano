<?php

class Nano_Migrate {

	const VERSION_TABLE = 'db_migrate';

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var Nano_Migrate_Step[string]
	 */
	protected $steps = array();

	/**
	 * @var boolean
	 */
	protected $silent = false;

	public function __construct($path = null) {
		if (null === $path) {
			$path = APP . '/migrate';
		}
		$this->path = $path;
		$this->loadSteps();
	}

	/**
	 * @return boolean
	 * @param Nano_Db $db
	 */
	public function run() {
		Nano_Db::instance()->beginTransaction();
		try {
			$version = $this->getCurrentVersion();
			$run     = ('' == $version ? true : false);
			$last    = $version;
			$this->logAllStart($version);
			foreach ($this->getSteps() as $name => $step) { /* @var $step Nano_Migrate_Step */
				if ($run) {
					$last = $name;
					$this->logStepStart($name, $step);
					$step->run(Nano_Db::instance());
					$this->logStepDone($name, $step);
					continue;
				}
				if ($name == $version) {
					$run = true;
				}
			}
			$this->setCurrentVersion($last);
			$this->logAllDone($last);
		} catch (Exception $e) {
			Nano_Db::instance()->rollBack();
			throw $e;
		}

		Nano_Db::instance()->commit();
		return true;
	}

	public function silent($value = true) {
		$this->silent = $value;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return Nano_Migrate_Step[string]
	 */
	public function getSteps() {
		return $this->steps;
	}

	/**
	 * @return string
	 */
	public function getCurrentVersion() {
		return Nano_Migrate_Version::get();
	}

	/**
	 * @return void
	 * @param string $version
	 */
	protected function setCurrentVersion($version) {
		Nano_Migrate_Version::set($version);
	}

	protected function loadSteps() {
		$i = new DirectoryIterator($this->getPath());
		foreach ($i as $item) { /* @var $item DirectoryIterator */
			if (!$item->isDir() || $item->isDot() || '.' == subStr($item->getBaseName(), 0, 1)) {
				continue;
			}
			$name = $item->getBaseName();
			$this->steps[$name] = new Nano_Migrate_Step($item->getPathname());
		}
		ksort($this->steps, SORT_STRING);
	}

	protected function logAllStart($version) {
		if ($this->silent) {
			return;
		}
		echo 'Start updates from ' . $version . PHP_EOL;
	}

	protected function logStepStart($name, $step) {
		if ($this->silent) {
			return;
		}
		echo "\t" . 'runing ' . $name . '...';
	}

	protected function logStepDone($step) {
		if ($this->silent) {
			return;
		}
		echo PHP_EOL;
	}

	protected function logAllDone($version) {
		if ($this->silent) {
			return;
		}
		echo 'Updated to ' . $version . PHP_EOL;
	}

}
