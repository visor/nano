<?php

class Setting_Category extends Nano_DbObject {

	const NAME = 'settings_categories';

	protected $table      = self::NAME;
	protected $primaryKey = array('setting_category_id');
	protected $properties = array(
		  'setting_category_id'
		, 'name'
		, 'title'
		, 'description'
		, 'order'
	);

	/**
	 * @var array
	 */
	private static $cache = null;

	public static function append($name, $title, $description = null) {
		try {
			$category = self::create(__CLASS__, array(
				  'name'        => $name
				, 'title'       => $title
				, 'description' => $description
				, 'order'       => self::getNexOrderValue()
			));
			$category->save();
			self::invalidate();
			return true;
		} catch (PDOException $e) {
			Nano_Log::message($e);
			return false;
		}
	}

	/**
	 * Setting_Category
	 */
	public static function first() {
		self::load();
		reset(self::$cache);
		return current(self::$cache);
	}

	/**
	 * @return mixed
	 * @param string $category
	 * @param string $name
	 */
	public static function get($name) {
		self::load();
		if (isset(self::$cache[$name])) {
			return self::$cache[$name];
		}
		throw new Nano_Exception('Category "' . $name . '" not found');
	}

	public static function getById($id) {
		self::load();
		foreach (self::$cache as $category) {
			if ($id == $category->setting_category_id) {
				return $category;
			}
		}
		throw new Nano_Exception('Category with id "' . $name . '" not found');
	}

	/**
	 * @return Setting_Category[string]
	 */
	public static function all() {
		self::load();
		return self::$cache;
	}

	protected static function load() {
		if (null === self::$cache) {
			self::$cache = self::loadCache();
		}
	}

	protected static function loadCache() {
		$result = array();
		$rows   = self::fetchThis(sql::select('*')->from(self::NAME)->order(Nano::db()->quoteName('order')));
		foreach ($rows as $row) {
			$result[$row->name] = $row;
		}
		return $result;
	}

	protected static function invalidate() {
		self::$cache = null;
	}

	protected static function getNexOrderValue() {
		return (int)self::db()->getCell('select max(' . self::db()->quoteName('order') . ') + 1 from ' . self::NAME);
	}

}