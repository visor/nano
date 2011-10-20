<?php

/**
 * @group migrate
 */
class Library_Migrate_StepTest extends PHPUnit_Framework_TestCase {

	public function testEmptyFolder() {
		$step = $this->createStep('01_empty');

		$this->assertEquals(0, count($step->getQueries()));
		self::assertInstanceOf('Nano_Migrate_ScriptEmpty', $step->getScript());
	}

	public function testQueryesOnly() {
		$step = $this->createStep('02_queries');

		$this->assertEquals(10, count($step->getQueries()));
		self::assertInstanceOf('Nano_Migrate_ScriptEmpty', $step->getScript());
	}

	public function testManualOnly() {
		$step = $this->createStep('03_script');

		$this->assertEquals(0, count($step->getQueries()));
		self::assertInstanceOf('Nano_Migrate_Script', $step->getScript());
		self::assertInstanceOf('Nano_Migrate_Script_03_script', $step->getScript());
	}

	public function testBothUpdates() {
		$step = $this->createStep('04_both');

		$this->assertEquals(10, count($step->getQueries()));
		self::assertInstanceOf('Nano_Migrate_Script', $step->getScript());
		self::assertInstanceOf('Nano_Migrate_Script_04_both', $step->getScript());
	}

	/**
	 * @return Nano_Migrate_Step
	 * @param string $path
	 */
	protected function createStep($path) {
		$result = new Nano_Migrate_Step(dirName(__FILE__) . '/_files/db-migrate-steps/' . $path);
		$result->load();
		return $result;
	}

}