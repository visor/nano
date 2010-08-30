<?php

class Site_Pager_Simple extends Site_Pager_Abstract {

	const NEAR_PAGES = 2;
	const PREV_TEXT  = '&laquo; Previous';
	const NEXT_TEXT  = 'Next &raquo;';

	/**
	 * @return string
	 */
	public function __toString() {
		$result = '<ul class="pagination">';
		if ($this->currentPage > 1) {
			$result .= $this->addLink($this->currentPage - 1, self::PREV_TEXT);
		} else {
			$result .= $this->addSpan(self::PREV_TEXT, 'previous-off');
		}
		foreach ($this->pages as $page) {
			if ($this->currentPage == $page) {
				$result .= $this->addSpan($page, 'active');
			} elseif (null === $page) {
				$result .= $this->addSpan('', 'empty');
			} else {
				$result .= $this->addLink($page);
			}
		}
		if ($this->currentPage < $this->totalPages) {
			$result .= $this->addLink($this->currentPage + 1, self::NEXT_TEXT);
		} else {
			$result .= $this->addSpan(self::NEXT_TEXT, 'next-off');
		}
		$result .= '</ul>';

		return $result;
	}

	/**
	 * @return array
	 */
	protected function calculatePages() {
		$result = array();
		if (1 == $this->totalPages) {
			return $result;
		}
		if ($this->totalPages < 2 * self::NEAR_PAGES + 1) {
			return range(1, $this->totalPages);
		}
		$first       = max(1, $this->currentPage - self::NEAR_PAGES);
		$last        = min($this->totalPages, $this->currentPage + self::NEAR_PAGES);
		$spaceBefore = false;
		$spaceAfter  = false;
		$result = range($first, $last);

		if ($this->needSpaceBeforeFirst($first)) {
			$spaceBefore = true;
		} else {
			$first = 1;
		}
		if ($this->needSpaceAfterLast($last)) {
			$spaceAfter = true;
		} else {
			$last = $this->totalPages;
		}

		$result = range($first, $last);
		if ($spaceBefore) {
			array_unshift($result, 1, null);
		}
		if ($spaceAfter) {
			array_push($result, null, $this->totalPages);
		}

		return $result;
	}

	/**
	 * @return bool
	 * @param int $page
	 */
	protected function needSpaceBeforeFirst($page) {
		return ($page > 3); // $page - 1 > 2
	}

	/**
	 * @return bool
	 * @param int $page
	 */
	protected function needSpaceAfterLast($page) {
		return ($this->totalPages - $page > 2);
	}

	protected function addLink($page, $title = null) {
		return '<li><a href="' . $this->getPageUrl($page) . '">' . (null === $title ? $page : $title) . '</a></li>';
	}

	protected function addSpan($text, $class) {
		return '<li><span class="' . $class. '">' . $text . '</span></li>';
	}

}