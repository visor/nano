<?php

class PagerHelper extends Nano_Helper {

	/**
	 * @var int
	 */
	protected $total = 0;

	/**
	 * @var int
	 */
	protected $perPage = 20;

	/**
	 * @var int
	 */
	protected $current = 1;

	/**
	 * @var int
	 */
	protected $offset  = 0;

	/**
	 * @var int
	 */
	protected $totalPages = 0;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @return PagerHelper
	 * @param int $page
	 * @param int $perPage
	 * @param int $total
	 */
	public function invoke($page = null, $perPager = null, $total = null) {
		$result = new self();
		$result->init($page, $perPager, $total);
		return $result;
	}

	/**
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->current;
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->perPage;
	}

	/**
	 * @return int
	 */
	public function getTotalPages() {
		return $this->totalPages;
	}

	public function __toString() {
		return '';
	}

	/**
	 * @return void
	 * @param int $page
	 * @param int $perPage
	 * @param int $total
	 */
	protected function init($page, $perPage, $total) {
		$this->name    = $page;
		$this->current = isset($_REQUEST[$page]) ? $_REQUEST[$page] : 1;
		$this->perPage = $perPage;
		$this->total   = $total;
		$this->generateVariables();
	}

	/**
	 * @return void
	 */
	protected function generateVariables() {
		$this->totalPages = ceil($this->total / $this->perPage);
		if ($this->current > $this->totalPages) {
			$this->current = $this->totalPages;
		}
		if ($this->current < 1) {
			$this->current = 1;
		}
		$this->offset = ($this->current - 1) * $this->perPage;
	}

}