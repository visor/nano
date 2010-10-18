<?php

class ActiveRecordNoPrimaryKey extends ActiveRecord {

	const TABLE_NAME         = 'test';

	protected $primaryKey    = null;
	protected $autoIncrement = true;
	protected $fields        = array('id', 'text');

}
