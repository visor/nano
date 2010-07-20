<?php

class Nano_MigrateTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Nano_Db
	 */
	protected $db = null;

	protected function setUp() {
		$this->db = new Nano_Db('sqlite::memory:');
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		$this->createVersionTable();
	}

	public function testPath() {
		$dbm1 = $this->createMigrate('empty');
		self::assertEquals(dirName(__FILE__) . '/_files/db-migrate-test/empty', $dbm1->getPath());
	}

	public function testCurrentVersionOnEmptyTable() {
		$testQuery = 'select * from ' . Nano_Migrate::VERSION_TABLE;
		$row       = $this->db->getRow($testQuery);

		self::assertFalse($row);
		self::assertEquals(0, count(Nano_Migrate_Version::getAll($this->db, true)));
	}

	public function testCurrentVersionOnPredefinedTable() {
		$migrate = $this->createMigrate('empty');

		$this->setVersion('01');
		Nano_Migrate_Version::getAll($this->db, true);
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '01'));

		$this->setVersion('02');
		Nano_Migrate_Version::getAll($this->db, true);
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '01'));
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '02'));
	}

	public function testEmptyMigration() {
		$migrate = $this->createMigrate('empty');
		self::assertEquals(0, count($migrate->getSteps()));
		self::assertTrue($migrate->run());
		self::assertEquals(0, count(Nano_Migrate_Version::getAll($this->db, true)));
	}

	public function testSimpleMigration() {
		$migrate = $this->createMigrate('simple');
		self::assertEquals(2, count($migrate->getSteps()));
		self::assertTrue($migrate->run());
		Nano_Migrate_Version::getAll($this->db, true);
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '03_0002'));
		self::assertEquals(4, $this->db->getCell('select count(*) from migration_test'));

		$rows     = $this->db->query('select * from migration_test order by id', PDO::FETCH_OBJ)->fetchAll();
		$expected = array(
			  '100' => 'first migration'
			, '101' => 'first migration script'
			, '200' => 'second migration'
			, '201' => 'second migration script'
		);
		foreach ($rows as $row) {
			self::assertArrayHasKey($row->id, $expected);
			self::assertEquals($expected[$row->id], $row->comment);
		}
	}

	public function testMigrationWithErrorInScript() {
		$migrate = $this->createMigrate('error-in-script');
		self::assertEquals(2, count($migrate->getSteps()));
		try {
			$migrate->run();
			self::fail('should throw exception');
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			throw $e;
		} catch (Exception $e) {
			self::assertEquals('invalid', $e->getMessage());
		}

		Nano_Migrate_Version::getAll($this->db, true);
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '02_0001'));
	}

	public function testMigrationWithErrorInQuery() {
		$migrate = $this->createMigrate('error-in-query');
		self::assertEquals(2, count($migrate->getSteps()));
		try {
			$migrate->run();
			self::fail('should throw exception');
		} catch (PHPUnit_Framework_AssertionFailedError $e) {
			throw $e;
		} catch (PDOException $e) {
		}

		Nano_Migrate_Version::getAll($this->db, true);
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '01_0001'));
	}

	public function testMigrateFromVersion() {
		$this->setVersion('04_0001');
		$this->setVersion('04_0002');
		Nano_Migrate_Version::getAll($this->db, true);

		$this->db->exec(
			'create table migration_test('
				. 'id integer primary key'
				. ', comment text'
			. ')'
		);
		$migrate = $this->createMigrate('from-version');

		self::assertEquals(5, count($migrate->getSteps()));
		self::assertTrue($migrate->run());

		Nano_Migrate_Version::getAll($this->db, true);
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '04_0003'));
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '04_0004'));
		self::assertTrue(Nano_Migrate_Version::exists($this->db, '04_0005'));
		self::assertEquals(6, $this->db->getCell('select count(*) from migration_test'));

		$rows     = $this->db->query('select * from migration_test order by id', PDO::FETCH_OBJ)->fetchAll();
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
		$result->setDb($this->db);
		$result->silent(true);
		return $result;
	}

	protected function createVersionTable() {
		$this->db->exec('create table if not exists ' . Nano_Migrate::VERSION_TABLE . '(
			  id integer primary key
			, version text
		)');
	}

	protected function setVersion($name) {
		$this->db->insert(Nano_Migrate::VERSION_TABLE, array('version' => $name));
	}

	protected function tearDown() {
		$this->db = null;
		unset($this->db);
	}

}
