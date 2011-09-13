<?php

/**
 * @group framework
 * @group loader
 */
class NanoLoaderTest extends TestUtils_TestCase {

	public function testIsModuleClass() {
		self::assertFalse(Nano_Loader::isModuleClass(__CLASS__));
		self::assertFalse(Nano_Loader::isModuleClass('M_ClassName'));
		self::assertFalse(Nano_Loader::isModuleClass('M_ModuleName_'));
		self::assertTrue(Nano_Loader::isModuleClass('M_ModuleName_ClassName'));
	}

	public function testModuleNameToFolder() {
		self::assertEquals('example', Nano_Loader::moduleToFolderName('Example'));
		self::assertEquals('a-example', Nano_Loader::moduleToFolderName('AExample'));
		self::assertEquals('other-module', Nano_Loader::moduleToFolderName('OtherModule'));
		self::assertEquals('someothermodule', Nano_Loader::moduleToFolderName('Someothermodule'));
	}

	public function testExtractModuleClass() {
		self::assertEquals(array('test-module', 'Library', 'Class'),    Nano_Loader::extractModuleClassParts('M_TestModule_Library_Class'));
		self::assertEquals(array('test-module', 'Controller', 'Class'), Nano_Loader::extractModuleClassParts('M_TestModule_Controller_Class'));
		self::assertEquals(array('test-module', 'Model', 'Class'),      Nano_Loader::extractModuleClassParts('M_TestModule_Model_Class'));
		self::assertEquals(array('test-module', 'Plugin', 'Class'),     Nano_Loader::extractModuleClassParts('M_TestModule_Plugin_Class'));
	}

	public function testFormatModuleName() {
		self::assertEquals('M_TestModule_Library_Class', Nano_Loader::formatModuleClassName('test-module', 'library', 'class'));
		self::assertEquals('M_TestModule_Controller_Class', Nano_Loader::formatModuleClassName('test-module', 'controller', 'class'));
		self::assertEquals('M_TestModule_Controller_SomeClass', Nano_Loader::formatModuleClassName('test-module', 'controller', 'some-class'));
	}

	public function testLoadingModuleClass() {
		self::assertFalse(Nano_Loader::load('M_TestModule_Library_Class'));
		self::assertFalse(Nano_Loader::load('M_TestModule_Controller_Class'));
		self::assertFalse(Nano_Loader::load('M_TestModule_Model_Class'));
		self::assertFalse(Nano_Loader::load('M_TestModule_Plugin_Class'));

		Nano::modules()->append('test-module', $this->files->get($this, DS . 'test-module'));
		self::assertTrue(Nano_Loader::load('M_TestModule_Library_Class'));
		self::assertTrue(Nano_Loader::load('M_TestModule_Controller_Class'));
		self::assertTrue(Nano_Loader::load('M_TestModule_Model_Class'));
		self::assertTrue(Nano_Loader::load('M_TestModule_Plugin_Class'));
	}

}