<?php

require './../library/Nano.php';

Nano_Config::setFormat(new Nano_Config_Format_Json());
Nano::run();