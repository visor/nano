<?php

interface Orm_DataSource {

	/**
	 * @param array $config
	 */
	public function __construct(array $config);

	/**
	 * @param string $typeName
	 * @return boolean
	 */
	public function typeSupported($typeName);

	/**
	 * @return Orm_Type
	 * @param string $typeName
	 */
	public function type($typeName);

	/**
	 * @return boolean
	 * @param Orm_Resource $resource
	 * @param stdClass $data
	 */
	public function insert(Orm_Resource $resource, stdClass $data);

	/**
	 * @return boolean
	 * @param Orm_Resource $resource
	 * @param stdClass $data
	 * @param Orm_Criteria $where
	 */
	public function update(Orm_Resource $resource, stdClass $data, Orm_Criteria $where);

	/**
	 * @return boolean
	 * @param Orm_Resource $resource
	 * @param Orm_Criteria|null $where
	 */
	public function delete(Orm_Resource $resource, Orm_Criteria $where = null);

	/**
	 * @return array|false
	 * @param Orm_Resource $resource
	 * @param Orm_Criteria $criteria
	 */
	public function get(Orm_Resource $resource, Orm_Criteria $criteria);

	/**
	 * @return array|false
	 * @param Orm_Resource $resource
	 * @param Orm_Criteria $criteria
	 * @param Orm_FindOptions $findOptions
	 */
	public function find(Orm_Resource $resource, Orm_Criteria $criteria = null, Orm_FindOptions $findOptions = null);

	/**
	 * @return mixed
	 * @param Orm_Resource $resource
	 * @param Orm_Criteria $criteria
	 */
	public function criteriaToExpression(Orm_Resource $resource, Orm_Criteria $criteria);

	/**
	 * @return string
	 * @param string $name
	 */
	public function quoteName($name);

}