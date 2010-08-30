<?php

/**
 * @property int $article_id
 * @property string $title
 * @property string $announce
 * @property string $body
 * @property Date $created
 * @property Date $modified
 * @property Date $published
 * @property booolean $active
 */
abstract class Site_Sortable_DbObject extends Site_Article_DbObject {

	protected $primaryKey = array('sortable_id');

	protected $properties = array(
		  'sortable_id'
		, 'title'
		, 'created'
		, 'modified'
		, 'published'
		, 'active'
		, 'position'
	);

	/**
	 * @return int
	 */
	public function id() {
		return $this->sortable_id;
	}

	public function moveTop() {
		return true;
	}

	public function moveUp() {
		return true;
	}

	public function moveDown() {
		return true;
	}

	public function moveBottom() {
		return true;
	}

}