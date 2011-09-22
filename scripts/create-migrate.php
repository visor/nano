<?php
return;

require dirName(dirName(__FILE__)) . '/library/Nano.php';

Nano::instance();

$name    = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
$queries = isset($_SERVER['argv'][2]) ? (bool)$_SERVER['argv'][2] : true;
$script  = isset($_SERVER['argv'][3]) ? (bool)$_SERVER['argv'][3] : false;

if (null === $name) {
	echo
		'Usage: '
			. PHP_EOL . '  ' . baseName(__FILE__) . ' name [queries [script]]'
			. PHP_EOL
			. PHP_EOL . '    queries - 1 or 0, default 1'
			. PHP_EOL . '    script  - 1 or 0, default 0'
			. PHP_EOL
			. PHP_EOL
	;
	exit(0);
}

$dir  = date('YmdHis-') . $name;
$path = APP . DIRECTORY_SEPARATOR . 'migrate' . DIRECTORY_SEPARATOR . $dir;
mkDir($path);

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