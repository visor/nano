<?php

require_once __DIR__ . '/library/Application.php';
$loader = new Nano_Loader();
$loader->registerApplication();

$args = $_SERVER['argv'];
array_shift($args);
$result = Nano_Cli::main($args);

if (0 !== $result) {
	exit($result);
}