<?php

class ActiveRecord_Exception_NoPrimaryKey extends ActiveRecord_Exception {

	public function __construct($instance) {
		parent::__construct('Primary key is not specified for class ' . get_class($instance));
	}

}
