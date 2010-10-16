<?php

class ActiveRecordNoFields extends ActiveRecord {

	const TABLE_NAME = 'test';

	protected $primaryKey =  array('id');

	protected $autoIncrement = true;

}
