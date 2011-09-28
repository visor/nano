<?php

require_once 'Application.php';

Application::create()
	->usingConfigurationFormat('php')
	->withRootDir(__DIR__)
	// put your configuration here
	->configure()
;
