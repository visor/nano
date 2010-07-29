<?php

Nano::routes()
	->add('^$', 'index', 'index')

	->add('^cp$',                  'control-panel', 'dashboard')
	->add('^cp/items$',            'control-panel', 'items')
	->add('^cp/edit/(?P<id>\d+)$', 'control-panel', 'edit')

	->add('^cp/settings$',                  'setting', 'index')
	->add('^cp/settings/(?P<category>.+)$', 'setting', 'index')

	->add('^login$',  'auth', 'login')
	->add('^auth$',   'auth', 'auth')
	->add('^logout$', 'auth', 'logout')
;
