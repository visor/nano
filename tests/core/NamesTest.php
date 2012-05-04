<?php

include_once $GLOBALS['application']->nanoRootDir . '/library/Nano/Names.php';

/**
 * @group core
 * @group names
 */
class Core_NamesTest extends TestUtils_TestCase {

	public function testAddingApplicationNamespaceForControllerClass() {
		self::assertEquals('App\\Controller\\Some',       \Nano\Names::controllerClass('some'));
		self::assertEquals('App\\Controller\\YetAnother', \Nano\Names::controllerClass('yet-another'));
	}

	public function testUsingControllerAdditionalNamespace() {
		self::assertEquals('App\\Controller\\News\\Index', \Nano\Names::controllerClass('news/index'));
	}

	public function testAddingModuleNamespaceForControllerClass() {
		self::assertEquals('Module\\Test\\Controller\\Index', \Nano\Names::controllerClass('index', 'test'));
	}

	public function testUsingControllerAdditionalNamespaceWithModuleNamespace() {
		self::assertEquals('Module\\Test\\Controller\\News\\Backend', \Nano\Names::controllerClass('news/backend', 'test'));
	}

}