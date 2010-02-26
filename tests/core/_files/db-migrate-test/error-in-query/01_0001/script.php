<?php

class Nano_Migrate_Script_01_0001 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (101, ' . $db->quote('first migration script') . ')'
		);
	}

}