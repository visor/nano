<?php

class Application_Exception_InvalidModuleNamespace extends Application_Exception {

	public function __construct($name) {
		parent::__construct('Given namespace "' . $name . '" is not valid module namespace');
	}

}