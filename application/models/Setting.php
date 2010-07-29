<?php

class Setting extends Nano_DbObject {

	const NAME = 'settings';

	protected $table      = self::NAME;
	protected $primaryKey = array('setting_id');
	protected $properties = array(
		  'setting_id'
		, 'setting_category_id'
		, 'title'
		, 'description'
		, 'name'
		, 'value'
		, 'default'
		, 'order'
		, 'values'
	);

	/**
	 * @var array
	 */
	protected static $cache = null;

	/**
	 * @return mixed
	 * @param string $category
	 * @param string $name
	 */
	public static function get($category, $name) {
		static::load();
		if (isset(self::$cache[$category][$name])) {
			return self::$cache[$category][$name];
		}
		return null;
	}

	/**
	 * @return void
	 * @param string $category
	 * @param string $name
	 * @param scalar $value
	 */
	public static function set($category, $name, $value) {
		$categoryId = Setting_Category::getByName($category);
		if (null === $categoryId) {
			return false;
		}
		try {
			Nano::db()->update(
				  self::NAME
				, array('value' => $value)
				, array(
					  'setting_category_id' => $categoryId
					, 'name'                => $name
				)
			);
			static::invalidate();
		} catch (PDOException $e) {
			return false;
		}
		return true;
	}

	public static function getFromCategory($category) {
		static::load();
		//return from cache
	}

	public static function append($category, $title, $description, $name, $value, $default, array $values = array()) {
		$categoryId = Setting_Category::getByName($category);
		if (null === $categoryId) {
			return false;
		}
		//add setting to the end of category
	}

	protected static function load() {
		if (null === self::$cache) {
			self::$cache = static::loadCache();
		}
	}

	protected static function loadCache() {
		return array();
	}

	protected static function invalidate() {
		self::$cache = null;
	}

}