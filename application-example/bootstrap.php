<?php

require_once __DIR__ . '/../library/Application.php';

Application::create()
	->usingConfigurationFormat('php')
	->withRootDir(__DIR__)
	->withPlugin(new TestUtils_Coverage_Plugin(__DIR__))
	->withPlugin(new ControlPanelAssets())
	->configure()
;

Orm::configure((array)Nano::config('orm'));