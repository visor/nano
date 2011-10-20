<?php

class Nano_Migrate_Script_04_0002 extends Nano_Migrate_Script {

	public function run(Nano_Db $db) {
		$db->exec(
			'insert into migration_test(id, comment) values (2010, ' . $db->quote('second migration script') . ')'
		);
	}

}