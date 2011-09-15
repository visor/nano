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
		if (empty($settings)) {
			$this->helper->ui()->addMessage('warning', 'Settings not found');
			$this->redirect('/cp/settings');
		}
		foreach ($settings as $category => $options) {
			foreach ($options as $name => $value) {
				Setting::set($category, $name, $value);
			}
		}
		$this->helper->ui()->addMessage('success', 'Settings have been saved successfully');
		$this->redirect('/cp/settings/' . $category);
	}
}