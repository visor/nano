<?php

$data = include(__DIR__ . '/config.php');

file_put_contents(__DIR__ . '/config.serialize', serialize($data));
file_put_contents(__DIR__ . '/config.json',      json_encode($data));
file_put_contents(__DIR__ . '/config.igbinary',  igbinary_serialize($data));


$dir = __DIR__ . '/../../../../../library';

include $dir . '/Route/Section/Common.php';
include $dir . '/Route/Section/Root.php';
include $dir . '/Route/Section/StaticLocation.php';
include $dir . '/Route/Section/RegExp.php';
include $dir . '/Routes.php';
include $dir . '/Route/Common.php';
include $dir . '/Route/StaticLocation.php';
include $dir . '/Route/RegExp.php';
include $dir . '/Route/Subdomain.php';
include $dir . '/Route/Runnable.php';

$routes = new \Nano\Routes();
include __DIR__ . '/routes.source.php';

$serialized = serialize($routes);
file_put_contents(__DIR__ . '/routes.format.php', $serialized);
file_put_contents(__DIR__ . '/routes.serialize',  $serialized);
file_put_contents(__DIR__ . '/routes.json',       $serialized);
file_put_contents(__DIR__ . '/routes.igbinary',   $serialized);