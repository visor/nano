<?php

class TestUtils_Mixin_App extends TestUtils_Mixin {

	protected $backup = null;

	public function backup() {
		if (null === $this->backup) {
			$this->backup = Nano::app();
		}
		Nano::setApplication(null);
	}

	public function restore() {
		if (null === $this->backup) {
			return;
		}
		Nano::setApplication(null);
		Nano::setApplication($this->backup);
		$this->backup = null;
	}

}