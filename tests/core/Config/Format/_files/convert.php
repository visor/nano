<?php

$data = include(__DIR__ . '/config.php');

file_put_contents(__DIR__ . '/config.serialize', serialize($data));
file_put_contents(__DIR__ . '/config.json', json_encode($data));
file_put_contents(__DIR__ . '/config.igbinary', igbinary_serialize($data));