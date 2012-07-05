<?php

/**
 * @group core
 * @group names
 */
class Core_ClassesTest extends TestUtils_TestCase {

	public function testIsNanoShouldReturnTrueWhenClassUsesTopNanoNamespace() {
		self::assertTrue(\Nano\Classes::isNanoClass('Nano\\Classes'));
	}

	public function testIsNanoShouldIgnoreFirstBackSlash() {
		self::assertTrue(\Nano\Classes::isNanoClass('\Nano\\Names'));
	}

	public function testIsNanoShouldReturnFalseWhenNanoNamespaceNotTop() {
		self::assertFalse(\Nano\Classes::isNanoClass('Some\Nano\\Classes'));
	}

	public function testIsNanoShouldReturnFalseWhenNoTopNanoNamespace() {
		self::assertFalse(\Nano\Classes::isNanoClass('App\\LibraryClass'));
	}

	public function testIsApplicationClassShouldReturnTrueWhenClassUsesTopAppNamespace() {
		self::assertTrue(\Nano\Classes::isApplicationClass('App\\Classes'));
	}

	public function testIsApplicationClassShouldIgnoreFirstBackSlash() {
		self::assertTrue(\Nano\Classes::isApplicationClass('\\App\\Controller\\Index'));
		self::assertTrue(\Nano\Classes::isApplicationClass('\\App\\LibraryClass'));
	}

	public function testIsApplicationClassShouldReturnFalseWhenAppNamespaceNotTop() {
		self::assertFalse(\Nano\Classes::isApplicationClass('Some\\App\\Class'));
	}

	public function testIsApplicationClassShouldReturnFalseWhenNoTopAppNamespace() {
		self::assertFalse(\Nano\Classes::isApplicationClass('Not\\AppClass'));
	}

	public function testIsModuleClassShouldReturnTrueWhenClassUsesTopModuleNamespace() {
		self::assertTrue(\Nano\Classes::isModuleClass('Module\\Classes'));
	}

	public function testIsModuleClassShouldIgnoreFirstBackSlash() {
		self::assertTrue(\Nano\Classes::isModuleClass('\\Module\\Controller\\Index'));
		self::assertTrue(\Nano\Classes::isModuleClass('\\Module\\LibraryClass'));
	}

	public function testIsModuleClassShouldReturnFalseWhenModuleNamespaceNotTop() {
		self::assertFalse(\Nano\Classes::isModuleClass('Some\\Module\\Class'));
	}

	public function testIsModuleClassShouldReturnFalseWhenNoTopModuleNamespace() {
		self::assertFalse(\Nano\Classes::isModuleClass('Not\\AppClass'));
	}

}