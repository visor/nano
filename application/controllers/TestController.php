<?php

class TestController extends Nano_C {

	public function indexAction() {
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