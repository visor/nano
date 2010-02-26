<?php

class NanoMigrateTest extends PHPUnit_Framework_TestCase {

	protected $backupGlobals = false;

	protected function setUp() {
		if (false === Nano::config('db')) {
			self::markTestSkipped('No database configuration');
		}
		Nano_Db::clean();
		Nano::db()->query('drop table if exists migration_test');
		$this->createVersionTable();
		Nano::db()->delete(Nano_Migrate::VERSION_TABLE);
	}

	public function testPath() {
		$dbm1 = $this->createMigrate('empty');
		$this->assertEquals(dirName(__FILE__) . '/_files/db-migrate-test/empty', $dbm1->getPath());
	}

	public function testCurrentVersionOnEmptyTable() {
		$migrate = $this->createMigrate('empty');

		$testQuery = 'select * from ' . Nano_Migrate::VERSION_TABLE;
		$row = Nano::db()->getRow($testQuery);
		$this->assertFalse($row);

		$this->assertEquals('', $migrate->getCurrentVersion(Nano::db()));

		$row = Nano::db()->getRow($testQuery);
		$this->assertObjectHasAttribute('id', $row);
		$this->assertObjectHasAttribute('version', $row);

		$this->assertEquals(1, $row->id);
		$this->assertEquals('', $row->version);
	}

	public function testCurrentVersionOnPredefinedTable() {
		$migrate = $this->createMigrate('empty');

		$this->setVersion('01');
		$this->assertEquals('01', $migrate->getCurrentVersion());

		$this->setVersion('02');
		$this->assertEquals('02', $migrate->getCurrentVersion());
	}

	public function testEmptyMigration() {
		$migrate = $this->createMigrate('empty');
		$this->assertEquals(0, count($migrate->getSteps()));
		$this->assertTrue($migrate->run());
	}

	public function testSimpleMigration() {
		$migrate = $this->createMigrate('simple');
		$this->assertEquals(2, count($migrate->getSteps()));
		$this->assertTrue($migrate->run());

		$this->assertEquals('03_0002', $migrate->getCurrentVersion());
		$this->assertEquals(4, Nano::db()->getCell('select count(*) from migration_test'));
		$rows = Nano::db()->query('select * from migration_test order by id', PDO::FETCH_OBJ)->fetchAll();
		$expected = array(
			  '100' => 'first migration'
			, '101' => 'first migration script'
			, '200' => 'second migration'
			, '201' => 'second migration script'
		);
		foreach ($rows as $row) {
			$this->assertArrayHasKey($row->id, $expected);
			$this->assertEquals($expected[$row->id], $row->comment);
		}
	}

	public function testMigrationWithErrorInScript() {
		$migrate = $this->createMigrate('error-in-script');
		$this->assertEquals(2, count($migrate->getSteps()));
		try {
			$migrate->run();
			$this->fail('should throw exception');
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			throw $e;
		} catch (Exception $e) {
			$this->assertEquals('invalid', $e->getMessage());
		}
		$this->assertEquals('', $migrate->getCurrentVersion());
	}

	public function testMigrationWithErrorInQuery() {
		$migrate = $this->createMigrate('error-in-query');
		$this->assertEquals(2, count($migrate->getSteps()));
		try {
			$migrate->run();
			$this->fail('should throw exception');
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			throw $e;
		} catch (PDOException $e) {
		}
		$this->assertEquals('', $migrate->getCurrentVersion());
	}

	public function testMigrateFromVersion() {
		Nano::db()->exec(
			'create table migration_test('
				. 'id integer primary key'
				. ', comment text'
			. ')'
		);
		$migrate = $this->createMigrate('from-version');
		Nano_Migrate_Version::set('04_0002');

		$this->assertEquals(5, count($migrate->getSteps()));
		$this->assertTrue($migrate->run());
		$this->assertEquals('04_0005', $migrate->getCurrentVersion());
		$this->assertEquals(6, Nano::db()->getCell('select count(*) from migration_test'));

		$rows = Nano::db()->query('select * from migration_test order by id', PDO::FETCH_OBJ)->fetchAll();
		$expected = array(
			  '300' => '3rd migration'
			, '301' => '3rd migration script'
			, '400' => '4th migration'
			, '401' => '4th migration script'
			, '500' => '5th migration'
			, '501' => '5th migration script'
		);
		foreach ($rows as $row) {
			$this->assertArrayHasKey($row->id, $expected);
			$this->assertEquals($expected[$row->id], $row->comment);
		}
	}

	/**
	 * @return Nano_Migrate
	 * @param string $path
	 */
	protected function createMigrate($path) {
		$result = new Nano_Migrate(dirName(__FILE__) . '/_files/db-migrate-test/' . $path);
		$result->silent(true);
		return $result;
	}

	protected function createVersionTable() {
		Nano::db()->exec('create table if not exists ' . Nano_Migrate::VERSION_TABLE . '(
			  id integer primary key
			, version text
		)');
	}

	protected function setVersion($name) {
		Nano::db()->delete(Nano_Migrate::VERSION_TABLE);
		Nano::db()->insert(Nano_Migrate::VERSION_TABLE, array(
			  'id' => 1
			, 'version' => $name
		));
	}

	protected function tearDown() {
		Nano::db()->delete(Nano_Migrate::VERSION_TABLE);
		Nano::db()->exec('drop table if exists ' . Nano_Migrate::VERSION_TABLE);
		Nano_Db::clean();
		Nano_Db::close();
	}

}