<?php

class Mapper_Library_Orm_Example_Address extends Orm_Mapper {

	/**
	 * @var string
	 */
	protected $modelClass = 'Library_Orm_Example_Address';

	/**
	 * @return array
	 */
	protected function getMeta() {
		return array(
			'name'          => 'address'
			, 'source'      => 'test'
			, 'fields'      => array(
				'id'         => array(
					'type'       => 'integer'
					, 'readonly' => true
				)
				, 'location' => array(
					'type'   => 'string'
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