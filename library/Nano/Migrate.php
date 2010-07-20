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

	/**
	 * @var Nano_Db
	 */
	protected $db = null;

	public function __construct($path = null) {
		if (null === $path) {
			$path = APP . '/migrate';
		}
		$this->path = $path;
		$this->loadSteps();
	}

	/**
	 * @return Nano_Db
	 */
	public function getDb() {
		if (null === $this->db) {
			$this->setDb(Nano::db());
		}
		return $this->db;
	}

	/**
	 * @return void
	 * @param Nano_Db $value
	 */
	public function setDb(Nano_Db $value) {
		$this->db = $value;
	}

	/**
	 * @return boolean
	 * @param string $onlyVersion
	 */
	public function run($onlyVersion = null) {
		try {
			$this->logAllStart();
			$transaction = false;
			foreach ($this->getSteps() as $name => $step) { /* @var $step Nano_Migrate_Step */
				if (Nano_Migrate_Version::exists($this->getDb(), $name)) {
					continue;
				}
				if (null !== $onlyVersion && $onlyVersion !== $name) {
					continue;
				}

				$this->logStepStart($name, $step);
				$this->getDb()->beginTransaction();
				$transaction = true;
				$step->run($this->getDb());
				$this->getDb()->commit();
				Nano_Migrate_Version::add($this->getDb(), $name);
				$transaction = false;
				$this->logStepDone($name, $step);
			}
			$this->logAllDone();
		} catch (Exception $e) {
			if (true === $transaction) {
				$this->getDb()->rollBack();
			}
			throw $e;
		}
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

	protected function logAllStart() {
		if ($this->silent) {
			return;
		}
		echo 'Starting updates' . PHP_EOL;
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

	protected function logAllDone() {
		if ($this->silent) {
			return;
		}
		echo 'Done.' . PHP_EOL;
	}

}
