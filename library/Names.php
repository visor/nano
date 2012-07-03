<?php

namespace Nano;

class Names {

	const NAMESPACE_NANO       = 'Nano';
	const NAMESPACE_APP        = 'App';
	const NAMESPACE_MODULE     = 'Module';

	const NAMESPACE_CONTROLLER = 'Controller';
	const NAMESPACE_HELPER     = 'Helper';
	const NAMESPACE_MODEL      = 'Model';
	const NAMESPACE_PLUGIN     = 'Plugin';

	const SUFFIX_ACTION        = 'Action';

	const LIBRARY_FOLDER       = 'library';

	const EXT = '.php';

	private static $separators = array('_', NS);

	private static $reservedNamespaces = array(
		self::NAMESPACE_CONTROLLER => 'controllers'
		, self::NAMESPACE_HELPER   => 'helpers'
		, self::NAMESPACE_MODEL    => 'models'
		, self::NAMESPACE_PLUGIN   => 'plugins'
	);

	/**
	 * Выполняет общее преобразование короткого имени в имя класса:
	 *  - Приводит переданное значение к CamelCase
	 *  - Удаляет знак "минус"
	 *  - Заменяет знак "слеш" на разделитель пространства имён
	 *
	 * @return string
	 * @param string $value
	 */
	public static function common($value) {
		$result = preg_replace('/\s+/', '', trim($value));
		$result = strToLower($result);
		$result = str_replace(array('-', '/'), array(' ', '. '), $result);
		$result = ucWords($result);
		$result = str_replace(array(' ', '.'), array('', NS), $result);
		return trim($result);
	}

	/**
	 * Преобразовывает короткое имя $shortName в имя класса контроллера приложения или модуля (если передан параметр $module)
	 *
	 * @return string
	 * @param string $shortName
	 * @param string|null $module
	 */
	public static function controllerClass($shortName, $module = null) {
		if ($module) {
			return self::moduleClass($module, $shortName, self::NAMESPACE_CONTROLLER);
		}
		return self::applicationClass($shortName, self::NAMESPACE_CONTROLLER);
	}

	/**
	 * Преобразовывает короткое имя $shortName в имя класса хелпера (приложения или модуля)
	 *
	 * @return string
	 * @param string $shortName
	 * @param string|null $module
	 */
	public static function helperClass($shortName, $module = null) {
		if ($module) {
			return self::moduleClass($module, $shortName, self::NAMESPACE_HELPER);
		}
		return self::applicationClass($shortName, self::NAMESPACE_HELPER);
	}

	/**
	 * Преобразовывает короткое имя $shortName в имя класса приложения
	 *
	 * @return string
	 * @param string $shortName
	 * @param string|null $namespace
	 */
	public static function applicationClass($shortName, $namespace = null) {
		return self::NAMESPACE_APP . NS . (null === $namespace ? '' : $namespace . NS) . self::common($shortName);
	}

	/**
	 * Преобразовывает короткое имя $shortName в имя класса модуля
	 *
	 * @return string
	 * @param string $module
	 * @param string $shortName
	 * @param string|null $namespace
	 */
	public static function moduleClass($module, $shortName, $namespace = null) {
		return self::NAMESPACE_MODULE . NS . self::common($module) . NS . (null === $namespace ? '' : $namespace . NS) . self::common($shortName);
	}

	/**
	 * Преобразовывает имя класса $className в имя файла фреймворка
	 *
	 * @return string
	 * @param string $className
	 */
	public static function nanoFile($className) {
		$fileName = subStr(trim($className, NS), strLen(self::NAMESPACE_NANO) + 1);
		$fileName = self::getFileName($fileName);
		return __DIR__ . DIRECTORY_SEPARATOR . $fileName;
	}

	/**
	 * Преобразовывает имя класса $className в имя файла приложения
	 *
	 * @return string
	 * @param string $className
	 */
	public static function applicationFile($className) {
		return self::classNameToFile(\Nano::app()->rootDir, self::removeNamespace($className, self::NAMESPACE_APP));
	}

	/**
	 * Преобразовывает имя класса модуля в имя файла модуля
	 *
	 * @return string
	 * @param string $className
	 *
	 * @throws \Application_Exception_ModuleNotFound
	 */
	public static function moduleFile($className) {
		$fileName = self::removeNamespace($className, self::NAMESPACE_MODULE);
		$module   = \Nano\Modules::namespaceToName(self::extractFirstNamespace($fileName));
		if (!\Nano::app()->modules->active($module)) {
			throw new \Application_Exception_ModuleNotFound($module);
		}

		$prefix   = \Nano::app()->modules->getPath($module);
		$fileName = self::removeNamespace($fileName);
		return self::classNameToFile($prefix, $fileName);
	}

	/**
	 * Преобразует имя класса $className в имя файла относительно корня приложения или модуля
	 * и добавляет к пути переданный префик $pathPrefix
	 *
	 * @return string
	 * @param string $pathPrefix
	 * @param string $className
	 */
	protected static function classNameToFile($pathPrefix, $className) {
		$folder   = self::getBaseFolderFromName($className);
		$result   = $pathPrefix;
		if (null === $folder) {
			$result  .= DS . self::LIBRARY_FOLDER;
			$fileName = $className;
		} else {
			$result  .= DS . $folder;
			$fileName = self::removeNamespace($className);
		}

		return $result . DS . self::getFileName($fileName);
	}

	/**
	 * Преобразовывает имя класса в имя файла (заменяет _ и \ на разделитель пути - DIRECTORY_SEPARATOR)
	 * и добавляет расширение
	 *
	 * @return string
	 * @param string $className
	 */
	protected static function getFileName($className) {
		return str_replace(self::$separators, DS, $className) . self::EXT;
	}

	/**
	 * Удаляет из имени класса $className префиксное пространство имём $nameSpace,
	 * или первое пронстранство имён, если параметр не передан.
	 *
	 * @return string
	 * @param string $className
	 * @param string $nameSpace
	 */
	protected static function removeNamespace($className, $nameSpace = null) {
		if (null === $nameSpace) {
			$nameSpace = self::extractFirstNamespace($className);
		}
		return subStr(lTrim($className, NS), strLen($nameSpace) + 1);
	}

	/**
	 * Возвращает имя служебной папки, в которой должен хранится класс
	 * или null, если класс является библиотечным
	 *
	 * @return null|string
	 * @param string $name
	 */
	protected static function getBaseFolderFromName($name) {
		$nameSpace = self::extractFirstNamespace($name);
		if (isSet(self::$reservedNamespaces[$nameSpace])) {
			return self::$reservedNamespaces[$nameSpace];
		}
		return null;
	}

	/**
	 * Возвращает первое пространство имём класса
	 *
	 * @return boolean
	 * @param string $className
	 */
	protected static function extractFirstNamespace($className) {
		$pos = strPos($className, NS, 1);
		if (false === $pos) {
			return null;
		}
		return lTrim(subStr($className, 0, $pos), NS);
	}

}