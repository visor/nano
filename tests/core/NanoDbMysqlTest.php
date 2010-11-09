<?php

/**
 * @group framework
 */
class NanoDbMysqlTest extends TestUtils_TestCase {

	public function testQuoteName() {
		self::assertEquals('`field`', Nano_Db_mysql::quoteName('field'));
		self::assertEquals('`table`.`field`', Nano_Db_mysql::quoteName('table.field'));
		self::assertEquals('`database`.`table`.`field`', Nano_Db_mysql::quoteName('database.table.field'));
	}

}
