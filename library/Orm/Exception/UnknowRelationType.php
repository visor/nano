<?php

class Orm_Exception_UnknownRelationType extends Orm_Exception {

	public function __construct($name) {
		parent::__construct('Relation type ' . $name . ' is not supported');
	}

}