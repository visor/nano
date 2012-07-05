<?php
/**
 * @var \Nano\Routes $routes
 */
$routes->section('response')
	->get('/set-body',    'response-test', 'set-body')
	->get('/render-body', 'response-test', 'render-body')
	->get('/header',      'response-test', 'header')
;