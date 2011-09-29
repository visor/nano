<?php

namespace TestNamespace;

class Mapper_Namespaced extends \Orm_Mapper {

	/**
	 * @var string
	 */
	protected $modelClass = 'Library_Orm_Example_Namespaced';

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