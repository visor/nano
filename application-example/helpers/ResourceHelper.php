<?php

class ResourceHelper extends Nano_Helper {

	protected static $lastServer = 0;

	/**
	 * @return ResourceHelper
	 */
	public function invoke() {
		return $this;
	}

	/**
	 * @return string
	 * @param string $location
	 */
	public function cdn($location) {
		return $this->getNextServer() . $location;
	}

	/**
	 * @return string[]
	 */
	protected function getServers() {
		return Nano::config('cdn')->servers;
	}

	/**
	 * @return string
	 */
	protected function getNextServer() {
		$servers = $this->getServers();
		$count   = count($servers);
		if (1 > $count) {
			return '';
		}
		$result  = $servers[self::$lastServer];
		++self::$lastServer;
		if ($count == self::$lastServer) {
			self::$lastServer = 0;
		}
		return $result;
	}

}