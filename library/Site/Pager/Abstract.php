<?php

abstract class Site_Pager_Abstract {

	/**
	 * @var string
	 */
	protected $urlTemplate;

	/**
	 * @var int
	 */
	protected $currentPage;

	/**
	 * @var int
	 */
	protected $totalItems;

	/**
	 * @var int
	 */
	protected $itemsPerPage;

	/**
	 * @var int
	 */
	protected $totalPages;

	/**
	 * @var int
	 */
	protected $offset;

	/**
	 * @var array
	 */
	protected $pages = array();

	public function __construct($urlTemplate, $currentPage, $totalItems, $itemsPerPage = 20) {
		$this->urlTemplate  = (string)$urlTemplate;
		$this->currentPage  = (int)$currentPage;
		$this->totalItems   = (int)$totalItems;
		$this->itemsPerPage = (int)$itemsPerPage;
		$this->init();
	}

	/**
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->currentPage;
	}

	/**
	 * @return int
	 */
	public function getTotalItems() {
		return $this->totalItems;
	}

	/**
	 * @return int
	 */
	public function getTotalPages() {
		return $this->totalPages;
	}

	/**
	 * @return int
	 */
	public function getItemsPerPage() {
		return $this->itemsPerPage;
	}

	public function getOffset() {
		return $this->offset;
	}

	public function getPageUrl($page) {
		return sprintf($this->urlTemplate, $this->fixPage($page));
	}

	/**
	 * @return string
	 */
	abstract public function __toString();

	/**
	 * @return array
	 */
	abstract protected function calculatePages();

	protected function init() {
		$this->totalPages  = ceil($this->totalItems / $this->itemsPerPage);
		$this->currentPage = $this->fixPage($this->currentPage);
		$this->offset      = ($this->currentPage - 1) * $this->itemsPerPage;
		$this->pages       = $this->calculatePages();
	}

	protected function fixPage($page) {
		if ($page < 1) {
			return 1;
		}
		if ($page > $this->totalPages && $page > 1) {
			return $this->totalPages;
		}
		return $page;
	}

}