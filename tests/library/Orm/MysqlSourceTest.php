<?php

require_once __DIR__ . '/TestPdoSource.php';

/**
 * @group library
 * @group orm
 * @group orm-source
 */
class Library_Orm_MysqlSourceTest extends Library_Orm_TestPdoSource {

	/**
	 * @var Orm_DataSource_Pdo_Mysql
	 */
	protected $source;

	/**
	 * @var Orm_Mapper
	 */
	protected $mapper;

	/**
	 * @return Orm_DataSource_Pdo
	 */
	protected function createDataSource() {
		$result = new Orm_DataSource_Pdo_Mysql(array());
		$result->usePdo(Nano::db());
		return $result;
	}

}