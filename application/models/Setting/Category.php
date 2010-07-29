<?php

class Setting_Category extends Nano_DbObject {

	const NAME = 'settings_categories';

	protected $table      = self::NAME;
	protected $primaryKey = array('setting_category_id');
	protected $properties = array(
		  'setting_category_id'
		, 'title'
		, 'description'
		, 'name'
		, 'order'
	);

	/**
	 * @var array
	 */
	private static $cache = null;

	/**
	 * @return mixed
	 * @param string $category
	 * @param string $name
	 */
	public static function getByName($name) {
		self::load();
		//return from cache
	}

	/**
	 * @return void
	 * @param string $category
	 * @param string $name
	 * @param scalar $value
	 */
	public static function set($category, $name, $value) {
		//save to db...
		self::invalidate();
	}

	private static function load() {
		self::$cache = array();
	}

	private static function invalidate() {
		self::$cache = null;
	}

}