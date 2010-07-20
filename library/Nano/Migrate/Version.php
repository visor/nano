<?php

class Nano_Migrate_Version extends Nano_DbObject {

	const EMPTY_VERSION   = '';

	protected $table      = Nano_Migrate::VERSION_TABLE;
	protected $increment  = true;
	protected $primaryKey = array('id');
	protected $properties = array(
		  'id'
		, 'version'
	);

	/**
	 * @var string[]
	 */
	private static $versions = null;

	/**
	 * @return string[]
	 * @param Nano_Db $db
	 * @param boolean $force
	 */
	public static function getAll(Nano_Db $db, $force = false) {
		if (null === self::$versions || true === $force) {
			self::$versions = $db->getAssoc('select version, id from ' . Nano_Migrate::VERSION_TABLE);
		}
		return self::$versions;
	}

	/**
	 * @return boolean
	 * @param Nano_Db $db
	 * @param string $value
	 */
	public static function exists(Nano_Db $db, $value) {
		return array_key_exists($value, self::getAll($db));
	}

	/**
	 * @return boolean
	 * @param Nano_Db $db
	 * @param string $value
	 */
	public static function add(Nano_Db $db, $value) {
		if (self::exists($db, $value)) {
			return false;
		}
		$db->insert(Nano_Migrate::VERSION_TABLE, array('version' => $value));
		self::getAll($db, true);
		return true;
	}

}