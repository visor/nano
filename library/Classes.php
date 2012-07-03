<?php

namespace Nano;

class Classes {

	/**
	 * Проверяет, является ли $className классом фреймворка
	 *
	 * @return boolean
	 * @param string $className
	 */
	public static function isNanoClass($className) {
		return self::isNamespaceClass($className, Names::NAMESPACE_NANO);
	}

	/**
	 * Проверяет, является ли $className классом приложения
	 *
	 * @return boolean
	 * @param string $className
	 */
	public static function isApplicationClass($className) {
		return self::isNamespaceClass($className, Names::NAMESPACE_APP);
	}

	/**
	 * Проверяет, является ли $className классом модуля приложения
	 *
	 * @return boolean
	 * @param string $className
	 */
	public static function isModuleClass($className) {
		return self::isNamespaceClass($className, Names::NAMESPACE_MODULE);
	}

	/**
	 * @return boolean
	 * @param string $className
	 * @param string $namespace
	 */
	protected static function isNamespaceClass($className, $namespace) {
		return 0 === strPos(lTrim($className, NS), $namespace . NS);
	}

}