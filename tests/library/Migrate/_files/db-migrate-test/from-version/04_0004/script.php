<?php

class Nano_Migrate_Script_04_0004 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (401, ' . $db->quote('4th migration script') . ')'
		);
	}

}