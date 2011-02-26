<?php

abstract class CachedContent {

	public function getContent() {
		$key    = $this->getKey();
		$result = Cache::get($key);
		if (null === $result) {
			$result = $this->buildContent();
			Cache::set($key, $result, $this->getLifeTime(), $this->getTags());
		}
		return $result;
	}

	/**
	 * @return string
	 */
	abstract protected function getKey();

	/**
	 * @return mixed
	 */
	abstract protected function buildContent();

	/**
	 * @return string[]
	 */
	protected function getTags() {
		return array();
	}

	/**
	 * @return int
	 */
	protected function getLifeTime() {
		return Date::ONE_DAY;
	}

}