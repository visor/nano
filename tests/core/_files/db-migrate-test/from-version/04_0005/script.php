<?php

class Nano_Migrate_Script_04_0005 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (501, ' . $db->quote('5th migration script') . ')'
		);
	}

}