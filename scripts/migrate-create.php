<?php

namespace CliScript;

/** @description Creates empty migration with empty queries and/or script files
 *
 * @param required migrateName         (any string) name of new migration (e.g. create-user-table or add-missed-field)
 * @param optional createQueriesFile   (1 or 0) create or not create $queries.php file with empty queries array, default — 1
 * @param optional createScriptFile    (1 or 0) create or not create $scrip.php file with empty script class, default — 0
 */
class MigrateCreate extends \Nano_Cli_Script {

	/**
	 * @return void
	 * @param string[] $args
	 */
	public function run(array $args) {
		$name    = isSet($args[0]) ? $args[0]       : null;
		$queries = isSet($args[1]) ? (bool)$args[1] : true;
		$script  = isSet($args[2]) ? (bool)$args[2] : false;

		if (null === $name) {
			$this->stop('Please pass migrateName parameter', 0);
		}

		$dir  = date('YmdHis-') . $name;
		$path = $this->getApplication()->getRootDir() . DIRECTORY_SEPARATOR . Migrate::DIR_NAME . DIRECTORY_SEPARATOR . $dir;
		if (!file_exists($path)) {
			mkDir($path, 0755, true);
		}

		if ($queries) {
			$queriesFile = $path . DIRECTORY_SEPARATOR . 'queries.php';
			$source      = <<<PHP
<?php

\$sql = array();

\$sql[] = '';
PHP;
			file_put_contents($queriesFile, $source);
		}

		if ($script) {
			$scriptFile = $path . DIRECTORY_SEPARATOR . 'script.php';
			$suffix     = str_replace('-', '_', $dir);
			$className  = 'Nano_Migrate_Script_' . $suffix;
			$source     = <<<PHP
<?php

class $className extends Nano_Migrate_Script {

	/**
	 * @return void
	 * @param Nano_Db \$db
	 */
	public function run(Nano_Db \$db) {
		//write code here
	}

}
PHP;
			file_put_contents($scriptFile, $source);
		}
	}

}