<?php

class Orm_Type_Mongo_Identify implements Orm_Type {

	/**
	 * @return mixed
	 * @param MongoId $value
	 */
	public function castToModel($value) {
		return $value->__toString();
	}

	/**
	 * @return mixed
	 * @param mixed $value
	 */
	public function castToDataSource($value) {
		return new MongoId($value);
	}

}