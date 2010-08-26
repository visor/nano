<?php

class Site_Sortable_Form extends Nano_Form {

	public function __construct() {
		parent::__construct(array('title'));
		$this
			->addValidator('title', new Nano_Validator_Required(), 'sortable-title-required')
			->setMode(self::MODE_VALIDATE_ALL)
		;
	}

}