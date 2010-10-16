<?php

class ActiveRecordNoName extends ActiveRecord {

	protected $primaryKey = array('id');

	protected $autoIncrement = true;

	protected $fields = array('id', 'text');

}
