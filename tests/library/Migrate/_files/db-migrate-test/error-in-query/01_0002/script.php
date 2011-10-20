<?php

class Nano_Migrate_Script_01_0002 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (201, ' . $db->quote('second migration script') . ')'
		);
	}

}