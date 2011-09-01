<?php

class Mapper_Library_OrmExampleWizard extends Orm_Mapper {

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
			, 'hasMany'     => array()
			, 'belongsTo'   => array()
		);
	}

}