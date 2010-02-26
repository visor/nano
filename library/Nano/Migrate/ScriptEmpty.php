<?php

class Nano_Migrate_ScriptEmpty extends Nano_Migrate_Script {

	/**
	 * @var Nano_Migrate_ScriptEmpty
	 */
	private static $instance = null;

	/**
	 * @return Nano_Migrate_ScriptEmpty
	 */
	public static function instance() {
		if (null == self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function run(Nano_Db $db) {}

}