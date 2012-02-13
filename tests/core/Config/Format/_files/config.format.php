<?php return (object)(array(
   'cache' => (object)(array(
     'path' => '/var/www/hosts/nano.lc/cache',
     'api' => 'File',
  )),
   'assets' => (object)(array(
     'path' => '/var/www/hosts/nano.lc/public/assets',
     'url' => '/assets',
  )),
   'notification' => (object)(array(
     'email' => (object)(array(
       'from' => 'welcome@nano.lc',
       'name' => 'Example project',
       'templatePath' => '/var/www/hosts/nano.lc/application/views/notification/email',
    )),
     'sms' => (object)(array(
       'ip' => array (
        0 => '127.0.0.1',
        1 => '192.168.1.4',
        2 => '194.67.81.38',
        3 => '194.67.83.38',
        4 => '213.219.251.249',
        5 => '90.156.151.65',
        6 => '83.137.50.31',
        7 => '213.248.32.158',
        8 => '213.219.251.120',
      ),
    )),
     'error' => array (
      0 => 'errors@nano.lc',
    ),
  )),
   'plugins' => array (
    0 => 'AuthPlugin',
    1 => 'LogPlugin',
    2 => 'AssetsPlugin',
  ),
   'files' => (object)(array(
     'personal_path' => '/var/www/hosts/nano.lc/files',
     'personal_url' => '/file',
     'photo_path' => '/var/www/hosts/nano.lc/public/resources/photos',
     'photo_url' => '/resources/photos',
  )),
));