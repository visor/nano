<?php

abstract class Orm_Mapper {

	/**
	 * @var string
	 */
	protected $modelClass;

	/**
	 * @var Orm_Resource
	 */
	protected $resource;

	/**
	 * @return Orm_Resource
	 */
	public function getResource() {
		if (null === $this->resource) {
			$this->resource = new Orm_Resource($this->getMeta());
		}
		return $this->resource;
	}

	/**
	 * @return boolean
	 * @param Orm_Model $model
	 */
	public function insert(Orm_Model $model) {
		if (false === $model->changed()) {
			return true;
		}
		return $this->dataSource()->insert($this->getResource(), $model->getData());
	}

	/**
	 * @return boolean
	 * @param Orm_Model $model
	 */
	public function update(Orm_Model $model) {
		if (false === $model->changed()) {
			return true;
		}
		return $this->dataSource()->update($this->getResource(), $model->getData(), $this->getIdentifyCriteria($model));
	}

	/**
	 * @return boolean
	 * @param Orm_Model $model
	 */
	public function delete(Orm_Model $model) {
		if ($model->isNew()) {
			return false;
		}
		return $this->dataSource()->delete($this->getResource(), $this->getIdentifyCriteria($model));
	}

	/**
	 * @return Orm_Model
	 * @param mixed $identity
	 */
	public function get($identity) {
		$values   = $this->paramsToArray(func_get_args());
		$criteria = Orm::criteria();
		foreach ($this->getResource()->identity() as $index => $fieldName) {
			$criteria->equals($fieldName, $values[$index]);
		}
		return $this->load($this->dataSource()->get($this->getResource(), $criteria));
	}

	/**
	 * @return array|boolean
	 * @param null|Orm_Criteria $criteria
	 * @param null|Orm_FindOptions $findOptions
	 */
	public function find(Orm_Criteria $criteria = null, Orm_FindOptions $findOptions = null) {
		$elements = $this->dataSource()->find($this->getResource(), $criteria, $findOptions);
		if (false === $elements) {
			return false;
		}
		return new Orm_Collection($this, $elements);
	}

	/**
	 * @return void
	 * @param stdClass $modelData
	 * @param array $sourceData
	 */
	public function mapToModel(stdClass $modelData, array $sourceData) {
		foreach ($this->getResource()->fields() as $name => $meta) {
			if (isSet($sourceData[$name])) {
				$value = $this->getResource()->castToModel($name, $sourceData[$name]);
			} else {
				$value = $this->getResource()->defaultValue($name);
			}
			$modelData->$name = $value;
		}
	}

	/**
	 * @return array
	 * @param stdClass $modelData
	 */
	public function mapToDataSource(stdClass $modelData) {
		$result = array();
		foreach ($this->getResource()->fields() as $name => $meta) {
			$result[$name] = $this->getResource()->castToDataSource($name, $modelData->$name);
		}
		return $result;
	}

	/**
	 * @return Orm_Model
	 * @param array $sourceData
	 */
	public function load(array $sourceData) {
		return new $this->modelClass($sourceData, true);
	}

	/**
	 * @return array
	 */
	abstract protected function getMeta();

	/**
	 * @return Orm_DataSource
	 */
	protected function dataSource() {
		return Orm::instance()->source($this->getResource()->sourceName());
	}

	protected function paramsToArray(array $parameters) {
		if (0 === count($parameters)) {
			return array();
		}
		if (isSet($parameters[0]) && is_array($parameters[0])) {
			return array_values($parameters[0]);
		}
		return $parameters;
	}

	/**
	 * @return Orm_Criteria
	 * @param Orm_Model $model
	 */
	protected function getIdentifyCriteria(Orm_Model $model) {
		$result = Orm::criteria();
		foreach ($this->getResource()->identity() as $fieldName) {
			$result->equals($fieldName, $model->__get($fieldName));
		}
		return $result;
	}

}