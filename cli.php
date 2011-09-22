#!/usr/bin/php
<?php

require_once __DIR__ . '/library/Nano/Loader.php';
$loader = new Nano_Loader();
$loader->register();

$args = $_SERVER['argv'];
array_shift($args);
Nano_Cli::main($args);