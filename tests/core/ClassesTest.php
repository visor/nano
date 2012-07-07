<?php

/**
 * @group core
 * @group names
 */
class Core_ClassesTest extends \Nano\TestUtils\TestCase {

	public function testIsNanoShouldReturnTrueWhenClassUsesTopNanoNamespace() {
		self::assertTrue(\Nano\Util\Classes::isNanoClass('Nano\\Util\\Classes'));
	}

	public function testIsNanoShouldIgnoreFirstBackSlash() {
		self::assertTrue(\Nano\Util\Classes::isNanoClass('\Nano\\Names'));
	}

	public function testIsNanoShouldReturnFalseWhenNanoNamespaceNotTop() {
		self::assertFalse(\Nano\Util\Classes::isNanoClass('Some\Nano\\Classes'));
	}

	public function testIsNanoShouldReturnFalseWhenNoTopNanoNamespace() {
		self::assertFalse(\Nano\Util\Classes::isNanoClass('App\\LibraryClass'));
	}

	public function testIsApplicationClassShouldReturnTrueWhenClassUsesTopAppNamespace() {
		self::assertTrue(\Nano\Util\Classes::isApplicationClass('App\\Classes'));
	}

	public function testIsApplicationClassShouldIgnoreFirstBackSlash() {
		self::assertTrue(\Nano\Util\Classes::isApplicationClass('\\App\\Controller\\Index'));
		self::assertTrue(\Nano\Util\Classes::isApplicationClass('\\App\\LibraryClass'));
	}

	public function testIsApplicationClassShouldReturnFalseWhenAppNamespaceNotTop() {
		self::assertFalse(\Nano\Util\Classes::isApplicationClass('Some\\App\\Class'));
	}

	public function testIsApplicationClassShouldReturnFalseWhenNoTopAppNamespace() {
		self::assertFalse(\Nano\Util\Classes::isApplicationClass('Not\\AppClass'));
	}

	public function testIsModuleClassShouldReturnTrueWhenClassUsesTopModuleNamespace() {
		self::assertTrue(\Nano\Util\Classes::isModuleClass('Module\\Classes'));
	}

	public function testIsModuleClassShouldIgnoreFirstBackSlash() {
		self::assertTrue(\Nano\Util\Classes::isModuleClass('\\Module\\Controller\\Index'));
		self::assertTrue(\Nano\Util\Classes::isModuleClass('\\Module\\LibraryClass'));
	}

	public function testIsModuleClassShouldReturnFalseWhenModuleNamespaceNotTop() {
		self::assertFalse(\Nano\Util\Classes::isModuleClass('Some\\Module\\Class'));
	}

	public function testIsModuleClassShouldReturnFalseWhenNoTopModuleNamespace() {
		self::assertFalse(\Nano\Util\Classes::isModuleClass('Not\\AppClass'));
	}

}