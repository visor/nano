<?php

class TestController extends Nano_C {

	public function indexAction() {
		$this->runContextAction();
	}

	public function indexDefaultAction() {
	}

	public function indexXmlAction() {
		$this->xmlVariable = 'some-value-for-xml';
	}

	public function indexRssAction() {
		$this->rssVariable = 'some-value-for-rss';
	}

	public function testAction() {
	}

	public function testVarAction() {
		$this->title = 'Some title';
		$this->array = array(
			  '01' => 'foo'
			, '03' => 'bar'
		);
	}

}