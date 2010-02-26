<?php

class Nano_Migrate_Version extends Nano_DbObject {

	protected $table      = Nano_Migrate::VERSION_TABLE;
	protected $increment  = false;
	protected $properties = array(
		  'id'
		, 'version'
	);

	/**
	 * @return string
	 * @param Nano_Db $db
	 */
	public static function get() {
		$result = new self(1);
		if ($result->isNew()) {
			return self::set('')->version;
		}
		return $result->version;
	}

	/**
	 * @return Nano_Migrate_Version
	 * @param string $value
	 */
	public static function set($value) {
		$version = new self(1);
		if ($version->isNew()) {
			$version->id = 1;
		}
		$version->version = $value;
		$version->save();
		return $version;
	}

}