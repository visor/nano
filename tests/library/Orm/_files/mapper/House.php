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
			, 'relations'   => array(
				'wizards' => array(
					'type'     => self::RELATION_TYPE_HAS_MANY
					, 'model'  => 'Library_OrmExampleWizard'
					, 'fields' => array('houseId')
				)
			)
		);
	}

}