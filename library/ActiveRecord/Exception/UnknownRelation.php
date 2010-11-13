<?php

class ActiveRecord_Exception_UnknownRelation extends ActiveRecord_Exception {

	public function __construct($name, $instance) {
		parent::__construct('Unknown relation "' . $name . '" in class ' . get_class($instance));
	}

}
