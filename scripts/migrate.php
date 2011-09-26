<?php

namespace CliScript;

/**
 * @description Run not applied migrates on given database
 *
 * @param optional database Name of database configuration (see db.php in YOUR_APP/settings/setup), default — using default database name
 */
class Migrate extends \Nano_Cli_Script {

	const DIR_NAME = 'migrate';

	/**
	 * @param string[] $args
	 * @return void
	 */
	public function run(array $args) {
		$db = isSet($args[0]) ? $args[0] : \Nano_Db::DEFAULT_NAME;
		try {
			\Nano_Db::setDefault($db);
			$migration = new \Nano_Migrate(
				$this->getApplication()->getRootDir() . DIRECTORY_SEPARATOR . self::DIR_NAME
			);
			$migration->run();
		} catch (\Exception $e) {
			echo $e;
		}
	}

}