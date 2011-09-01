<?php

abstract class Orm_DataSource_Abstract implements Orm_DataSource {

	/**
	 * @var Orm_Type[]
	 */
	protected $typeInstances = array();

	/**
	 * @var string[]
	 */
	protected $supportedTypes = array();

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @return boolean
	 * @param string $typeName
	 */
	public function typeSupported($typeName) {
		return isSet($this->supportedTypes[$typeName]);
	}

	/**
	 * @return Orm_Type
	 * @param string $typeName
	 * @throws Orm_Exception_UnsupportedType
	 */
	public function type($typeName) {
		if (!$this->typeSupported($typeName)) {
			throw new Orm_Exception_UnsupportedType($typeName);
		}
		if (isSet($this->typeInstances[$typeName])) {
			return $this->typeInstances[$typeName];
		}
		return ($this->typeInstances[$typeName] = $this->createTypeInstance($typeName));
	}

	/**
	 * @param array $config
	 */
	public function __construct(array $config) {
		$this->config = $config;
	}

	/**
	 * @return boolean
	 * @param stdClass $data
	 */
	protected function isEmptyObject(stdClass $data) {
		$values = get_object_vars($data);
		return empty($values);
	}

	/**
	 * @param string $typeName
	 * @return Orm_Type
	 */
	protected function createTypeInstance($typeName) {
		$className = 'Orm_Type_' . $this->supportedTypes[$typeName];
		return new $className;
	}

	/**
	 * @return Orm_DataSource_Abstract
	 * @param string $name
	 * @param string $class
	 */
	protected function addType($name, $class) {
		$this->supportedTypes[$name] = $class;
		return $this;
	}

}