<?php

$data = include(__DIR__ . '/config.php');

file_put_contents(__DIR__ . '/config.serialize', serialize($data));
file_put_contents(__DIR__ . '/config.json',      json_encode($data));
file_put_contents(__DIR__ . '/config.igbinary',  igbinary_serialize($data));


$dir = __DIR__ . '/../../../../../library';

include $dir . '/Nano/Routes.php';
include $dir . '/Nano/RouteAbstract.php';
include $dir . '/Nano/Route/Static.php';
include $dir . '/Nano/Route/RegExp.php';
include $dir . '/Nano/Route/Subdomain.php';
include $dir . '/Nano/Route/Runnable.php';

$routes = new Nano_Routes();
include __DIR__ . '/routes.source.php';

$serialized = serialize($routes);
file_put_contents(__DIR__ . '/routes.format.php', $serialized);
file_put_contents(__DIR__ . '/routes.serialize',  $serialized);
file_put_contents(__DIR__ . '/routes.json',       $serialized);
file_put_contents(__DIR__ . '/routes.igbinary',   $serialized);