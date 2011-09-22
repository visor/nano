<?php
return;

require dirName(__DIR__) . '/library/Nano.php';
require APP . DS . 'bootstrap.php';

$db = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : Nano_Db::DEFAULT_NAME;

try {
	Nano_Db::setDefault($db);
	$migration = new Nano_Migrate();
	$migration->run();
} catch (Exception $e) {
	echo $e;
}
