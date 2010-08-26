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
abstract class Sortable_DbObject extends Article_DbObject {

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
	 * @return Article_DbObject
	 * @param string $title
	 * @param string $body
	 */
	public static function createNew($title) {
		return static::create(get_called_class(), array('title' => $title));
	}

	/**
	 * @return int
	 */
	public function id() {
		return $this->sortable_id;
	}

	public function moveTop() {
	}

	public function moveUp() {
	}

	public function moveDown() {
	}

	public function moveBottom() {
	}

}