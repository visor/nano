<?php

/**
 * @property int $id
 * @property string $text
 */
class ActiveRecordBasic extends ActiveRecord {

	const TABLE_NAME      = 'test';

	protected $primaryKey    = array('id');
	protected $autoIncrement = true;
	protected $fields        = array('id', 'text');

}