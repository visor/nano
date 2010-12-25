<?php

class Nano_Db_Log {

	/**
	 * @var string
	 */
	protected $lastQuery     = null;

	/**
	 * @var double
	 */
	protected $lastQueryTime = null;

	/**
	 * @var array
	 */
	protected $queries = array();

	/**
	 * @var string
	 */
	protected $logFile = null;

	/**
	 * @return bool
	 */
	public function enabled() {
		return false !== $this->getLogFile();
	}

	/**
	 * @return void
	 * @param string $query
	 * @param string|double $time
	 */
	public function append($query, $time, $error = false) {
		if (!$this->enabled()) {
			return;
		}
		$this->lastQuery     = (string)$query;
		$this->lastQueryTime = $time;
		$this->queries[]     = array(
			  'time'  => $this->lastQueryTime
			, 'query' => $this->lastQuery
		);
		if ($error) {
			error_log('[' . date('Y.m.d H:i:s') . '] ERROR ' . $query . PHP_EOL, 3, $this->getLogFile());
			return;
		}
		error_log('[' . date('Y.m.d H:i:s') . '] ' . sprintf('%03.010f %s', $this->lastQueryTime, $this->lastQuery) . PHP_EOL, 3, $this->getLogFile());
	}

	/**
	 * @return string
	 */
	public function getLastQuery() {
		return $this->lastQuery;
	}

	/**
	 * @return double
	 */
	public function getLastQueryTime() {
		return $this->lastQueryTime;
	}

	/**
	 * @return int
	 */
	public function count() {
		return count($this->queries);
	}

	/**
	 * @return array
	 */
	public function queries() {
		return $this->queries;
	}

	/**
	 * @return void
	 */
	public function clean() {
		$this->lastQuery     = null;
		$this->lastQueryTime = null;
		$this->queries       = array();
	}

	/**
	 * @return string
	 */
	protected function getLogFile() {
		if (null === $this->logFile) {
			$config = Nano_Db::getConfig(Nano::db()->getName());
			if (isset($config['log']) && false !== $config['log']) {
				$this->logFile = $config['log'];
			} else {
				$this->logFile = false;
			}
		}
		return $this->logFile;
	}

}