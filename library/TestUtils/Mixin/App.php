<?php

class TestUtils_Mixin_App extends TestUtils_Mixin {

	protected $app = null;

	public function backup() {
		$this->app = Nano::app();
		Nano::setApplication(null);
	}

	public function restore() {
		Nano::setApplication(null);
		Nano::setApplication($this->app);
	}

}