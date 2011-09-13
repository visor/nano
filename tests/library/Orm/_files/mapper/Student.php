<?php

class Mapper_Library_Orm_Example_Student extends Orm_Mapper {

	/**
	 * @return array
	 */
	protected function getMeta() {
		return array(
			'name'          => 'student'
			, 'source'      => 'test'
			, 'fields'      => array(
				'wizardId' => array(
					'type' => 'integer'
					, 'null' => false
				)
				, 'houseId' => array(
					'type'   => 'integer'
					, 'null' => false
				)
				, 'isDAMembmer' => array(
					'type'      => 'boolean'
					, 'null'    => false
					, 'default' => false
				)
			)
			, 'incremental' => false
			, 'identity'    => array('wizardId')
			, 'hasMany'     => array()
			, 'belongsTo'   => array()
		);
	}

}