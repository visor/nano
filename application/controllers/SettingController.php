<?php

class SettingController extends Nano_C {

	public $layout = 'control-panel';

	public function indexAction() {
		$this->pageTitle = 'Settings';
		$this->category  = $this->p('category'/*, Settings_Category::first(); */);
	}

}