<?php

namespace CliScript;

/**
 * @description Creates history table in given database
 *
 * @param optional database Name of database configuration (see db.php in YOUR_APP/settings/setup)
 */
class MigrateInit extends \Nano_Cli_Script {

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		\Nano_Migrate_Init::init(
			\Nano::db(isSet($args[0]) ? $args[0] : null)
		);
	}

}