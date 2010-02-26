<?php

abstract class Nano_Migrate_Script {

	abstract public function run(Nano_Db $db);

}