<?php
/**
 * @var \Nano\Routes $routes
 */
$routes
	->get('', 'index', 'index')

	->section('cp')
		->get('',                    'control-panel', 'dashboard')
		->get('/items',              'control-panel', 'items')
		->get('~/edit/(?P<id>\d+)',  'control-panel', 'edit')
		->post('~/edit/(?P<id>\d+)', 'control-panel', 'edit')
		->get('/variables',          'control-panel', 'variables')

		->get('/settings',                   'setting', 'index')
		->get('~/settings/(?P<category>.+)', 'setting', 'index')
		->post('/settings/save',              'setting', 'save')
	->end()

	->get('login',  'auth', 'login')
	->post('auth',  'auth', 'auth')
	->get('logout', 'auth', 'logout')
;
