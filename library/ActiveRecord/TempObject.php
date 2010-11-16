<?php

class ActiveRecord_TempObject {

	const STORAGE_NAME     = 'temp-object';
	const KEY_ID           = 'id';
	const KEY_RECORD       = 'record';
	const KEY_CHILD_FIELD  = 'child';
	const KEY_PARENT_FIELD = 'parent';

	/**
	 * @var array
	 */
	private static $storage = null;

	/**
	 * @var ActiveRecord
	 */
	protected $record;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var ActiveRecord[]
	 */
	protected $childs = array();

	/**
	 * @param ActiveRecord $record
	 */
	public function __construct(ActiveRecord $record) {
		self::storage();
		$this->record = $record;
		$this->id     = self::nextId();
		self::$storage[self::KEY_RECORD][$this->id] = $this;
		self::updateStorage();
	}

	/**
	 * @return ActiveRecord_TempObject
	 */
	public static function get($id) {
		$storage = self::storage();
		if (isset($storage[self::KEY_RECORD][$id])) {
			return $storage[self::KEY_RECORD][$id];
		}
		return null;
	}

	public function id() {
		return $this->id;
	}

	/**
	 * @return ActiveRecord
	 */
	public function record() {
		return $this->record;
	}

	/**
	 * @return void
	 * @param mixed $object
	 */
	public function addChild($object, $childField, $parentField) {
		if (!(($object instanceof ActiveRecord) || ($object instanceof ActiveRecord_TempObject))) {
			throw new InvalidArgumentException('Child should be instance of ActiveRecord_TempObject or ActiveRecord');
		}
		$this->childs[] = array(
			  self::KEY_RECORD       => $object
			, self::KEY_CHILD_FIELD  => $childField
			, self::KEY_PARENT_FIELD => $parentField
		);
	}

	/**
	 * @return void
	 */
	public function save() {
		self::storage();
		$this->record()->save();
		foreach ($this->childs as $info) {
			/**
			 * @var ActiveRecord_TempObject $temp
			 * @var ActiveRecord $record
			 */
			$temp   = null;
			$record = $info[self::KEY_RECORD];
			$child  = $info[self::KEY_CHILD_FIELD];
			$parent = $info[self::KEY_PARENT_FIELD];
			if ($record instanceof ActiveRecord_TempObject) {
				$temp   = $record;
				$record = $record->record();
			}
			$record->__set($child, $this->record()->__get($parent));
			if ($record->isNew() && $record->canInsert() || !$record->isNew()) {
				$temp ? $temp->save() : $record->save();
			}
		}
		unset(self::$storage[self::KEY_RECORD][$this->id]);
		self::updateStorage();
	}

	/**
	 * @return int
	 */
	protected static function nextId() {
		self::storage();
		return ++self::$storage[self::KEY_ID];
	}

	protected static function storage() {
		if (null === self::$storage) {
			if (!isset($_SESSION[self::STORAGE_NAME])) {
				$_SESSION[self::STORAGE_NAME] = array();
			}
			self::$storage = $_SESSION[self::STORAGE_NAME];
			if (!isset(self::$storage[self::KEY_ID])) {
				self::$storage[self::KEY_ID] = 0;
			}
			if (!isset(self::$storage[self::KEY_RECORD])) {
				self::$storage[self::KEY_RECORD] = array();
			}
		}
		return self::$storage;
	}

	protected static function updateStorage() {
		$_SESSION[self::STORAGE_NAME] = self::$storage;
	}

}