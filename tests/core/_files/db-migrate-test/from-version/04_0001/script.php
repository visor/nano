<?php

class Nano_Migrate_Script_04_0001 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (1010, ' . $db->quote('first migration script') . ')'
		);
	}

}