<?php
/**
 * @var Nano_Routes $routes
 */
$routes
	->get('', 'index', 'index')

	->prefix('cp')
		->get('',                    'control-panel', 'dashboard')
		->get('/items',              'control-panel', 'items')
		->get('~/edit/(?P<id>\d+)',  'control-panel', 'edit')
		->post('~/edit/(?P<id>\d+)', 'control-panel', 'edit')
		->get('/variables',          'control-panel', 'variables')

	->prefix(null)
		->add('login',  'auth', 'login')
		->post('auth',   'auth', 'auth')
		->add('logout', 'auth', 'logout')
;
