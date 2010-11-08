<?php

require './library/Nano.php';

Nano::instance();

if ($_SERVER['argc'] < 2) {
	Nano_C_Cli::usage();
	return;
}

list($controller, $action) = Nano_C_Cli::extractControllerAction($_SERVER['argv'][1]);
$args = $_SERVER['argv'];
array_unshift($args, null);
array_unshift($args, null);
Nano_C_Cli::main($controller, $action, $args);