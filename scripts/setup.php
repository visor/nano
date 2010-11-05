<?php

function fromTo($from, $to) {
	echo "\t", sprintf('%-20s', $from), ' => ', $to, PHP_EOL;
}

echo 'Setting up', PHP_EOL;
$root = realPath(__DIR__ . '/../application/settings');
$env  = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'dev';
fromTo('env.php', $root . DIRECTORY_SEPARATOR . 'env.php');
file_put_contents($root  . DIRECTORY_SEPARATOR . 'env.php', '<?php $config = \'' . $env . '\';');

$source = __DIR__ . '/setup/' . $env;
$files = new DirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . 'setup' . DIRECTORY_SEPARATOR . $env);

foreach ($files as $from) {
	/**
	 * @var DirectoryIterator $from
	 */
	if ($from->isDir() || $from->isDot()) {
		continue;
	}

	$sourceFile      = $from->getPathName();
	$destinationFile = $root . DIRECTORY_SEPARATOR . $from->getBaseName();
	if (file_exists($sourceFile)) {
		fromTo($from, $destinationFile);
		copy($sourceFile, $destinationFile);
	}
}
echo 'Done', PHP_EOL;

if (file_exists($source . '.php')) {
	include($source . '.php');

	if (function_exists('setupEnv')) {
		setupEnv();
	}
}

require dirName(__DIR__) . '/library/Nano.php';
Nano::reloadConfig();