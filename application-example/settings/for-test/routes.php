<?php
/**
 * @var Nano_Routes $routes
 */
$routes->prefix('response')
	->get('/set-body',    'response-test', 'set-body')
	->get('/render-body', 'response-test', 'render-body')
	->get('/header',      'response-test', 'header')
;