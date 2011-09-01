<?php

class Orm {

	const MAPPER_PREFIX = 'Mapper';
	const MODEL_PREFIX  = 'Model';

	/**
	 * @var Orm
	 */
	private static $instance = null;

	/**
	 * @var Orm_Mapper[]
	 */
	private static $mappers = array();

	/**
	 * @var Orm_DataSource[]
	 */
	private $dataSources = array();

	/**
	 * @return Orm
	 */
	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return Orm_Mapper
	 * @param string $model
	 */
	public static function mapper($model) {
		$key = strToLower($model);

		if (isSet(self::$mappers[$key])) {
			return self::$mappers[$key];
		}

		$class = self::mapperClass($model);
		return (self::$mappers[$key] = new $class);
	}

	/**
	 * @return Orm_Criteria
	 */
	public static function criteria() {
		return Orm_Criteria::create();
	}

	/**
	 * @return Orm_FindOptions
	 */
	public static function findOptions() {
		return Orm_FindOptions::create();
	}

	/**
	 * @return Orm_DataSource[]
	 * @static
	 */
	public static function backup() {
		$result = self::instance()->dataSources;
		self::$instance = null;
		return $result;
	}

	/**
	 * @return boolean
	 * @param Orm_DataSource[] $dataSources
	 */
	public static function restore(array $dataSources) {
		try {
			self::$instance = null;
			foreach ($dataSources as $key => $dataSource) {
				self::instance()->addSource($key, $dataSource);
			}
			return true;
		} catch (Exception $e) {
			self::$instance = null;
			return false;
		}
	}

	/**
	 * @return Orm
	 * @param string $key
	 * @param Orm_DataSource $source
	 */
	public function addSource($key, Orm_DataSource $source) {
		$this->dataSources[$key] = $source;
		return $this;
	}

	/**
	 * @return Orm_DataSource
	 * @param string $key
	 * @throws Orm_Exception_InvalidDataSource
	 */
	public function source($key) {
		if (isSet($this->dataSources[$key])) {
			return $this->dataSources[$key];
		}
		throw new Orm_Exception_InvalidDataSource($key);
	}

	/**
	 * @return string
	 * @param stirng $model
	 */
	protected static function mapperClass($model) {
		return self::MAPPER_PREFIX . '_' . $model;
	}

}