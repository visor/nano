<?php

class Setting extends Nano_DbObject {

	const NAME = 'settings';

	protected $table      = self::NAME;
	protected $primaryKey = array('setting_id');
	protected $properties = array(
		  'setting_id'
		, 'setting_category_id'
		, 'type'
		, 'name'
		, 'value'
		, 'title'
		, 'description'
		, 'default'
		, 'values'
		, 'order'
	);

	/**
	 * @var array
	 */
	protected static $cache = null;

	/**
	 * @return scalar
	 * @param string $category
	 * @param string $name
	 */
	public static function get($category, $name) {
		$setting = self::getObject($category, $name);
		if (null === $setting) {
			return null;
		}
		if (null !== $setting->value) {
			return $setting->value;
		}
		return $setting->default;
	}

	/**
	 * @return Setting
	 * @param string $category
	 * @param string $name
	 */
	public static function getObject($category, $name) {
		static::load();
		if (isset(self::$cache[$category][$name])) {
			return self::$cache[$category][$name];
		}
		return null;
	}

	/**
	 * @return array
	 * @param string $name
	 */
	public static function getCategory($name) {
		static::load();
		if (isset(self::$cache[$name])) {
			return self::$cache[$name];
		}
		return array();
	}

	/**
	 * @return void
	 * @param string $category
	 * @param string $name
	 * @param scalar $value
	 */
	public static function set($category, $name, $value) {
		try {
			$categoryId = Setting_Category::get($category)->setting_category_id;
			self::db()->update(
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
	}

	/**
	 * @return boolean
	 * @param string $category
	 * @param string $type
	 * @param string $name
	 * @param string $title
	 * @param string $description
	 * @param scalar $default
	 * @param array $values
	 */
	public static function append($category, $type, $name, $title, $description = null, $default = null, array $values = array()) {
		try {
			$categoryId = Setting_Category::get($category)->setting_category_id;
			$setting    = parent::create(__CLASS__, array(
				  'setting_category_id' => $categoryId
				, 'type'                => $type
				, 'name'                => $name
				, 'value'               => null
				, 'title'               => $title
				, 'description'         => $description
				, 'default'             => $default
				, 'values'              => ($values ? null : serialize($values))
				, 'order'               => self::getNexOrderValue($categoryId)
			));
			$setting->save();
			return true;
		} catch (Nano_Exception $e) {
			Nano_Log::message($e);
			return false;
		}
	}

	protected static function load() {
		if (null === self::$cache) {
			self::$cache = static::loadCache();
		}
	}

	protected static function loadCache() {
		$result = array();
		$query  = sql::select('s.*')
			->from(array('s' => self::NAME))
			->innerJoin(array('c' => Setting_Category::NAME), 's.setting_category_id = c.setting_category_id')
			->order('c.' . self::db()->quoteName('order'))
			->order('s.' . self::db()->quoteName('order'))
		;
		$rows   = self::fetchThis($query);
		foreach ($rows as $row) {
			$category = Setting_Category::getById($row->setting_category_id)->name;
			if (isset($result[$category])) {
				$result[$category][$row->name] = $row;
			} else {
				$result[$category] = array($row->name => $row);
			}
		}
		return $result;
	}

	protected static function invalidate() {
		self::$cache = null;
	}

	protected static function getNexOrderValue($id) {
		return (int)self::db()->getCell('select max(' . self::db()->quoteName('order') . ') + 1 from ' . self::NAME . ' where setting_category_id = ' . self::db()->quote($id));
	}

}