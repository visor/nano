<?php

class Mapper_Library_OrmExampleWizard extends Orm_Mapper {

	/**
	 * @var string
	 */
	protected $modelClass = 'Library_OrmExampleWizard';

	/**
	 * @return array
	 */
	protected function getMeta() {
		return array(
			'name'          => 'wizard'
			, 'source'      => 'test'
			, 'fields'      => array(
				'id'     => array(
					'type' => 'integer'
				)
				, 'firstName' => array(
					'type'   => 'string'
					, 'null' => false
				)
				, 'lastName' => array(
					'type'   => 'string'
					, 'null' => false
				)
				, 'role' => array(
					'type'      => 'string'
					, 'null'    => false
					, 'default' => 'student'
				)
				, 'addressId' => array(
					'type'   => 'integer'
					, 'null' => false
				)
			)
			, 'incremental' => 'id'
			, 'identity'    => array('id')
			, 'relations'   => array(
				'address' => array(
					'type'     => self::RELATION_TYPE_BELONGS_TO
					, 'model'  => 'Mapper_Library_Orm_Example_Address'
					, 'fields' => array('addressId')
				)
			)
		);
	}

}