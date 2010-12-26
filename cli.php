#!/bin/env php
<?php

require __DIR__ . '/library/Nano.php';

Nano::instance();

if ($_SERVER['argc'] < 2) {
	Nano_C_Cli::usage();
	return;
}

list($controller, $action) = Nano_C_Cli::extractControllerAction($_SERVER['argv'][1]);
$args = $_SERVER['argv'];
array_shift($args);
array_shift($args);
Nano_C_Cli::main($controller, $action, $args);