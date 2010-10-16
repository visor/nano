<?php

class ActiveRecord_Exception_AutoIncrementNotDefined extends ActiveRecord_Exception {

	public function __construct($instance) {
		parent::__construct('Autoincrement flag not defined for class ' . get_class($instance));
	}

}
