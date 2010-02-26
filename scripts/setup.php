<?php

function fromTo($from, $to) {
	echo "\t", sprintf('%-20s', $from), ' => ', $to, PHP_EOL;
}

echo 'Setting up', PHP_EOL;
$root = realPath(dirName(__FILE__) . '/../application/settings') . DS;
$env  = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'dev';
fromTo('env.php', $root . 'env.php');
file_put_contents($root . 'env.php', '<?php $config = \'' . $env . '\';');
require_once dirName(dirName(__FILE__)) . '/lib/Nano.php';

$files = array(
	  'db.php'        => 'db.php'
	, 'log.php'       => 'log.php'
	, 'selenium.php'  => 'selenium.php'
	, 'web.php'       => 'web.php'
);

$source = dirName(__FILE__) . '/setup/' . $env;

if (file_exists($source)) {
	foreach ($files as $from => $to) {
		$sourceFile      = $source . DS . $from;
		$destinationDir  = realPath(dirName($root . $to));
		$destinationFile = $destinationDir . DS . baseName($to);
		if (file_exists($sourceFile)) {
			fromTo($from, $destinationFile);
			copy($sourceFile, $destinationFile);
		}
	}
}
echo 'Done', PHP_EOL;

if (file_exists($source . '.php')) {
	include($source . '.php');

	if (function_exists('setupEnv')) {
		setupEnv();
	}
}

Nano::reloadConfig();
require dirName(__FILE__) . '/include.php';
define('DOCUMENT_ROOT', dirName(ROOT));
echo PHP_EOL;

try {
	Nano_Db::close();
	$migration = new Nano_Migrate();
	$migration->run();
} catch (Exception $e) {
	echo $e;
}