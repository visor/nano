<?php

/**
 * @outputBuffering enabled
 * @group cli
 * @group framework
 */
class CliTest extends TestUtils_TestCase {

	public static function setUpBeforeClass() {
		require_once __DIR__ . '/_files/ValidCliController.php';
		require_once __DIR__ . '/_files/InvalidCliController.php';
	}

	protected function setUp() {
		self::markTestSkipped('need refactor');
	}

	public function testExtractingNames() {
		self::assertEquals(array('index', 'index'), Nano_C_Cli::extractControllerAction('index'));
		self::assertEquals(array('c', 'a'), Nano_C_Cli::extractControllerAction('c.a'));
	}

	public function testUnknownController() {
		Nano_C_Cli::main('1', '1', array());
		self::assertEquals('Unknown controller: 1' . PHP_EOL, ob_get_contents());
	}

	public function testInvalidController() {
		Nano_C_Cli::main('invalid-cli', 'test', array());
		self::assertEquals('Not CLI controller: invalid-cli' . PHP_EOL, ob_get_contents());
	}

	public function testUnknownAction() {
		Nano_C_Cli::main('valid-cli', '1', array());
		self::assertEquals('Unknown action: 1' . PHP_EOL, ob_get_contents());
	}

	public function testValid() {
		Nano_C_Cli::main('valid-cli', 'simple', array());
		self::assertEquals('OK', ob_get_contents());
		ob_clean();

		Nano_C_Cli::main('valid-cli', 'args', array('some content'));
		self::assertEquals('some content', ob_get_contents());
		ob_clean();

		Nano_C_Cli::main('valid-cli', null, array());
		self::assertEquals('Hello World!', ob_get_contents());
	}

}