<?php

class ActiveRecord_Exception_UnknownField extends ActiveRecord_Exception {

	public function __construct($name, $instance) {
		parent::__construct('Unknown field "' . $name . '" in class ' . get_class($instance));
	}

}
