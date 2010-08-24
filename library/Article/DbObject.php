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
abstract class Article_DbObject extends Nano_DbObject implements Nano_Editable {

	protected $primaryKey = array('article_id');

	protected $properties = array(
		  'article_id'
		, 'title'
		, 'announce'
		, 'body'
		, 'created'
		, 'modified'
		, 'published'
		, 'active'
	);

	/**
	 * @return Article_DbObject
	 * @param string $title
	 * @param string $body
	 */
	public static function createNew($title, $body) {
		return static::create(get_called_class(), array(
			  'title' => $title
			, 'body' => $body
		));
	}

	/**
	 * @return Article_DbObject
	 * @param int $id
	 */
	public static function get($id) {
		$class = get_called_class();
		return new $class($id);
	}

	/**
	 * PDOStatement
	 * @param Date $date
	 * @param int $page
	 * @param int $itemsPerPage
	 */
	public static function getPublished(Date $date = null, $page = null, $itemsPerPage = null) {
		if (null === $date) {
			$date = Date::now();
		}
		$query = sql::select('*')->from(static::NAME)
			->where(sql::expr()
				->add('active', '=', '1')
				->and('published', '<=', $date->toSql())
			)
			->order('published desc')
			->limitPage($page, $itemsPerPage)
		;
		return static::fetchThis($query);
	}

	/**
	 * PDOStatement
	 * @param int $page
	 * @param int $itemsPerPage
	 */
	public static function getAll($page = null, $itemsPerPage = null) {
		$query = sql::select('*')->from(static::NAME)
			->order('published desc')
			->limitPage($page, $itemsPerPage)
		;
		return static::fetchThis($query);
	}

	/**
	 * @return int
	 * @param Date $date
	 */
	public static function countPublished(Date $date = null) {
		if (null === $date) {
			$date = Date::now();
		}
		$query = sql::select('count(*)')->from(static::NAME)
			->where(sql::expr()
				->add('active', '=', '1')
				->and('published', '<=', $date->toSql())
			)
		;
		return self::db()->getCell($query->toString());
	}

	/**
	 * @return int
	 * @param Date $date
	 */
	public static function countAll() {
		return self::db()->getCell(sql::select('count(*)')->from(static::NAME)->toString());
	}

	/**
	 * @return int
	 */
	public function id() {
		return $this->article_id;
	}

	/**
	 * @param boolean $value
	 * @return Article_DbObject
	 */
	public function setActivity($value) {
		$this->active = ($value ? 1 : 0);
		return $this;
	}

	public function publish(Date $date = null) {
		if (null === $date) {
			$date = Date::now();
		}
		$this->setActivity(true);
		$this->published = $date->toSql();
		return $this;
	}

	public function unpublish() {
		$this->setActivity(false);
		$this->published = null;
		return $this;
	}

	/**
	 * @return Nano_Editable
	 * @param array $data
	 */
	public function populate(array $data) {
		foreach ($data as $property => $value) {
			$this->__set($property, $value);
		}
	}

	/**
	 * @return array
	 */
	public function toForm() {
		return $this->data;
	}

	protected function beforeSave() {
		$this->modified = Date::now()->toSql();
		if ($this->isNew()) {
			$this->created = $this->modified;
			if (null === $this->active) {
				$this->setActivity(false);
			}
		}
	}

}