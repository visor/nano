<?php

/**
 * @group log
 * @group database
 * @group framework
 */
class NanoDbLoggerTest extends TestUtils_TestCase {

	protected $logBackup;

	protected function setUp() {
		Nano::db()->exec('drop table if exists nano_log_test');
		Nano::db()->exec(
			'create table nano_log_test(id int(11) not null auto_increment primary key, text varchar(100))'
		);
		$this->clearLog();
		$config = Nano_Db::getConfig(Nano::db()->getName());
		if (isset($config['log'])) {
			$this->logBackup = $config['log'];
		}
	}

	public function testLoggingQuery() {
		Nano::db()->query('select 1+2');
		self::assertEquals(1, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals('select 1+2', Nano::db()->log()->getLastQuery());

		Nano::db()->query('select 2+2', PDO::FETCH_ASSOC);
		self::assertEquals(2, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals('select 2+2', Nano::db()->log()->getLastQuery());

		Nano::db()->query('select 3+2', PDO::FETCH_COLUMN, 1);
		self::assertEquals(3, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals('select 3+2', Nano::db()->log()->getLastQuery());

		$var = new stdClass();
		Nano::db()->query('select 4+2', PDO::FETCH_INTO, $var);
		self::assertEquals(4, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals('select 4+2', Nano::db()->log()->getLastQuery());

		Nano::db()->query('select 5+2', PDO::FETCH_CLASS, 'ActiveRecord', array());
		self::assertEquals(5, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals('select 5+2', Nano::db()->log()->getLastQuery());
	}

	public function testLoggingExec() {
		Nano::db()->exec('set names UTF8');
		self::assertEquals(1, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals('set names UTF8', Nano::db()->log()->getLastQuery());
	}

	public function testLoggingPdoStatement() {
		$sql     = 'insert into nano_log_test(text) values(:value)';
		$stmt    = Nano::db()->prepare($sql);
		$queries = array();
		$actual  = array();
		for ($i = 1; $i <= 10; ++$i) {
			$queries[] = $sql;
			$stmt->execute(array('value' => $i));
		}
		foreach (Nano::db()->log()->queries() as $row) {
			$actual[] = $row['query'];
		}
		self::assertEquals(10, Nano::db()->log()->count());
		self::assertNotNull(Nano::db()->log()->getLastQueryTime());
		self::assertEquals($sql, Nano::db()->log()->getLastQuery());
		self::assertEquals($queries, $actual);
	}

	public function testDisabledLog() {
		$config = Nano::config('db');
		$config[Nano::db()->getName()]['log'] = false;
		Nano::setConfig('db', $config);

		$sql  = 'insert into nano_log_test(text) values(:value)';
		$stmt = Nano::db()->prepare($sql);
		for ($i = 1; $i <= 10; ++$i) {
			$stmt->execute(array('value' => $i));
		}
		Nano::db()->log()->append('test', 100);
		Nano::db()->query('select 1+1');
		Nano::db()->query('select 1+2', PDO::FETCH_BOTH);
		Nano::db()->query('select 1+3', PDO::FETCH_COLUMN, 1);
		Nano::db()->query('select 1+4', PDO::FETCH_CLASS, 'ActiveRecord', array());

		self::assertEquals(0, Nano::db()->log()->count());
		self::assertEquals(array(), Nano::db()->log()->queries());
		self::assertNull(Nano::db()->log()->getLastQueryTime());
		self::assertNull(Nano::db()->log()->getLastQuery());
	}

	public function testCleanLog() {
		Nano::db()->log()->append('test', 100);
		self::assertEquals(1, Nano::db()->log()->count());
		self::assertEquals(array(array('query' => 'test', 'time' => 100)), Nano::db()->log()->queries());
		self::assertEquals(100, Nano::db()->log()->getLastQueryTime());
		self::assertEquals('test', Nano::db()->log()->getLastQuery());

		Nano::db()->log()->clean();
		self::assertEquals(0, Nano::db()->log()->count());
		self::assertEquals(array(), Nano::db()->log()->queries());
		self::assertNull(Nano::db()->log()->getLastQueryTime());
		self::assertNull(Nano::db()->log()->getLastQuery());
	}

	public function testLogginWithErrorInQuery() {
		self::assertException(
			function() {
				Nano::db()->query('invalid query');
			}
			, 'PDOException'
			, ''
		);
		self::assertEquals(2, Nano::db()->log()->count());

		self::assertException(
			function() {
				$statement = Nano::db()->prepare('invalid query');
				$statement->execute();
			}
			, 'PDOException'
			, ''
		);
		self::assertEquals(4, Nano::db()->log()->count());
	}

	protected function clearLog($remove = true) {
		Nano::db()->log()->clean();
		self::setObjectProperty(Nano::db()->log(), 'logFile', null);
		$config = Nano_Db::getConfig(Nano::db()->getName());
		if ($remove && file_exists($config['log'])) {
			unlink($config['log']);
		}

		self::assertEquals(0, Nano::db()->log()->count());
		self::assertEquals(array(), Nano::db()->log()->queries());
		self::assertNull(Nano::db()->log()->getLastQueryTime());
		self::assertNull(Nano::db()->log()->getLastQuery());
	}

	protected function tearDown() {
		Nano::db()->exec('drop table if exists nano_log_test');
		$this->clearLog(false);
		if ($this->logBackup) {
			$config = Nano::config('db');
			$config[Nano::db()->getName()]['log'] = $this->logBackup;
			Nano::setConfig('db', $config);
		}
	}

}