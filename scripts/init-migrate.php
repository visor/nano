<?php

require dirName(__FILE__) . '/include.php';

$version = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';

Nano_Migrate_Init::init(Nano::db());
Nano_Migrate_Version::set($version);