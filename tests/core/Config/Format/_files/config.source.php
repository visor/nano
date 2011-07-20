<?php return array(
	'cache' => array(
		'path' => '/var/www/hosts/nano.lc/cache',
		'api' => 'File',
	),
	'assets' => array(
		'path' => '/var/www/hosts/nano.lc/public/assets',
		'url' => '/assets',
	),
	'notification' => array(
		'email' => array(
			'from' => 'welcome@nano.lc',
			'name' => 'Example project',
			'templatePath' => '/var/www/hosts/nano.lc/application/views/notification/email',
		),
		'sms' => array(
			'ip' => array (
				'127.0.0.1',
				'192.168.1.4',
				'194.67.81.38',
				'194.67.83.38',
				'213.219.251.249',
				'90.156.151.65',
				'83.137.50.31',
				'213.248.32.158',
				'213.219.251.120',
			),
		),
		'error' => array (
			'errors@nano.lc',
		),
	),
	'plugins' => array (
		'AuthPlugin',
		'LogPlugin',
		'AssetsPlugin',
	),
	'files' => array(
		'personal_path' => '/var/www/hosts/nano.lc/files',
		'personal_url' => '/file',
		'photo_path' => '/var/www/hosts/nano.lc/public/resources/photos',
		'photo_url' => '/resources/photos',
	),
);