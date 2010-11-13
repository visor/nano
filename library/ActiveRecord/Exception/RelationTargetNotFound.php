<?php

class ActiveRecord_Exception_RelationTargetNotFound extends ActiveRecord_Exception {

	public function __construct($relation, $instance) {
		parent::__construct('Required relation target ' . $relation[ActiveRecord::REL_CLASS] . ' not found for ' . get_class($instance));
	}

}
