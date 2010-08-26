<?php

class Site_Article_Form extends Nano_Form {

	public function __construct() {
		parent::__construct(array(
			  'title'
			, 'announce'
			, 'body'
			, 'published'
			, 'active'
		));
		$this
			->addValidator('title', new Nano_Validator_Required(), 'article-title-required')
			->addValidator('body', new Nano_Validator_Required(), 'article-body-required')
			->setMode(self::MODE_VALIDATE_ALL)
		;
	}

}