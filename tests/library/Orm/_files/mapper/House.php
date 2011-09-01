<?php

class Mapper_LibraryOrmExampleHouse extends Orm_Mapper {

	/**
	 * @return array
	 */
	protected function getMeta() {
		return array(
			'name'          => 'house'
			, 'source'      => 'test'
			, 'fields'      => array(
				'id'     => array(
					'type' => 'integer'
				)
				, 'name' => array(
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