<?php

require dirName(__DIR__) . '/library/Nano.php';
Nano::instance();

$database = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;

Nano_Migrate_Init::init(Nano::db($database));