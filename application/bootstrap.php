<?php

Nano_Config::setFormat(new Nano_Config_Format_Igbinary());
Nano::configure(new Nano_Config(SETTINGS . DS . Nano_Config::CONFIG_FILE_NAME));
Nano::instance();