<?php

require_once __DIR__ . '/../library/Application.php';

$application = new \Nano\Application();
$application
	->withConfigurationFormat('php')
	->withRootDir(__DIR__)
	->withPlugin(new Nano\TestUtils\Coverage\Plugin(__DIR__))

	->configure()
;