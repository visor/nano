<?php

class ActiveRecord_Exception_NoFields extends ActiveRecord_Exception {

	public function __construct($instance) {
		parent::__construct('No fields defined for class ' . get_class($instance));
	}

}
