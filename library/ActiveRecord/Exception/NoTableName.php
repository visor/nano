<?php

class ActiveRecord_Exception_NoTableName extends ActiveRecord_Exception {

	public function __construct($instance) {
		parent::__construct('Table name is not specified for class ' . get_class($instance));
	}

}
