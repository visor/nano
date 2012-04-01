<?php
/**
 * @var Nano_Routes $routes
 */
$routes
	->prefix('error')
		->get('/no-errors',    'raise', 'ok')
		->get('/action-fatal', 'raise', 'fatal-error')
		->get('/view-fatal',   'raise', 'fatal-error-in-view')
		->get('/compile',      'raise', 'compile')
		->get('/exception',    'raise', 'exception')
		->get('/warning',      'raise', 'warning')
		->get('/notice',       'raise', 'notice')
		->get('/no-action',    'raise', 'no-action')
		->get('/404',          'raise', 'not-found')
		->get('/500',          'raise', 'internal-error')
		->get('/custom',       'raise', 'custom')
		->get('/no-class',     'no-class', 'index')
		->get('/null-output',  'raise', 'null-output')

	->prefix('response')
		->get('/set-body',    'response-test', 'set-body')
		->get('/render-body', 'response-test', 'render-body')
		->get('/header',      'response-test', 'header')

	->prefix('cookie')
		->get('/set',   'cookie-test', 'set')
		->get('/view',  'cookie-test', 'view')
		->get('/erase', 'cookie-test', 'erase')
;