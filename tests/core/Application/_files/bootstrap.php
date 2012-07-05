<?php

$application = new \Nano\Application();

$application
	->withConfigurationFormat('php')
	->withRootDir(__DIR__)
	->withModulesDir(__DIR__ . DIRECTORY_SEPARATOR . 'application-modules')
	->withSharedModulesDir(__DIR__ . DIRECTORY_SEPARATOR . 'shared-modules')
	->withModule('module1')
	->withModule('module2')
	->withModule('module3')
	->withModule('module4')
	->withModule('module5')
	->configure()
;