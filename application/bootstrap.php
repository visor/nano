<?php

require_once __DIR__ . '/../library/Application.php';

Application::create()
	->usingConfigurationFormat('php')
	->withRootDir(__DIR__)
	->withPlugin(new ControlPanelAssets())
	->configure()
;
