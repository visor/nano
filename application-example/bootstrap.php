<?php

require_once __DIR__ . '/../library/Application.php';

$application = new Application();
$application
	->withConfigurationFormat('php')
	->withRootDir(__DIR__)
	->withPlugin(new TestUtils_Coverage_Plugin(__DIR__))
	->withPlugin(new ControlPanelAssets())

	->configure()
;