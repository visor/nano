<?php

class SettingController extends Nano_C {

	public $layout = 'control-panel';

	public function indexAction() {
		$this->name      = $this->p('category', Setting_Category::first()->name);
		$this->current   = Setting_Category::get($this->name);
		$this->settings  = Setting::getCategory($this->name);
		$this->pageTitle = 'Settings: ' . $this->current->title;
	}

	public function saveAction() {
		$settings = isset($_POST['setting']) ? (array)$_POST['setting'] : array();
		Nano_Log::message(var_export($settings, true));
		if (empty($settings)) {
			$this->redirect('/cp/settings');
		}
		foreach ($settings as $category => $options) {
			foreach ($options as $name => $value) {
				Setting::set($category, $name, $value);
			}
		}
		$this->redirect('/cp/settings/' . $category);
	}
}