<?php

/**
 * @property int $id
 * @property string $text
 */
class ActiveRecordNoAutoIncrement extends ActiveRecord {

	const TABLE_NAME      = 'test';

	protected $primaryKey = array('id');
	protected $fields     = array('id', 'text');

}