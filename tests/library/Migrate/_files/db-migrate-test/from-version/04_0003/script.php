<?php

class Nano_Migrate_Script_04_0003 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (301, ' . $db->quote('3rd migration script') . ')'
		);
	}

}